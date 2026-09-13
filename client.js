const buttons = document.querySelectorAll('.station-btn');
const joinMessage = document.getElementById('joinMessage');

buttons.forEach(button => {
    button.addEventListener('click', async () => {
        const station = button.dataset.station;
        button.disabled = true;
        button.classList.add('loading');

        try {
            const body = new URLSearchParams({ action: 'take_ticket', station });
            const response = await fetch(`api.php?_=${Date.now()}`, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body
            });
            const data = await response.json();

            if (data.success) {
                // Store only the client's own ticket, then move to the separate status page.
                localStorage.setItem('qease_ticket', data.ticket_number);
                localStorage.setItem('qease_last_notified', '');
                window.location.href = `queue-status.php?ticket=${encodeURIComponent(data.ticket_number)}`;
            } else {
                showMessage(data.message || 'Unable to get a queue ticket.', true);
            }
        } catch (error) {
            console.error(error);
            showMessage('Unable to connect to the queue server. Please try again.', true);
        } finally {
            button.disabled = false;
            button.classList.remove('loading');
        }
    });
});

function showMessage(message, isError = false) {
    if (!joinMessage) return;
    joinMessage.classList.remove('hidden');
    joinMessage.classList.toggle('error-result', isError);
    joinMessage.textContent = message;
}
