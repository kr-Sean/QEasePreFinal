<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Q-Ease Admin</title>
<link rel="stylesheet" href="style.css">
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
      <p>Queue Management Dashboard</p>
    </div>
  </div>
</header>

<main class="container">
  <div class="admin-grid">
    <section class="card admin-card">
      <h2>Registrar</h2>
      <div class="big-current" id="Registrar-current">-</div>
      <p>Waiting: <span id="Registrar-waiting">0</span></p>
      <button type="button" onclick="nextClient('Registrar')">Call Next</button>
      <button type="button" class="secondary-btn" onclick="completeClient('Registrar')">Complete</button>
      <button type="button" class="danger-btn" onclick="skipClient('Registrar')">Skip</button>
      <button type="button" class="reset-btn" onclick="resetStation('Registrar')">Reset Queue</button>
    </section>

    <section class="card admin-card">
      <h2>Cashier</h2>
      <div class="big-current" id="Cashier-current">-</div>
      <p>Waiting: <span id="Cashier-waiting">0</span></p>
      <button type="button" onclick="nextClient('Cashier')">Call Next</button>
      <button type="button" class="secondary-btn" onclick="completeClient('Cashier')">Complete</button>
      <button type="button" class="danger-btn" onclick="skipClient('Cashier')">Skip</button>
      <button type="button" class="reset-btn" onclick="resetStation('Cashier')">Reset Queue</button>
    </section>

    <section class="card admin-card">
      <h2>Document Releasing</h2>
      <div class="big-current" id="Document Releasing-current">-</div>
      <p>Waiting: <span id="Document Releasing-waiting">0</span></p>
      <button type="button" onclick="nextClient('Document Releasing')">Call Next</button>
      <button type="button" class="secondary-btn" onclick="completeClient('Document Releasing')">Complete</button>
      <button type="button" class="danger-btn" onclick="skipClient('Document Releasing')">Skip</button>
      <button type="button" class="reset-btn" onclick="resetStation('Document Releasing')">Reset Queue</button>
    </section>
  </div>
</main>

<script src="admin.js?v=3"></script>
</body>
</html>
