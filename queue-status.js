const statusContent = document.getElementById('statusContent');
const leaveQueue = document.getElementById('leaveQueue');
const params = new URLSearchParams(window.location.search);
let currentTicket = params.get('ticket') || localStorage.getItem('qease_ticket');
let lastNotified = localStorage.getItem('qease_last_notified') || '';

if (currentTicket) {
    localStorage.setItem('qease_ticket', currentTicket);
    checkQueue();
} else {
    showNoTicket();
}

leaveQueue.addEventListener('click', () => {
    localStorage.removeItem('qease_ticket');
    localStorage.removeItem('qease_last_notified');
    window.location.href = 'index.php';
});

async function checkQueue() {
    if (!currentTicket) return;

    try {
        const response = await fetch(`api.php?action=queue_status&ticket=${encodeURIComponent(currentTicket)}&_=${Date.now()}`);
        const data = await response.json();

        if (!data.success) {
            if (response.status === 404) {
                localStorage.removeItem('qease_ticket');
                currentTicket = null;
                showNoTicket('Your queue ticket is no longer active.');
            } else {
                showError(data.message || 'Unable to load your queue status.');
            }
            return;
        }

        renderStatus(data);
    } catch (error) {
        console.error(error);
        showError('Unable to connect to the queue server. Retrying automatically...');
    }
}

function renderStatus(data) {
    const isServing = data.status === 'serving';
    const isFinished = data.status === 'completed' || data.status === 'skipped';

    let statusClass = 'waiting-status';
    let statusText = 'Waiting';
    let position = data.position;
    let ahead = data.ahead;
    let estimated = data.estimated_time;
    let message = `You are <strong>${escapeHtml(data.position)}</strong> in line.`;

    if (isServing) {
        statusClass = 'serving-status';
        statusText = 'Now Serving';
        position = 'NOW';
        ahead = '0';
        estimated = 'Now';
        message = `<strong class="your-turn">It's your turn!</strong><br>Please proceed to the <strong>${escapeHtml(data.station)}</strong> station.`;
        notify(`It is your turn at ${data.station}.`);
    } else if (isFinished) {
        statusClass = 'finished-status';
        statusText = data.status === 'completed' ? 'Completed' : 'Skipped';
        position = '—';
        ahead = '—';
        estimated = '—';
        message = `Your ticket has been marked as <strong>${escapeHtml(statusText.toLowerCase())}</strong>.`;
    } else if (data.ahead <= 4) {
        notify(`You are ${data.ahead} client(s) away from your turn at ${data.station}.`);
    }

    statusContent.innerHTML = `
        <div class="queue-identity">
            <div>
                <span class="eyebrow">${escapeHtml(data.station)}</span>
                <div class="queue-label">Your Queue Number</div>
            </div>
            <div class="queue-ticket">${escapeHtml(data.ticket)}</div>
        </div>

        <div class="queue-status-pill ${statusClass}">${statusText}</div>

        <div class="queue-stat-grid">
            <div class="queue-stat">
                <span>Position</span>
                <strong>${escapeHtml(position)}</strong>
            </div>
            <div class="queue-stat">
                <span>People Ahead</span>
                <strong>${escapeHtml(ahead)}</strong>
            </div>
            <div class="queue-stat highlight-stat">
                <span>Estimated Waiting Time</span>
                <strong>${escapeHtml(estimated)}</strong>
            </div>
        </div>

        <div class="queue-message">${message}</div>
        ${!isServing && !isFinished ? `<div class="service-info">Average service time: <strong>${escapeHtml(data.average_service_minutes)} min/client</strong>, calculated from up to the latest 10 completed clients at this station.</div>` : ''}
        <div class="last-updated">Last checked just now</div>
    `;
}

function showNoTicket(message = 'You do not currently have an active queue ticket.') {
    statusContent.innerHTML = `
        <div class="empty-state">
            <div class="empty-icon">✓</div>
            <h3>No Active Queue</h3>
            <p>${escapeHtml(message)}</p>
            <a href="index.php" class="primary-link">Join a Queue</a>
        </div>
    `;
    leaveQueue.style.display = 'none';
}

function showError(message) {
    statusContent.innerHTML = `<div class="error-state">${escapeHtml(message)}</div>`;
}

function notify(message) {
    const key = `${currentTicket}:${message}`;
    if (lastNotified === key) return;
    lastNotified = key;
    localStorage.setItem('qease_last_notified', key);

    if ('Notification' in window) {
        if (Notification.permission === 'granted') {
            new Notification('Q-Ease Queue Notification', { body: message });
        } else if (Notification.permission !== 'denied') {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    new Notification('Q-Ease Queue Notification', { body: message });
                }
            });
        }
    }
}

function escapeHtml(value) {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

setInterval(checkQueue, 3000);
