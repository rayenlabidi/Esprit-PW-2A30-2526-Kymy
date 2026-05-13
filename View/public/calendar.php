<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Workify - Calendrier des evenements</title>
<link rel="stylesheet" href="assets/workify-template.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<style>
.calendar-shell{display:grid;grid-template-columns:minmax(0,1fr)320px;gap:22px;align-items:start}
.calendar-card{padding:24px;overflow:hidden}
.calendar-top{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:18px;flex-wrap:wrap}
.calendar-title-row{display:flex;align-items:center;gap:12px}
.calendar-title-icon{display:grid;place-items:center;width:42px;height:42px;border-radius:14px;color:#fff;background:linear-gradient(135deg,#2f66f5,#14b8a6)}
.calendar-side{display:grid;gap:18px}
.legend-list,.upcoming-list{display:grid;gap:10px;margin-top:14px}
.legend-item{display:flex;align-items:center;gap:10px;padding:11px 12px;border-radius:14px;background:#f7faff;border:1px solid rgba(219,229,243,.85);font-weight:800;color:#334155}
.legend-dot{width:12px;height:12px;border-radius:999px;box-shadow:0 0 0 5px rgba(47,102,245,.08)}
.upcoming-item{display:grid;grid-template-columns:42px 1fr;gap:12px;align-items:center;padding:12px;border-radius:16px;background:#fff;border:1px solid rgba(219,229,243,.95);text-decoration:none;color:inherit;transition:.2s ease}
.upcoming-item:hover{transform:translateY(-1px);border-color:rgba(47,102,245,.38);box-shadow:0 12px 26px rgba(15,23,42,.06)}
.upcoming-date{display:grid;place-items:center;min-height:42px;border-radius:14px;color:#fff;background:linear-gradient(135deg,#2f66f5,#14b8a6);font-size:.75rem;font-weight:850;line-height:1.1;text-align:center}
.upcoming-title{font-weight:850;color:#0f172a;line-height:1.3}
.upcoming-meta{font-size:.8rem;color:#73849d;margin-top:3px}
.fc{font-family:inherit}
.fc .fc-toolbar{gap:14px;align-items:center;margin-bottom:18px!important}
.fc .fc-toolbar-title{font-size:1.25rem!important;font-weight:850;color:#14213d}
.fc .fc-button{background:#fff!important;border:1px solid #dbe5f3!important;color:#14213d!important;border-radius:14px!important;font-weight:850!important;padding:9px 13px!important;box-shadow:0 10px 20px rgba(15,23,42,.04)!important}
.fc .fc-button:hover,.fc .fc-button-active{background:#2f66f5!important;border-color:#2f66f5!important;color:#fff!important}
.fc .fc-today-button{background:linear-gradient(135deg,#2f66f5,#14b8a6)!important;border-color:transparent!important;color:#fff!important}
.fc-theme-standard td,.fc-theme-standard th,.fc-theme-standard .fc-scrollgrid{border-color:#dbe5f3!important}
.fc-scrollgrid{border-radius:20px;overflow:hidden;background:#fff}
.fc-col-header-cell{background:#f7faff!important;padding:10px 0!important}
.fc-col-header-cell-cushion{color:#73849d;text-transform:uppercase;font-size:.78rem;font-weight:850;text-decoration:none}
.fc-daygrid-day-number{color:#14213d;text-decoration:none;font-weight:800;padding:10px!important}
.fc-daygrid-day.fc-day-today{background:rgba(47,102,245,.08)!important}
.fc-event{border-radius:10px!important;font-weight:850!important;border:0!important;padding:3px 7px!important;box-shadow:0 8px 18px rgba(15,23,42,.1)}
.fc-daygrid-event-dot{display:none}
#cal-tooltip{position:fixed;z-index:9999;pointer-events:none;opacity:0;transition:opacity .15s;max-width:280px}
.visible{opacity:1!important}
@media(max-width:980px){.calendar-shell{grid-template-columns:1fr}.calendar-side{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:680px){.calendar-side{grid-template-columns:1fr}.fc .fc-toolbar{flex-direction:column;align-items:flex-start}.calendar-card{padding:16px}}
</style>
</head>
<body>

<?php
$activeNav = 'calendar';
require BASE_PATH . '/View/shared/_nav.php';

$palette = [
  'intelligence artificielle'=>'#2f66f5','machine learning'=>'#2f66f5',
  'data science'=>'#14b8a6','big data'=>'#14b8a6','data'=>'#14b8a6',
  'cyber'=>'#ef4444','securite'=>'#ef4444',
  'devops'=>'#f97316','cloud'=>'#f97316',
  'blockchain'=>'#f59e0b','web3'=>'#f59e0b',
  'ux'=>'#22c55e','ui'=>'#22c55e','design'=>'#22c55e',
];
$fallbacks = ['#2f66f5','#14b8a6','#22c55e','#f97316','#f59e0b','#06b6d4','#ef4444'];
$catColors = [];
foreach ($categories as $cat) {
  $lower = mb_strtolower($cat['name']);
  $color = $fallbacks[$cat['id'] % count($fallbacks)];
  foreach ($palette as $kw => $c) {
    if (str_contains($lower, trim($kw))) { $color = $c; break; }
  }
  $catColors[$cat['id']] = ['color' => $color, 'name' => $cat['name']];
}
$pdo = Database::getInstance()->getPdo();
$statsRow = $pdo->query("SELECT COUNT(*) AS total, SUM(status='upcoming') AS upcoming, SUM(status='ongoing') AS ongoing, SUM(is_online=0 AND status IN('upcoming','ongoing')) AS onsite FROM events")->fetch(PDO::FETCH_ASSOC);
$upcoming = $pdo->query("SELECT e.id,e.title,e.event_date,e.is_online,e.event_category_id,c.name AS category_name FROM events e LEFT JOIN event_categories c ON e.event_category_id=c.id WHERE e.status IN('upcoming','ongoing') AND e.event_date >= NOW() ORDER BY e.event_date ASC LIMIT 12")->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="page-shell">
  <div class="container">
    <section class="hero">
      <div>
        <span class="eyebrow">Calendrier</span>
        <h1>Calendrier des evenements.</h1>
        <p class="hero-text">Vue mensuelle et hebdomadaire, coloree par categorie de metier.</p>
      </div>
      <div class="hero-panel">
        <div class="mini-card"><span class="stat-label">Total</span><strong style="font-size:2rem;"><?= (int)$statsRow['total'] ?></strong></div>
        <div class="mini-card"><span class="stat-label">A venir</span><strong style="font-size:2rem;"><?= (int)$statsRow['upcoming'] ?></strong></div>
        <div class="mini-card"><span class="stat-label">En cours</span><strong style="font-size:2rem;"><?= (int)$statsRow['ongoing'] ?></strong></div>
        <div class="mini-card"><span class="stat-label">Presentiel</span><strong style="font-size:2rem;"><?= (int)$statsRow['onsite'] ?></strong></div>
      </div>
    </section>

    <div class="calendar-shell">
      <section class="section-card calendar-card">
        <div class="calendar-top">
          <div class="calendar-title-row">
            <span class="calendar-title-icon"><i class="fa-solid fa-calendar-days"></i></span>
            <div>
              <span class="eyebrow">Planning</span>
              <h2>Agenda</h2>
            </div>
          </div>
          <span class="badge badge-info"><i class="fa-solid fa-circle-info"></i> Cliquez sur un evenement</span>
        </div>
        <div id="workify-calendar"></div>
      </section>

      <aside class="calendar-side">
        <section class="profile-card">
          <span class="eyebrow">Categories</span>
          <div class="legend-list">
            <?php foreach ($catColors as $info): ?>
              <div class="legend-item">
                <span class="legend-dot" style="background:<?= $info['color'] ?>;"></span>
                <span><?= htmlspecialchars($info['name']) ?></span>
              </div>
            <?php endforeach; ?>
            <?php if (empty($catColors)): ?>
              <div class="legend-item"><span class="legend-dot" style="background:#2f66f5;"></span><span>Autres</span></div>
            <?php endif; ?>
          </div>
        </section>

        <section class="profile-card">
          <span class="eyebrow">Prochains</span>
          <div class="upcoming-list">
            <?php if (empty($upcoming)): ?>
              <div class="muted">Aucun evenement a venir.</div>
            <?php else: ?>
              <?php foreach ($upcoming as $ev): ?>
                <a class="upcoming-item" href="index.php?action=show&id=<?= $ev['id'] ?>">
                  <span class="upcoming-date"><?= date('d/m', strtotime($ev['event_date'])) ?><br><?= date('H:i', strtotime($ev['event_date'])) ?></span>
                  <span>
                    <span class="upcoming-title"><?= htmlspecialchars($ev['title']) ?></span>
                    <span class="upcoming-meta">
                      <i class="fa-solid <?= $ev['is_online'] ? 'fa-globe' : 'fa-location-dot' ?>"></i>
                      <?= htmlspecialchars($ev['category_name'] ?? 'Categorie') ?>
                    </span>
                  </span>
                </a>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </section>
      </aside>
    </div>
  </div>
</main>

<?php require BASE_PATH . '/View/shared/_footer.php'; ?>

<div id="cal-tooltip" class="profile-card">
  <strong id="tt-title"></strong>
  <div class="muted" id="tt-date"></div>
  <div class="muted" id="tt-loc"></div>
  <span class="badge badge-info" id="tt-cat"></span>
</div>

<script>
var calInstance=null, tooltip=document.getElementById('cal-tooltip');
document.addEventListener('DOMContentLoaded',function(){
  calInstance=new FullCalendar.Calendar(document.getElementById('workify-calendar'),{
    locale:'fr',initialView:'dayGridMonth',height:'auto',firstDay:1,navLinks:true,dayMaxEvents:3,
    headerToolbar:{left:'prev,next today',center:'title',right:'dayGridMonth,timeGridWeek,timeGridDay'},
    buttonText:{today:'Aujourd\'hui',month:'Mois',week:'Semaine',day:'Jour'},
    events:{url:'index.php?action=calendar_json',method:'GET'},
    eventMouseEnter:function(info){
      var ev=info.event, props=ev.extendedProps, d=ev.start;
      document.getElementById('tt-title').textContent=ev.title;
      document.getElementById('tt-date').textContent=d?d.toLocaleDateString('fr-FR',{weekday:'long',day:'numeric',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'}):'';
      document.getElementById('tt-loc').textContent=props.location||'';
      var catBadge=document.getElementById('tt-cat');
      var catColor=props.color||ev.backgroundColor||'#2f66f5';
      catBadge.textContent=props.category||'Categorie';
      catBadge.style.background=catColor+'22';
      catBadge.style.color=catColor;
      catBadge.style.border='1px solid '+catColor+'55';
      tooltip.classList.add('visible');
    },
    eventMouseLeave:function(){tooltip.classList.remove('visible');},
    eventClick:function(info){info.jsEvent.preventDefault(); if(info.event.url) window.location.href=info.event.url;}
  });
  calInstance.render();
});
document.addEventListener('mousemove',function(e){
  var x=e.clientX+14,y=e.clientY+14;
  if(x+280>window.innerWidth)x=e.clientX-275;
  if(y+160>window.innerHeight)y=e.clientY-155;
  tooltip.style.left=x+'px'; tooltip.style.top=y+'px';
});
</script>
</body>
</html>
