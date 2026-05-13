<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Workify - Evenements</title>
<link rel="stylesheet" href="assets/workify-template.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
.event-stats-row{display:flex;gap:16px;align-items:center;flex-wrap:wrap;margin:30px 0 24px}
.event-stat-pill{display:inline-flex;align-items:center;gap:10px;min-height:58px;padding:0 26px;border-radius:22px;background:rgba(255,255,255,.9);border:1px solid rgba(219,229,243,.96);box-shadow:0 18px 34px rgba(15,23,42,.07);font-weight:850;color:#111827}
.event-stat-pill.total{margin-left:auto}
.stat-dot{width:12px;height:12px;border-radius:999px;display:inline-block}
.dot-upcoming{background:#2f66f5}.dot-ongoing{background:#0f766e}.dot-completed{background:#64748b}.dot-cancelled{background:#ef4444}
.filters-panel{padding:26px;border-radius:26px;background:rgba(255,255,255,.86);border:1px solid rgba(219,229,243,.96);box-shadow:0 20px 45px rgba(37,87,217,.08);margin-bottom:28px}
.filter-status-row,.filter-control-row{display:flex;align-items:center;gap:12px;flex-wrap:wrap}
.filter-control-row{margin-top:16px}
.filter-chip,.filter-select-pill{min-height:54px;display:inline-flex;align-items:center;gap:9px;padding:0 22px;border-radius:999px;border:1px solid #dbe5f3;background:rgba(255,255,255,.96);color:#0f172a;font-weight:850;text-decoration:none;box-shadow:0 10px 22px rgba(15,23,42,.04);transition:.2s ease}
.filter-chip:hover,.filter-select-pill:hover{transform:translateY(-1px);border-color:rgba(47,102,245,.42);color:#2f66f5}
.filter-chip.active{background:#2f66f5;color:#fff;border-color:#2f66f5;box-shadow:0 14px 28px rgba(47,102,245,.24)}
.filter-ico{font-size:1rem;line-height:1}
.filter-select-pill{position:relative;padding:0 18px;min-width:260px}
.filter-select-pill select{width:100%;height:52px;border:0;background:transparent;font:inherit;font-weight:850;color:inherit;outline:none;padding-left:4px;cursor:pointer}
.filter-order{min-width:160px;justify-content:center}
.filter-spacer{flex:1}
.event-card{display:grid;grid-template-rows:auto 1fr;min-height:100%;transition:transform .2s ease,box-shadow .2s ease}
.event-card:hover{transform:translateY(-3px);box-shadow:0 22px 48px rgba(15,23,42,.1)}
.event-card .course-media{min-height:190px;position:relative}
.event-card .course-media::after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(15,23,42,0),rgba(15,23,42,.48))}
.event-status-chip{position:absolute;right:14px;top:14px;z-index:1;background:rgba(255,255,255,.92);backdrop-filter:blur(12px)}
.event-card-title{margin:14px 0 8px;font-size:1.08rem;line-height:1.35}
.event-card-desc{min-height:50px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.event-meta-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin:18px 0}
.event-meta-item{display:flex;gap:9px;align-items:flex-start;padding:11px;border-radius:14px;background:#f7faff}
.event-meta-item i{color:var(--brand);width:16px;text-align:center;margin-top:2px}
.event-meta-item span{display:block;color:var(--ink-500);font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.04em}
.event-meta-item strong{display:block;color:var(--ink-950);font-size:.84rem;line-height:1.35}
.event-actions{justify-content:space-between;border-top:1px solid var(--line);padding-top:16px}
@media(max-width:640px){.event-meta-grid{grid-template-columns:1fr}.event-actions{justify-content:flex-start}.event-stat-pill.total{margin-left:0}.filter-select-pill{min-width:100%;width:100%}}
</style>
</head>
<body>

<?php $activeNav = 'events'; require BASE_PATH . '/View/shared/_nav.php'; ?>

<main class="page-shell">
  <div class="container">
    <section class="hero">
      <div>
        <span class="eyebrow">Evenements Workify</span>
        <h1>Decouvrez nos evenements professionnels.</h1>
        <p class="hero-text">Workshops, conferences et meetups pour rester connecte aux opportunites professionnelles.</p>
        <form class="hero-actions" action="index.php" method="GET">
          <input type="hidden" name="action" value="search">
          <input class="form-control" type="text" name="q" placeholder="Rechercher un evenement..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
          <button type="submit" class="btn btn-primary">Rechercher</button>
        </form>
      </div>
      <div class="hero-panel">
        <?php
          $stats = $stats ?? ['upcoming'=>0,'ongoing'=>0,'completed'=>0,'cancelled'=>0];
          $heroStats = [
            ['A venir', $stats['upcoming'] ?? 0],
            ['En cours', $stats['ongoing'] ?? 0],
            ['Termines', $stats['completed'] ?? 0],
            ['Annules', $stats['cancelled'] ?? 0],
          ];
        ?>
        <?php foreach ($heroStats as [$label, $value]): ?>
          <div class="mini-card">
            <span class="stat-label"><?= htmlspecialchars($label) ?></span>
            <strong style="display:block;font-size:2rem;margin-top:6px;"><?= (int)$value ?></strong>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <?php if (!empty($_GET['success'])): ?>
      <div class="flash flash-success"><?= match($_GET['success']) { 'created'=>'Evenement cree.','updated'=>'Evenement mis a jour.','deleted'=>'Evenement supprime.', default=>'Operation reussie.' } ?></div>
    <?php endif; ?>
    <?php if (!empty($_GET['error'])): ?>
      <div class="flash flash-error">Evenement introuvable.</div>
    <?php endif; ?>

    <?php
      $currentSort   = $_GET['sort']     ?? 'event_date';
      $currentOrder  = $_GET['order']    ?? 'desc';
      $currentStatus = $_GET['status']   ?? '';
      $currentCat    = $_GET['category'] ?? '';
      $currentQ      = $_GET['q']        ?? '';
      $currentAction = $_GET['action']   ?? 'list';
      $categories    = $categories ?? [];
    ?>

    <div class="event-stats-row">
      <span class="event-stat-pill"><span class="stat-dot dot-upcoming"></span><?= (int)($stats['upcoming'] ?? 0) ?> A venir</span>
      <span class="event-stat-pill"><span class="stat-dot dot-ongoing"></span><?= (int)($stats['ongoing'] ?? 0) ?> En cours</span>
      <span class="event-stat-pill"><span class="stat-dot dot-completed"></span><?= (int)($stats['completed'] ?? 0) ?> Termines</span>
      <span class="event-stat-pill"><span class="stat-dot dot-cancelled"></span><?= (int)($stats['cancelled'] ?? 0) ?> Annules</span>
      <span class="event-stat-pill total"><span class="filter-ico">🗓️</span><?= array_sum($stats) ?> total</span>
    </div>

    <section class="filters-panel">
      <div class="filter-status-row">
        <a href="index.php?action=list&sort=<?= urlencode($currentSort) ?>&order=<?= urlencode($currentOrder) ?>&category=<?= urlencode($currentCat) ?>&q=<?= urlencode($currentQ) ?>"
           class="filter-chip <?= ($currentAction === 'list' && !$currentStatus) ? 'active' : '' ?>">Tous</a>
        <?php foreach(['upcoming'=>'↪️ A venir','ongoing'=>'▶️ En cours','completed'=>'✅ Termines','cancelled'=>'❌ Annules'] as $s=>$lbl): ?>
          <a href="index.php?action=filter&status=<?= $s ?>&sort=<?= urlencode($currentSort) ?>&order=<?= urlencode($currentOrder) ?>&category=<?= urlencode($currentCat) ?>&q=<?= urlencode($currentQ) ?>"
             class="filter-chip <?= $currentStatus === $s ? 'active' : '' ?>"><?= $lbl ?></a>
        <?php endforeach; ?>
      </div>

      <div class="filter-control-row">
        <form method="GET" action="index.php" class="filter-select-pill">
          <span class="filter-ico">📁</span>
          <input type="hidden" name="action" value="filter">
          <input type="hidden" name="status" value="<?= htmlspecialchars($currentStatus) ?>">
          <input type="hidden" name="sort" value="<?= htmlspecialchars($currentSort) ?>">
          <input type="hidden" name="order" value="<?= htmlspecialchars($currentOrder) ?>">
          <input type="hidden" name="q" value="<?= htmlspecialchars($currentQ) ?>">
          <select name="category" onchange="this.form.submit()">
            <option value="">Toutes categories</option>
            <?php foreach($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>" <?= $currentCat == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </form>

        <form method="GET" action="index.php" class="filter-select-pill">
          <span class="filter-ico">🗓️</span>
          <input type="hidden" name="action" value="<?= htmlspecialchars($currentAction) ?>">
          <input type="hidden" name="status" value="<?= htmlspecialchars($currentStatus) ?>">
          <input type="hidden" name="category" value="<?= htmlspecialchars($currentCat) ?>">
          <input type="hidden" name="order" value="<?= htmlspecialchars($currentOrder) ?>">
          <input type="hidden" name="q" value="<?= htmlspecialchars($currentQ) ?>">
          <select name="sort" onchange="this.form.submit()">
            <option value="event_date" <?= $currentSort === 'event_date' ? 'selected' : '' ?>>Trier par date</option>
            <option value="title" <?= $currentSort === 'title' ? 'selected' : '' ?>>Trier par titre</option>
            <option value="max_participants" <?= $currentSort === 'max_participants' ? 'selected' : '' ?>>Participants</option>
            <option value="status" <?= $currentSort === 'status' ? 'selected' : '' ?>>Statut</option>
          </select>
        </form>

        <a class="filter-chip filter-order" href="index.php?<?= http_build_query(array_merge($_GET, ['order' => $currentOrder === 'asc' ? 'desc' : 'asc'])) ?>">
          <?= $currentOrder === 'asc' ? '↑ Croissant' : '↓ Decroissant' ?>
        </a>
      </div>

      <?php if ($currentQ || $currentStatus || $currentCat): ?>
        <div style="margin-top:14px;">
          <a class="ghost-link" href="index.php?action=list">Effacer les filtres</a>
        </div>
      <?php endif; ?>
    </section>

    <div class="section-head">
      <div>
        <span class="eyebrow">Catalogue</span>
        <h2><?= count($events) ?> evenement<?= count($events) > 1 ? 's' : '' ?></h2>
      </div>
      <a href="index.php?action=create" class="btn btn-primary">Nouvel evenement</a>
    </div>

    <?php if (empty($events)): ?>
      <section class="section-card empty-card">
        <p class="empty-copy">Aucun evenement trouve.</p>
      </section>
    <?php else: ?>
      <section class="card-grid">
        <?php foreach ($events as $e): ?>
          <article class="course-card event-card">
            <a href="index.php?action=show&id=<?= $e['id'] ?>" class="course-media" style="display:block;background-image:url('<?= htmlspecialchars(!empty($e['image_url']) ? $e['image_url'] : 'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=900&q=80') ?>');">
              <span class="badge badge-info event-status-chip">
                <i class="fa-solid fa-circle-dot"></i>
                <?= match($e['status']) { 'upcoming'=>'A venir', 'ongoing'=>'En cours', 'completed'=>'Termine', 'cancelled'=>'Annule', default=>ucfirst($e['status']) } ?>
              </span>
            </a>
            <div class="course-body">
              <div class="chip-row">
                <span class="badge badge-info"><i class="fa-solid fa-folder-open"></i><?= htmlspecialchars($e['category_name'] ?? 'Categorie') ?></span>
                <span class="badge <?= $e['is_online'] ? 'badge-success' : 'badge-warning' ?>"><i class="fa-solid <?= $e['is_online'] ? 'fa-globe' : 'fa-location-dot' ?>"></i><?= $e['is_online'] ? 'En ligne' : 'Presentiel' ?></span>
              </div>
              <h3 class="event-card-title"><?= htmlspecialchars($e['title']) ?></h3>
              <p class="card-copy event-card-desc"><?= htmlspecialchars($e['description']) ?></p>
              <div class="event-meta-grid">
                <div class="event-meta-item"><i class="fa-regular fa-calendar"></i><div><span>Date</span><strong><?= date('d/m/Y H:i', strtotime($e['event_date'])) ?></strong></div></div>
                <div class="event-meta-item"><i class="fa-solid fa-location-dot"></i><div><span>Lieu</span><strong><?= htmlspecialchars($e['location']) ?></strong></div></div>
                <div class="event-meta-item"><i class="fa-solid fa-user-tie"></i><div><span>Organisateur</span><strong><?= htmlspecialchars($e['organizer_name']) ?></strong></div></div>
                <div class="event-meta-item"><i class="fa-solid fa-users"></i><div><span>Places</span><strong><?= (int)$e['max_participants'] ?> participants</strong></div></div>
              </div>
              <div class="card-actions event-actions">
                <a href="index.php?action=show&id=<?= $e['id'] ?>" class="btn btn-primary btn-small"><i class="fa-solid fa-eye"></i>Voir</a>
                <div class="card-actions">
                  <a href="index.php?action=edit&id=<?= $e['id'] ?>" class="btn btn-outline btn-small" title="Modifier"><i class="fa-solid fa-pen"></i></a>
                  <button onclick="confirmDelete(<?= $e['id'] ?>, '<?= addslashes(htmlspecialchars($e['title'])) ?>')" class="btn btn-danger btn-small" type="button" title="Supprimer"><i class="fa-solid fa-trash"></i></button>
                </div>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </section>
    <?php endif; ?>
  </div>
</main>

<div id="delModal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.48);z-index:999;align-items:center;justify-content:center;padding:20px;">
  <div class="auth-card" style="max-width:420px;">
    <h2>Supprimer l'evenement ?</h2>
    <p id="delModalTitle" class="muted"></p>
    <div class="hero-actions">
      <button onclick="document.getElementById('delModal').style.display='none'" class="btn btn-outline" type="button">Annuler</button>
      <button onclick="document.getElementById('pubDelForm').submit()" class="btn btn-danger" type="button">Supprimer</button>
    </div>
  </div>
</div>
<form id="pubDelForm" method="GET" action="index.php" style="display:none;">
  <input type="hidden" name="action" value="delete">
  <input type="hidden" name="id" id="pubDelId" value="">
</form>

<?php require BASE_PATH . '/View/shared/_footer.php'; ?>

<script src="assets/workify-template.js"></script>
<script>
function confirmDelete(id, title) {
  document.getElementById('pubDelId').value = id;
  document.getElementById('delModalTitle').textContent = '"' + title + '" sera supprime definitivement.';
  document.getElementById('delModal').style.display = 'flex';
}
document.getElementById('delModal').addEventListener('click', function(e){ if(e.target===this) this.style.display='none'; });
</script>
</body>
</html>
