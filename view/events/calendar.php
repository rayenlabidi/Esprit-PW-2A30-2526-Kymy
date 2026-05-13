<?php
$pageTitle = 'Calendrier Evenements';
$activeModule = 'events';
$office = isset($office) ? $office : 'front';
include __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <div>
        <p class="eyebrow"><?= $office === 'back' ? 'Workify events' : 'Agenda Workify'; ?></p>
        <h2>Calendrier des evenements</h2>
        <p class="muted">Visualisez les prochaines sessions par jour et ouvrez rapidement leurs details.</p>
    </div>
    <a class="btn" href="../controller/EventC.php?office=<?= htmlspecialchars($office, ENT_QUOTES); ?>&action=list">Liste</a>
</div>

<div class="calendar-shell" data-calendar-url="../controller/EventC.php?office=<?= htmlspecialchars($office, ENT_QUOTES); ?>&action=calendar_json">
    <div class="calendar-header">
        <button class="btn" type="button" data-calendar-prev>Precedent</button>
        <h3 data-calendar-title>Calendrier</h3>
        <button class="btn" type="button" data-calendar-next>Suivant</button>
    </div>
    <div class="calendar-weekdays">
        <span>Lun</span><span>Mar</span><span>Mer</span><span>Jeu</span><span>Ven</span><span>Sam</span><span>Dim</span>
    </div>
    <div class="calendar-grid" data-calendar-grid></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const shell = document.querySelector('[data-calendar-url]');
    if (!shell) return;

    const grid = shell.querySelector('[data-calendar-grid]');
    const title = shell.querySelector('[data-calendar-title]');
    const state = { date: new Date(), events: [] };

    const sameDay = (a, b) => a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth() && a.getDate() === b.getDate();
    const dateKey = (date) => date.toISOString().slice(0, 10);
    const monthLabel = (date) => date.toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' });
    const escapeHTML = (value) => String(value).replace(/[&<>"']/g, (char) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    }[char]));

    function render() {
        const year = state.date.getFullYear();
        const month = state.date.getMonth();
        const first = new Date(year, month, 1);
        const last = new Date(year, month + 1, 0);
        const offset = (first.getDay() + 6) % 7;
        const today = new Date();
        title.textContent = monthLabel(state.date);
        grid.innerHTML = '';

        for (let i = 0; i < offset; i++) {
            grid.insertAdjacentHTML('beforeend', '<div class="calendar-day muted-day"></div>');
        }

        for (let day = 1; day <= last.getDate(); day++) {
            const current = new Date(year, month, day);
            const events = state.events.filter((event) => event.date === dateKey(current));
            const cards = events.map((event) => `
                <a class="calendar-event" href="../controller/${escapeHTML(event.url)}" style="border-color:${escapeHTML(event.color)}">
                    <span style="background:${escapeHTML(event.color)}"></span>
                    <strong>${escapeHTML(event.time)} - ${escapeHTML(event.title)}</strong>
                    <small>${escapeHTML(event.category)} / ${escapeHTML(event.location)}</small>
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
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
