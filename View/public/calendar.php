<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Workify — Calendrier des événements</title>

<!-- FullCalendar v6 (CDN) -->
<link    href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet"/>
<script  src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --primary:      #6c63ff;
  --primary-dark: #574fd6;
  --accent:       #ff6584;
  --bg:           #f8f7ff;
  --surface:      #ffffff;
  --border:       #e5e7eb;
  --text:         #1e1b4b;
  --muted:        #6b7280;
  --radius:       14px;
  --shadow:       0 4px 24px rgba(108,99,255,.09);
}
body { font-family:'Segoe UI',system-ui,sans-serif; background:var(--bg); color:var(--text); min-height:100vh; }

/* ── Page layout ── */
.cal-page { max-width:1100px; margin:0 auto; padding:32px 24px 60px; }

/* ── Header ── */
.cal-header {
  display:flex; align-items:center; justify-content:space-between;
  flex-wrap:wrap; gap:16px; margin-bottom:28px;
}
.cal-header-left h1 { font-size:1.55rem; font-weight:800; color:var(--text); }
.cal-header-left p  { font-size:.88rem; color:var(--muted); margin-top:4px; }

/* ── View toggle buttons ── */
.view-toggle { display:flex; gap:6px; background:#f0eeff; border-radius:10px; padding:4px; }
.view-btn {
  padding:7px 18px; border-radius:8px; border:none; cursor:pointer;
  font-size:.85rem; font-weight:600; color:var(--muted);
  background:transparent; transition:.18s;
}
.view-btn.active { background:var(--primary); color:#fff; box-shadow:0 2px 8px rgba(108,99,255,.3); }
.view-btn:hover:not(.active) { background:#ddd8ff; color:var(--primary); }

/* ── Main grid ── */
.cal-layout { display:grid; grid-template-columns:1fr 260px; gap:24px; align-items:start; }
@media(max-width:800px){ .cal-layout { grid-template-columns:1fr; } }

/* ── Calendar card ── */
.cal-card {
  background:var(--surface); border-radius:var(--radius);
  box-shadow:var(--shadow); padding:24px; overflow:hidden;
}

/* FullCalendar overrides */
.fc .fc-toolbar-title      { font-size:1.1rem; font-weight:800; color:var(--text); }
.fc .fc-button             { background:var(--primary) !important; border-color:var(--primary) !important; font-size:.8rem !important; font-weight:600 !important; border-radius:7px !important; }
.fc .fc-button:hover       { background:var(--primary-dark) !important; }
.fc .fc-button-active      { background:var(--primary-dark) !important; }
.fc .fc-today-button       { background:#ff6584 !important; border-color:#ff6584 !important; }
.fc-event                  { border-radius:6px !important; font-size:.78rem !important; font-weight:600 !important; padding:2px 6px !important; cursor:pointer; }
.fc-event:hover            { filter:brightness(1.1); }
.fc-daygrid-day.fc-day-today { background:#f0eeff !important; }
.fc-col-header-cell        { background:#f8f7ff; font-size:.8rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.5px; }
.fc-scrollgrid             { border-radius:10px; overflow:hidden; }
.fc-timegrid-slot          { height:40px !important; }
.fc-timegrid-event .fc-event-main { padding:2px 6px !important; }

/* ── Sidebar ── */
.cal-sidebar { display:flex; flex-direction:column; gap:16px; }

/* ── Legend card ── */
.legend-card {
  background:var(--surface); border-radius:var(--radius);
  box-shadow:var(--shadow); padding:20px;
}
.legend-card h3 { font-size:.88rem; font-weight:700; color:var(--text); margin-bottom:14px; text-transform:uppercase; letter-spacing:.5px; }
.legend-list    { display:flex; flex-direction:column; gap:8px; }
.legend-item    { display:flex; align-items:center; gap:10px; font-size:.83rem; color:var(--muted); font-weight:500; }
.legend-dot     { width:13px; height:13px; border-radius:50%; flex-shrink:0; box-shadow:0 2px 4px rgba(0,0,0,.15); }

/* ── Upcoming events panel ── */
.upcoming-card {
  background:var(--surface); border-radius:var(--radius);
  box-shadow:var(--shadow); padding:20px;
}
.upcoming-card h3 { font-size:.88rem; font-weight:700; color:var(--text); margin-bottom:14px; text-transform:uppercase; letter-spacing:.5px; }
.upcoming-list    { display:flex; flex-direction:column; gap:10px; max-height:380px; overflow-y:auto; }
.upcoming-item {
  display:flex; align-items:flex-start; gap:10px;
  padding:10px 12px; border-radius:9px; border:1.5px solid var(--border);
  text-decoration:none; color:var(--text); transition:.18s;
}
.upcoming-item:hover { border-color:var(--primary); background:#f8f7ff; }
.upcoming-dot  { width:10px; height:10px; border-radius:50%; margin-top:4px; flex-shrink:0; }
.upcoming-info .ev-title { font-size:.83rem; font-weight:700; line-height:1.3; margin-bottom:3px; }
.upcoming-info .ev-meta  { font-size:.75rem; color:var(--muted); }
.upcoming-empty { font-size:.82rem; color:var(--muted); text-align:center; padding:20px 0; }

/* ── Tooltip popup ── */
#cal-tooltip {
  position:fixed; z-index:9999;
  background:var(--surface); border-radius:10px;
  box-shadow:0 8px 30px rgba(0,0,0,.15);
  border:1.5px solid var(--border);
  padding:14px 16px; min-width:210px; max-width:260px;
  pointer-events:none; opacity:0; transition:opacity .15s;
}
#cal-tooltip.visible { opacity:1; }
#cal-tooltip .tt-title { font-size:.9rem; font-weight:800; color:var(--text); margin-bottom:6px; }
#cal-tooltip .tt-row   { font-size:.78rem; color:var(--muted); margin-top:3px; }
#cal-tooltip .tt-badge {
  display:inline-block; padding:2px 10px; border-radius:50px;
  font-size:.72rem; font-weight:700; margin-top:7px; color:#fff;
}

/* ── Stats bar ── */
.cal-stats {
  display:flex; gap:12px; flex-wrap:wrap; margin-bottom:20px;
}
.cal-stat {
  flex:1; min-width:100px; background:var(--surface);
  border-radius:10px; padding:14px 16px; box-shadow:var(--shadow);
  display:flex; flex-direction:column; gap:2px;
}
.cal-stat .stat-val { font-size:1.4rem; font-weight:800; color:var(--primary); }
.cal-stat .stat-lbl { font-size:.75rem; color:var(--muted); font-weight:600; }

@media(max-width:500px){
  .cal-header { flex-direction:column; align-items:flex-start; }
  .cal-page { padding:16px 12px; }
}
</style>
</head>
<body>

<?php
$activeNav  = 'calendar';
require BASE_PATH . '/View/shared/_nav.php';

/* ── Palette couleurs (même logique que dans le contrôleur) ── */
$palette = [
  'intelligence artificielle'=>'#6c63ff','machine learning'=>'#6c63ff',
  'data science'=>'#3b82f6','big data'=>'#3b82f6','data'=>'#3b82f6',
  'cyber'=>'#ef4444','sécurité'=>'#ef4444',
  'devops'=>'#f97316','cloud'=>'#f97316',
  'blockchain'=>'#f59e0b','web3'=>'#f59e0b',
  'ux'=>'#22c55e','ui'=>'#22c55e','design'=>'#22c55e',
  'mobile'=>'#14b8a6','flutter'=>'#14b8a6',
  'réalité'=>'#ec4899','vr'=>'#ec4899','métavers'=>'#ec4899',
  'robotique'=>'#06b6d4','iot'=>'#06b6d4','embarqué'=>'#06b6d4',
  'green'=>'#10b981','tech for good'=>'#10b981',
  'product'=>'#6366f1','agilité'=>'#6366f1',
  'growth'=>'#d97706','marketing'=>'#d97706',
  'it'=>'#3b82f6','informatique'=>'#3b82f6',
];
$fallbacks = ['#6c63ff','#3b82f6','#22c55e','#f97316','#ec4899',
              '#14b8a6','#f59e0b','#06b6d4','#ef4444','#10b981','#6366f1','#d97706'];

$catColors = [];
foreach ($categories as $cat) {
  $lower = mb_strtolower($cat['name']);
  $color = $fallbacks[$cat['id'] % count($fallbacks)];
  foreach ($palette as $kw => $c) {
    if (str_contains($lower, trim($kw))) { $color = $c; break; }
  }
  $catColors[$cat['id']] = ['color' => $color, 'name' => $cat['name']];
}

/* ── Statistiques rapides ── */
$pdo = Database::getInstance()->getPdo();
$statsRow = $pdo->query("SELECT
    COUNT(*) AS total,
    SUM(status='upcoming') AS upcoming,
    SUM(status='ongoing')  AS ongoing,
    SUM(is_online=0 AND status IN('upcoming','ongoing')) AS onsite
  FROM events")->fetch(PDO::FETCH_ASSOC);

/* ── Prochains événements (sidebar) ── */
$upcoming = $pdo->query(
  "SELECT e.id, e.title, e.event_date, e.is_online, e.event_category_id,
          c.name AS category_name
   FROM events e LEFT JOIN event_categories c ON e.event_category_id=c.id
   WHERE e.status IN('upcoming','ongoing') AND e.event_date >= NOW()
   ORDER BY e.event_date ASC LIMIT 12"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="cal-page">

  <!-- ── Page header ── -->
  <div class="cal-header">
    <div class="cal-header-left">
      <h1>📅 Calendrier des événements</h1>
      <p>Vue mensuelle et hebdomadaire — colorée par catégorie de métier</p>
    </div>
    <div class="view-toggle">
      <button class="view-btn active" id="btnMonth"  onclick="switchView('dayGridMonth',this)">📅 Mois</button>
      <button class="view-btn"        id="btnWeek"   onclick="switchView('timeGridWeek',this)">📆 Semaine</button>
      <button class="view-btn"        id="btnDay"    onclick="switchView('timeGridDay',this)">🗓️ Jour</button>
    </div>
  </div>

  <!-- ── Stats bar ── -->
  <div class="cal-stats">
    <div class="cal-stat">
      <span class="stat-val"><?= $statsRow['total'] ?></span>
      <span class="stat-lbl">Total événements</span>
    </div>
    <div class="cal-stat" style="--primary:#22c55e;">
      <span class="stat-val" style="color:#22c55e;"><?= $statsRow['upcoming'] ?></span>
      <span class="stat-lbl">À venir</span>
    </div>
    <div class="cal-stat" style="--primary:#f97316;">
      <span class="stat-val" style="color:#f97316;"><?= $statsRow['ongoing'] ?></span>
      <span class="stat-lbl">En cours</span>
    </div>
    <div class="cal-stat" style="--primary:#f59e0b;">
      <span class="stat-val" style="color:#f59e0b;"><?= $statsRow['onsite'] ?></span>
      <span class="stat-lbl">Présentiel</span>
    </div>
  </div>

  <!-- ── Layout ── -->
  <div class="cal-layout">

    <!-- Calendrier -->
    <div class="cal-card">
      <div id="workify-calendar"></div>
    </div>

    <!-- Sidebar -->
    <div class="cal-sidebar">

      <!-- Légende couleurs -->
      <div class="legend-card">
        <h3>🎨 Catégories</h3>
        <div class="legend-list">
          <?php foreach ($catColors as $catId => $info): ?>
          <div class="legend-item">
            <div class="legend-dot" style="background:<?= $info['color'] ?>;"></div>
            <span><?= htmlspecialchars($info['name']) ?></span>
          </div>
          <?php endforeach; ?>
          <?php if (empty($catColors)): ?>
          <div class="legend-item">
            <div class="legend-dot" style="background:#6c63ff;"></div>
            <span>Autres</span>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Prochains événements -->
      <div class="upcoming-card">
        <h3>🔜 Prochains événements</h3>
        <div class="upcoming-list">
          <?php if (empty($upcoming)): ?>
            <div class="upcoming-empty">Aucun événement à venir.</div>
          <?php else: ?>
            <?php foreach ($upcoming as $ev):
              $color = $catColors[$ev['event_category_id']]['color'] ?? '#6c63ff';
            ?>
            <a href="index.php?action=show&id=<?= $ev['id'] ?>" class="upcoming-item">
              <div class="upcoming-dot" style="background:<?= $color ?>;"></div>
              <div class="upcoming-info">
                <div class="ev-title"><?= htmlspecialchars($ev['title']) ?></div>
                <div class="ev-meta">
                  <?= date('d/m/Y • H:i', strtotime($ev['event_date'])) ?>
                  · <?= $ev['is_online'] ? '🌐' : '📍' ?>
                </div>
              </div>
            </a>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

    </div><!-- /sidebar -->
  </div><!-- /layout -->
</div>

<!-- Tooltip flottant -->
<div id="cal-tooltip">
  <div class="tt-title" id="tt-title"></div>
  <div class="tt-row"   id="tt-date"></div>
  <div class="tt-row"   id="tt-loc"></div>
  <div class="tt-badge" id="tt-cat"></div>
</div>

<script>
/* ═══════════════════════════════════════════════
   FULLCALENDAR INIT
═══════════════════════════════════════════════ */
var calInstance = null;
var tooltip     = document.getElementById('cal-tooltip');

document.addEventListener('DOMContentLoaded', function () {

  var calEl = document.getElementById('workify-calendar');

  calInstance = new FullCalendar.Calendar(calEl, {
    locale:          'fr',
    initialView:     'dayGridMonth',
    height:          'auto',
    firstDay:        1,            // Semaine commence lundi
    navLinks:        true,
    dayMaxEvents:    3,
    eventMaxStack:   3,
    headerToolbar: {
      left:   'prev,next today',
      center: 'title',
      right:  ''                   // géré par nos boutons custom
    },
    buttonText: { today:'Aujourd\'hui' },

    // ── Chargement des événements depuis l'API JSON ──
    events: {
      url:    'index.php?action=calendar_json',
      method: 'GET',
      failure: function() {
        console.error('Erreur chargement événements');
      }
    },

    // ── Affichage d'un event ──
    eventContent: function(arg) {
      var dot   = '<span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#fff;opacity:.85;margin-right:5px;flex-shrink:0;"></span>';
      var title = '<span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + arg.event.title + '</span>';
      return { html: '<div style="display:flex;align-items:center;width:100%;overflow:hidden;">' + dot + title + '</div>' };
    },

    // ── Tooltip au hover ──
    eventMouseEnter: function(info) {
      var ev    = info.event;
      var props = ev.extendedProps;
      var d     = ev.start;
      var dateStr = d ? d.toLocaleDateString('fr-FR',{weekday:'long',day:'numeric',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'}) : '';

      document.getElementById('tt-title').textContent = ev.title;
      document.getElementById('tt-date').textContent  = '📅 ' + dateStr;
      document.getElementById('tt-loc').textContent   = props.location || '';
      var badge = document.getElementById('tt-cat');
      badge.textContent = props.category || '';
      badge.style.background = props.color || '#6c63ff';

      tooltip.classList.add('visible');
    },
    eventMouseLeave: function() {
      tooltip.classList.remove('visible');
    },

    // ── Clic sur un événement → page détail ──
    eventClick: function(info) {
      info.jsEvent.preventDefault();
      if (info.event.url) window.location.href = info.event.url;
    },

    // ── Clic sur un jour (vue mois) → passer en vue semaine ──
    navLinkDayClick: function(date) {
      calInstance.changeView('timeGridDay', date);
      setActiveBtn('btnDay');
    },
  });

  calInstance.render();
});

// Déplacer le tooltip avec la souris
document.addEventListener('mousemove', function(e) {
  var x = e.clientX + 14;
  var y = e.clientY + 14;
  if (x + 280 > window.innerWidth)  x = e.clientX - 275;
  if (y + 160 > window.innerHeight) y = e.clientY - 155;
  tooltip.style.left = x + 'px';
  tooltip.style.top  = y + 'px';
});

// ── Vue toggle ──
function switchView(viewName, btn) {
  calInstance.changeView(viewName);
  setActiveBtn(btn.id);
}
function setActiveBtn(id) {
  ['btnMonth','btnWeek','btnDay'].forEach(function(bid){
    document.getElementById(bid).classList.toggle('active', bid === id);
  });
}
</script>

</body>
</html>
