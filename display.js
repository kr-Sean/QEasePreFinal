let previousCalls = {};

async function refreshDisplay() {
    try {
        const response = await fetch(`api.php?action=dashboard&_=${Date.now()}`);
        const data = await response.json();
        if (!data.success) return;

        Object.entries(data.stations).forEach(([station, info]) => {
            document.getElementById(`${station}-display`).textContent = info.serving;
        });

        const newest = data.current_calls[0];
        if (newest && previousCalls[newest.station] !== newest.ticket_number) {
            previousCalls[newest.station] = newest.ticket_number;
            showCall(`${newest.ticket_number} — ${newest.station}`);
        }
    } catch (error) {
        console.error(error);
    }
}

function showCall(text) {
    const banner = document.getElementById('callBanner');
    banner.textContent = `NOW SERVING: ${text}`;
    banner.classList.remove('hidden');

    // Simple audio announcement if the browser allows speech synthesis.
    if ('speechSynthesis' in window) {
        const utterance = new SpeechSynthesisUtterance(`Now serving ${text}`);
        speechSynthesis.cancel();
        speechSynthesis.speak(utterance);
    }

    setTimeout(() => banner.classList.add('hidden'), 5000);
}

refreshDisplay();
setInterval(refreshDisplay, 2000);
