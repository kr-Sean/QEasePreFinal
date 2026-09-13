<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Q-Ease | Join Queue</title>
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
      <h1>Q-EASE</h1>
      <p>Your Average Queueing System.</p>
    </div>
  </div>
</header>

<main class="container client-container">
  <section class="card client-card">
    <div class="section-heading">
      <span class="eyebrow">Client portal</span>
      <h2>Choose a Service</h2>
      <p>Select the station you want to queue in.</p>
    </div>

    <div class="station-grid">
      <button type="button" class="station-btn" data-station="Registrar">
        <span>Registrar</span><small>Queue for Registrar services</small>
      </button>
      <button type="button" class="station-btn" data-station="Cashier">
        <span>Cashier</span><small>Queue for Cashier services</small>
      </button>
      <button type="button" class="station-btn" data-station="Document Releasing">
        <span>Document Releasing</span><small>Queue for document releasing</small>
      </button>
    </div>

    <div id="joinMessage" class="result hidden"></div>
  </section>
</main>

<script src="client.js?v=4"></script>
</body>
</html>
