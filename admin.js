async function post(action, station) {
    const body = new URLSearchParams({ action, station });
    const response = await fetch('api.php?_=' + Date.now(), {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body
    });
    const text = await response.text();
    try {
        return JSON.parse(text);
    } catch (error) {
        console.error('Server response:', text);
        throw new Error('Server returned an invalid response. Check PHP/MySQL.');
    }
}

async function nextClient(station) {
    const data = await post('next', station);
    if (!data.success) {
        alert(data.message);
        return;
    }
    refreshDashboard();
}

async function completeClient(station) {
    await post('complete', station);
    refreshDashboard();
}

async function skipClient(station) {
    await post('skip', station);
    refreshDashboard();
}


async function resetStation(station) {
    const confirmed = confirm(
        `Reset the ${station} queue?\n\nAll tickets for this station will be cleared. The next client will receive ${station === 'Registrar' ? 'R001' : station === 'Cashier' ? 'C001' : 'D001'}.`
    );

    if (!confirmed) return;

    try {
        const data = await post('reset', station);

        if (!data.success) {
            alert(data.message || 'Unable to reset the queue.');
            return;
        }

        alert(`${station} queue has been reset successfully. The next ticket will start at 001.`);
        await refreshDashboard();
    } catch (error) {
        console.error(error);
        alert('Reset failed. Make sure Apache, PHP, and MySQL are running, then try again.');
    }
}

async function refreshDashboard() {
    const response = await fetch(`api.php?action=dashboard&_=${Date.now()}`);
    const data = await response.json();

    if (!data.success) return;

    Object.entries(data.stations).forEach(([station, info]) => {
        document.getElementById(`${station}-current`).textContent = info.serving;
        document.getElementById(`${station}-waiting`).textContent = info.waiting;
    });
}

refreshDashboard();
setInterval(refreshDashboard, 2000);
