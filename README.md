# Q-Ease Simple Queueing System

Technology:
- HTML
- CSS
- JavaScript
- PHP
- MySQL

## Features

1. Client interface
   - Client chooses Registrar, Cashier, or Document Releasing.
   - System creates a queue number:
     - Registrar: R001, R002...
     - Cashier: C001, C002...
     - Document Releasing: D001, D002...
   - Client page checks the queue every 3 seconds.
   - When 4 or fewer clients are ahead, the browser displays an alert and attempts a browser notification.
   - When it is the client's turn, another notification appears.

2. Admin interface
   - See the current ticket per station.
   - See how many clients are waiting.
   - Call Next.
   - Complete the current client.
   - Skip the current client.

3. Display interface
   - Shows the current ticket being served for all three stations.
   - Automatically refreshes every 2 seconds.
   - Announces newly called tickets using browser speech synthesis when supported.

## Installation using XAMPP

1. Install XAMPP.
2. Copy the `queueing_system` folder into:
   `C:/xampp/htdocs/`
3. Start Apache and MySQL from XAMPP.
4. Open phpMyAdmin.
5. Import `database.sql`.
6. Open:
   `http://localhost/queueing_system/`

Interfaces:
- Client: `http://localhost/queueing_system/`
- Admin: `http://localhost/queueing_system/admin.php`
- Display: `http://localhost/queueing_system/display.php`

## Important notification note

Browser notifications normally require permission from the user. The client should allow notifications when prompted.

The 4-client rule is calculated separately for each station. For example, if R005 has R001-R004 still waiting ahead of it, R005 is considered 4 clients away and receives the notification.

## Beginner-friendly flow

Client -> take ticket -> MySQL saves ticket
Admin -> Call Next -> PHP changes ticket to serving
Display -> reads serving ticket from PHP
Client -> polls PHP -> counts waiting tickets ahead
Client -> notification when ahead <= 4
