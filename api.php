<?php
header('Content-Type: application/json');
require 'config.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

function json_response($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

$stations = ['Registrar', 'Cashier', 'Document Releasing'];

// Average service time is calculated from the most recent 10 completed
// clients at each station. If there is not enough history yet, use a
// 5-minute default so the client still gets an estimate.
function get_average_service_seconds($pdo, $station) {
    $stmt = $pdo->prepare(
        "SELECT AVG(TIMESTAMPDIFF(SECOND, called_at, completed_at))
         FROM tickets
         WHERE station = ?
           AND status = 'completed'
           AND called_at IS NOT NULL
           AND completed_at IS NOT NULL
           AND completed_at >= called_at
         ORDER BY completed_at DESC
         LIMIT 10"
    );
    $stmt->execute([$station]);
    $avg = $stmt->fetchColumn();
    return ($avg !== null && (float)$avg > 0) ? (int)round((float)$avg) : 300;
}

function format_wait_time($seconds) {
    $minutes = (int)ceil(max(0, $seconds) / 60);
    if ($minutes < 1) return 'Less than 1 min';
    if ($minutes === 1) return '1 min';
    if ($minutes < 60) return $minutes . ' mins';
    $hours = intdiv($minutes, 60);
    $mins = $minutes % 60;
    return $mins ? $hours . ' hr ' . $mins . ' min' : $hours . ' hr';
}

try {
    if ($action === 'take_ticket') {
        $station = $_POST['station'] ?? '';
        if (!in_array($station, $stations, true)) {
            json_response(['success'=>false, 'message'=>'Invalid station.'], 400);
        }

        $prefixes = [
            'Registrar' => 'R',
            'Cashier' => 'C',
            'Document Releasing' => 'D'
        ];
        $prefix = $prefixes[$station];

        // Number tickets separately per station.
        $stmt = $pdo->prepare(
            "SELECT COALESCE(MAX(CAST(SUBSTRING(ticket_number, 2) AS UNSIGNED)), 0) + 1
             FROM tickets WHERE station = ?"
        );
        $stmt->execute([$station]);
        $next = (int)$stmt->fetchColumn();

        $ticket = $prefix . str_pad($next, 3, '0', STR_PAD_LEFT);

        $stmt = $pdo->prepare(
            "INSERT INTO tickets (ticket_number, station, status) VALUES (?, ?, 'waiting')"
        );
        $stmt->execute([$ticket, $station]);

        json_response([
            'success'=>true,
            'ticket_number'=>$ticket,
            'station'=>$station,
            'message'=>"Your ticket is $ticket."
        ]);
    }

    if ($action === 'queue_status') {
        $ticket = $_GET['ticket'] ?? '';
        if ($ticket === '') json_response(['success'=>false, 'message'=>'Ticket required.'], 400);

        $stmt = $pdo->prepare(
            "SELECT id, ticket_number, station, status, created_at, called_at
             FROM tickets WHERE ticket_number = ? ORDER BY id DESC LIMIT 1"
        );
        $stmt->execute([$ticket]);
        $row = $stmt->fetch();

        if (!$row) json_response(['success'=>false, 'message'=>'Ticket not found.'], 404);

        if ($row['status'] === 'waiting') {
            $stmt = $pdo->prepare(
                "SELECT COUNT(*) FROM tickets
                 WHERE station = ? AND status = 'waiting' AND id < ?"
            );
            $stmt->execute([$row['station'], $row['id']]);
            $ahead = (int)$stmt->fetchColumn();
        } else {
            $ahead = 0;
        }

        $position = ($row['status'] === 'waiting') ? $ahead + 1 : 0;
        $avgSeconds = get_average_service_seconds($pdo, $row['station']);
        $estimatedSeconds = $position > 0 ? $position * $avgSeconds : 0;

        $stmt = $pdo->prepare(
            "SELECT ticket_number FROM tickets
             WHERE station = ? AND status = 'serving'
             ORDER BY called_at DESC, id DESC LIMIT 1"
        );
        $stmt->execute([$row['station']]);
        $serving = $stmt->fetchColumn() ?: null;

        json_response([
            'success'=>true,
            'ticket'=>$row['ticket_number'],
            'station'=>$row['station'],
            'status'=>$row['status'],
            'ahead'=>$ahead,
            'position'=>$position,
            'serving'=>$serving,
            'average_service_seconds'=>$avgSeconds,
            'average_service_minutes'=>round($avgSeconds / 60, 1),
            'estimated_seconds'=>$estimatedSeconds,
            'estimated_minutes'=>$estimatedSeconds ? (int)ceil($estimatedSeconds / 60) : 0,
            'estimated_time'=>$estimatedSeconds ? format_wait_time($estimatedSeconds) : 'Now',
            'notify'=>($row['status'] === 'waiting' && $ahead <= 4)
        ]);
    }

    if ($action === 'station_estimates') {
        $result = [];
        foreach ($stations as $station) {
            $stmt = $pdo->prepare(
                "SELECT COUNT(*) FROM tickets WHERE station = ? AND status = 'waiting'"
            );
            $stmt->execute([$station]);
            $waiting = (int)$stmt->fetchColumn();

            $avgSeconds = get_average_service_seconds($pdo, $station);
            // A new client would be position waiting + 1.
            $position = $waiting + 1;
            $estimatedSeconds = $position * $avgSeconds;

            $result[$station] = [
                'waiting' => $waiting,
                'position' => $position,
                'average_service_seconds' => $avgSeconds,
                'average_service_minutes' => round($avgSeconds / 60, 1),
                'estimated_seconds' => $estimatedSeconds,
                'estimated_minutes' => (int)ceil($estimatedSeconds / 60),
                'estimated_time' => format_wait_time($estimatedSeconds)
            ];
        }
        json_response(['success'=>true, 'stations'=>$result]);
    }

    if ($action === 'dashboard') {
        $result = [];
        foreach ($stations as $station) {
            $stmt = $pdo->prepare(
                "SELECT ticket_number FROM tickets
                 WHERE station = ? AND status = 'serving'
                 ORDER BY called_at DESC, id DESC LIMIT 1"
            );
            $stmt->execute([$station]);
            $serving = $stmt->fetchColumn() ?: '-';

            $stmt = $pdo->prepare(
                "SELECT COUNT(*) FROM tickets WHERE station = ? AND status = 'waiting'"
            );
            $stmt->execute([$station]);
            $waiting = (int)$stmt->fetchColumn();

            $result[$station] = [
                'serving'=>$serving,
                'waiting'=>$waiting
            ];
        }

        $stmt = $pdo->query(
            "SELECT ticket_number, station, status, called_at
             FROM tickets WHERE status IN ('serving')
             ORDER BY called_at DESC, id DESC LIMIT 10"
        );

        json_response(['success'=>true, 'stations'=>$result, 'current_calls'=>$stmt->fetchAll()]);
    }

    if ($action === 'next') {
        $station = $_POST['station'] ?? '';
        if (!in_array($station, $stations, true)) {
            json_response(['success'=>false, 'message'=>'Invalid station.'], 400);
        }

        // Finish any currently serving ticket at this station.
        $stmt = $pdo->prepare(
            "UPDATE tickets SET status='completed', completed_at=NOW()
             WHERE station=? AND status='serving'"
        );
        $stmt->execute([$station]);

        // Take the oldest waiting ticket.
        $stmt = $pdo->prepare(
            "SELECT id, ticket_number FROM tickets
             WHERE station=? AND status='waiting'
             ORDER BY id ASC LIMIT 1"
        );
        $stmt->execute([$station]);
        $next = $stmt->fetch();

        if (!$next) {
            json_response(['success'=>false, 'message'=>'No waiting clients at this station.']);
        }

        $stmt = $pdo->prepare(
            "UPDATE tickets SET status='serving', called_at=NOW() WHERE id=?"
        );
        $stmt->execute([$next['id']]);

        json_response(['success'=>true, 'ticket'=>$next['ticket_number'], 'station'=>$station]);
    }

    if ($action === 'reset') {
        $station = trim($_POST['station'] ?? '');
        if (!in_array($station, $stations, true)) {
            json_response(['success'=>false, 'message'=>'Invalid station.'], 400);
        }

        // Delete every ticket belonging to this station. Because ticket numbers
        // are generated from MAX(ticket_number), removing the station's records
        // makes the next ticket start again at 001.
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("DELETE FROM tickets WHERE station = ?");
            $stmt->execute([$station]);
            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }

        json_response([
            'success'=>true,
            'station'=>$station,
            'message'=>"$station queue has been reset. Next ticket will be 001."
        ]);
    }

    if ($action === 'complete') {
        $station = $_POST['station'] ?? '';
        $stmt = $pdo->prepare(
            "UPDATE tickets SET status='completed', completed_at=NOW()
             WHERE station=? AND status='serving'"
        );
        $stmt->execute([$station]);
        json_response(['success'=>true]);
    }

    if ($action === 'skip') {
        $station = $_POST['station'] ?? '';
        $stmt = $pdo->prepare(
            "UPDATE tickets SET status='skipped', completed_at=NOW()
             WHERE station=? AND status='serving'"
        );
        $stmt->execute([$station]);
        json_response(['success'=>true]);
    }

    json_response(['success'=>false, 'message'=>'Unknown action.'], 400);

} catch (Throwable $e) {
    json_response(['success'=>false, 'message'=>$e->getMessage()], 500);
}
?>
