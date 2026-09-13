<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Q-Ease Display</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="display-page">
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
      <p>Now Serving</p>
    </div>
  </div>
</header>

<main class="display-grid">
  <section class="display-card">
    <h2>Registrar</h2>
    <div id="Registrar-display" class="display-number">-</div>
  </section>

  <section class="display-card">
    <h2>Cashier</h2>
    <div id="Cashier-display" class="display-number">-</div>
  </section>

  <section class="display-card">
    <h2>Document Releasing</h2>
    <div id="Document Releasing-display" class="display-number">-</div>
  </section>
</main>

<div id="callBanner" class="call-banner hidden"></div>

<script src="display.js"></script>
</body>
</html>
