<?php
$calendarWidgetId = 'eventCalendar' . uniqid();
?>
<div class="calendar-widget" id="<?= htmlspecialchars($calendarWidgetId, ENT_QUOTES); ?>" data-calendar-url="../controller/EventC.php?office=<?= htmlspecialchars($office, ENT_QUOTES); ?>&action=calendar_json">
    <div class="calendar-widget-head">
        <div>
            <p class="eyebrow">Calendrier</p>
            <h3><?= $office === 'back' ? 'Planning des evenements' : 'Prochains evenements'; ?></h3>
        </div>
        <div class="calendar-widget-controls">
            <button class="btn" type="button" data-calendar-prev>Precedent</button>
            <strong data-calendar-title></strong>
            <button class="btn" type="button" data-calendar-next>Suivant</button>
        </div>
    </div>
    <div class="calendar-weekdays">
        <span>Lun</span><span>Mar</span><span>Mer</span><span>Jeu</span><span>Ven</span><span>Sam</span><span>Dim</span>
    </div>
    <div class="calendar-grid compact" data-calendar-grid></div>
</div>

<script>
(function () {
    const shell = document.getElementById(<?= json_encode($calendarWidgetId); ?>);
    if (!shell || shell.dataset.ready === '1') return;
    shell.dataset.ready = '1';

    const grid = shell.querySelector('[data-calendar-grid]');
    const title = shell.querySelector('[data-calendar-title]');
    const state = { date: new Date(), events: [] };

    const escapeHTML = (value) => String(value).replace(/[&<>"']/g, (char) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    }[char]));
    const dateKey = (date) => date.toISOString().slice(0, 10);
    const sameDay = (a, b) => a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth() && a.getDate() === b.getDate();

    function render() {
        const year = state.date.getFullYear();
        const month = state.date.getMonth();
        const first = new Date(year, month, 1);
        const last = new Date(year, month + 1, 0);
        const offset = (first.getDay() + 6) % 7;
        const today = new Date();
        title.textContent = state.date.toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' });
        grid.innerHTML = '';

        for (let i = 0; i < offset; i++) {
            grid.insertAdjacentHTML('beforeend', '<div class="calendar-day muted-day"></div>');
        }

        for (let day = 1; day <= last.getDate(); day++) {
            const current = new Date(year, month, day);
            const cards = state.events.filter((event) => event.date === dateKey(current)).map((event) => `
                <a class="calendar-event" href="../controller/${escapeHTML(event.url)}" style="border-color:${escapeHTML(event.color)}">
                    <span style="background:${escapeHTML(event.color)}"></span>
                    <strong>${escapeHTML(event.time)} - ${escapeHTML(event.title)}</strong>
                </a>
            `).join('');

            grid.insertAdjacentHTML('beforeend', `
                <div class="calendar-day ${sameDay(current, today) ? 'today' : ''}">
                    <div class="calendar-number">${day}</div>
                    ${cards}
                </div>
            `);
        }
    }

    shell.querySelector('[data-calendar-prev]').addEventListener('click', () => {
        state.date = new Date(state.date.getFullYear(), state.date.getMonth() - 1, 1);
        render();
    });

    shell.querySelector('[data-calendar-next]').addEventListener('click', () => {
        state.date = new Date(state.date.getFullYear(), state.date.getMonth() + 1, 1);
        render();
    });

    fetch(shell.dataset.calendarUrl)
        .then((response) => response.json())
        .then((events) => {
            state.events = events;
            render();
        })
        .catch(() => {
            grid.innerHTML = '<div class="card">Impossible de charger le calendrier.</div>';
        });
})();
</script>
