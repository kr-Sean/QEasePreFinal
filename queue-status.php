<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Q-Ease | My Queue</title>
<link rel="stylesheet" href="style.css?v=4">
</head>
<body>
<header>
  <div class="brand">
    <div class="brand-icon" aria-hidden="true">
      <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M7 4.5A2.5 2.5 0 0 1 9.5 2h5A2.5 2.5 0 0 1 17 4.5V7h1.5A2.5 2.5 0 0 1 21 9.5v9a2.5 2.5 0 0 1-2.5 2.5h-13A2.5 2.5 0 0 1 3 18.5v-9A2.5 2.5 0 0 1 5.5 7H7V4.5Z" stroke="currentColor" stroke-width="1.8"/>
        <path d="M7 7h10M8 12h8M8 16h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        <circle cx="16.5" cy="16" r="1.5" fill="currentColor"/>
      </svg>
    </div>
    <div class="brand-text">
      <h1>Q-Ease</h1>
      <p>Smart &amp; Simple Queueing System</p>
    </div>
  </div>
</header>

<main class="container client-container status-container">
  <section class="card queue-status-card">
    <div class="section-heading status-heading">
      <span class="eyebrow">My queue</span>
      <h2>Queue Status</h2>
      <p>Happy Queueing.</p>
    </div>

    <div id="statusContent" class="status-content">
      <div class="loading-state">Loading your queue...</div>
    </div>

    <div class="status-actions">
      <button id="leaveQueue" type="button" class="secondary-btn">Leave Queue</button>
      <a href="index.php" class="text-link">Choose another service</a>
    </div>
  </section>
</main>

<script src="queue-status.js?v=1"></script>
</body>
</html>
