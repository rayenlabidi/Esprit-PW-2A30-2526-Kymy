<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Workify — Événements</title>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
:root{
  --primary:#6c63ff;--primary-dark:#574fd6;--accent:#ff6584;
  --success:#22c55e;--warning:#f59e0b;--danger:#ef4444;--info:#3b82f6;
  --bg:#f4f3ff;--surface:#fff;--border:#e5e7eb;--text:#1e1b4b;--muted:#6b7280;
  --radius:14px;--shadow:0 4px 24px rgba(108,99,255,.09);
}
body{font-family:'Segoe UI',system-ui,sans-serif;background:var(--bg);color:var(--text);min-height:100vh;}

/* Hero */
.hero{background:linear-gradient(135deg,var(--primary) 0%,#4338ca 100%);color:#fff;text-align:center;padding:60px 24px 80px;}
.hero h1{font-size:2.4rem;font-weight:800;margin-bottom:10px;}
.hero p{font-size:1rem;opacity:.85;max-width:480px;margin:0 auto 28px;}
.hero-search{display:flex;max-width:500px;margin:0 auto;background:#fff;border-radius:50px;overflow:hidden;box-shadow:0 8px 32px rgba(0,0,0,.18);}
.hero-search input{flex:1;border:none;outline:none;padding:14px 20px;font-size:.95rem;color:var(--text);}
.hero-search button{background:var(--accent);border:none;color:#fff;padding:14px 24px;font-weight:700;font-size:.9rem;cursor:pointer;transition:.2s;white-space:nowrap;}
.hero-search button:hover{opacity:.88;}

/* Container */
.container{max-width:1120px;margin:0 auto;padding:36px 24px;}

/* Stats strip */
.stats-strip{display:flex;gap:10px;margin-bottom:28px;flex-wrap:wrap;}
.stat-pill{background:var(--surface);border-radius:50px;padding:7px 18px;font-size:.82rem;font-weight:600;box-shadow:var(--shadow);display:flex;align-items:center;gap:8px;}
.dot{width:9px;height:9px;border-radius:50%;flex-shrink:0;}
.dot-upcoming{background:var(--info);}.dot-ongoing{background:var(--success);}.dot-completed{background:var(--muted);}.dot-cancelled{background:var(--danger);}

/* Toolbar */
.pub-toolbar{display:flex;gap:10px;margin-bottom:22px;flex-wrap:wrap;align-items:center;}
.filter-btn{padding:7px 16px;border-radius:50px;font-size:.8rem;font-weight:600;border:1.5px solid var(--border);background:var(--surface);cursor:pointer;text-decoration:none;color:var(--text);transition:.18s;}
.filter-btn:hover,.filter-btn.active{background:var(--primary);color:#fff;border-color:var(--primary);}
.filter-select{padding:7px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:.83rem;outline:none;cursor:pointer;background:var(--surface);color:var(--text);}
.filter-select:focus{border-color:var(--primary);}
.sort-select{padding:7px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:.83rem;outline:none;background:var(--surface);color:var(--text);cursor:pointer;}

/* Grid */
.cards-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:22px;}
.card{background:var(--surface);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden;display:flex;flex-direction:column;transition:transform .2s,box-shadow .2s;}
.card:hover{transform:translateY(-4px);box-shadow:0 14px 44px rgba(108,99,255,.15);}
.card-img{width:100%;height:180px;background:linear-gradient(135deg,#ede9fe,#c7d2fe);display:flex;align-items:center;justify-content:center;font-size:3rem;position:relative;overflow:hidden;}
.card-img img{width:100%;height:100%;object-fit:cover;}
.card-img-badge{position:absolute;top:10px;right:10px;}
.card-body{padding:18px;flex:1;display:flex;flex-direction:column;gap:8px;}
.card-category{font-size:.72rem;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:.5px;}
.card-title{font-size:1.02rem;font-weight:700;line-height:1.35;}
.card-meta{display:flex;flex-direction:column;gap:3px;font-size:.8rem;color:var(--muted);}
.card-meta span{display:flex;align-items:center;gap:5px;}
.card-desc{font-size:.82rem;color:var(--muted);line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.card-footer{padding:14px 18px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:8px;}
.badge{display:inline-block;padding:3px 10px;border-radius:50px;font-size:.72rem;font-weight:700;}
.badge-upcoming{background:#dbeafe;color:#1d4ed8;}.badge-ongoing{background:#dcfce7;color:#15803d;}
.badge-completed{background:#f3f4f6;color:#6b7280;}.badge-cancelled{background:#fee2e2;color:#b91c1c;}
.badge-online{background:#ede9fe;color:#7c3aed;}.badge-onsite{background:#fef3c7;color:#b45309;}
.btn-detail{display:inline-flex;align-items:center;gap:5px;padding:7px 16px;background:var(--primary);color:#fff;border-radius:8px;font-size:.82rem;font-weight:600;text-decoration:none;transition:.18s;}
.btn-detail:hover{background:var(--primary-dark);}
.card-actions{display:flex;gap:5px;}
.btn-sm-action{display:inline-flex;align-items:center;padding:7px 10px;border-radius:7px;font-size:.82rem;font-weight:600;text-decoration:none;border:none;cursor:pointer;transition:.18s;}
.btn-edit-pub{background:#f0eeff;color:var(--primary);}.btn-edit-pub:hover{background:var(--primary);color:#fff;}
.btn-del-pub{background:#fee2e2;color:var(--danger);}.btn-del-pub:hover{background:var(--danger);color:#fff;}

.empty{text-align:center;padding:80px 20px;color:var(--muted);}
.empty-icon{font-size:3rem;margin-bottom:12px;}
.alert{padding:12px 18px;border-radius:8px;margin-bottom:18px;font-size:.88rem;font-weight:500;}
.alert-success{background:#dcfce7;color:#166534;}.alert-error{background:#fee2e2;color:#991b1b;}
footer{text-align:center;padding:36px 24px;color:var(--muted);font-size:.8rem;border-top:1px solid var(--border);margin-top:40px;}
@media(max-width:600px){.hero h1{font-size:1.7rem;}.cards-grid{grid-template-columns:1fr;}}
</style>
</head>
<body>

<?php $activeNav = 'events'; require BASE_PATH . '/View/shared/_nav.php'; ?>

<!-- Hero -->
<div class="hero">
  <h1>Découvrez nos Événements</h1>
  <p>Workshops, conférences, meetups — restez connecté aux opportunités professionnelles.</p>
  <form class="hero-search" action="index.php" method="GET">
    <input type="hidden" name="action" value="search">
    <input type="text" name="q" placeholder="Rechercher un événement…" value="<?= htmlspecialchars($_GET['q']??'') ?>">
    <button type="submit">🔍 Rechercher</button>
  </form>
</div>

<div class="container">
  <?php if(!empty($_GET['success'])): ?>
    <div class="alert alert-success">✅ <?= match($_GET['success']){'created'=>'Événement créé.','updated'=>'Événement mis à jour.','deleted'=>'Événement supprimé.',default=>'OK.'} ?></div>
  <?php endif; ?>
  <?php if(!empty($_GET['error'])): ?>
    <div class="alert alert-error">❌ Événement introuvable.</div>
  <?php endif; ?>

  <!-- Stats strip -->
  <?php
    $statLabels=['upcoming'=>'À venir','ongoing'=>'En cours','completed'=>'Terminés','cancelled'=>'Annulés'];
    $stats=$stats??['upcoming'=>0,'ongoing'=>0,'completed'=>0,'cancelled'=>0];
  ?>
  <div class="stats-strip">
    <?php foreach($statLabels as $key=>$label): ?>
      <div class="stat-pill"><span class="dot dot-<?= $key ?>"></span><?= $stats[$key]??0 ?> <?= $label ?></div>
    <?php endforeach; ?>
    <div class="stat-pill" style="margin-left:auto;"><span>📅</span><?= array_sum($stats) ?> total</div>
  </div>

  <!-- Toolbar -->
  <?php
    $currentSort   = $_GET['sort']     ?? 'event_date';
    $currentOrder  = $_GET['order']    ?? 'desc';
    $currentStatus = $_GET['status']   ?? '';
    $currentCat    = $_GET['category'] ?? '';
    $currentQ      = $_GET['q']        ?? '';
    $currentAction = $_GET['action']   ?? 'list';
    $categories    = $categories ?? [];
  ?>
  <div class="pub-toolbar">
    <!-- Filtre statut -->
    <a href="index.php?action=list&sort=<?= $currentSort ?>&order=<?= $currentOrder ?>"
       class="filter-btn <?= ($currentAction==='list'&&!$currentStatus)?'active':'' ?>">Tous</a>
    <?php foreach(['upcoming'=>'🔜 À venir','ongoing'=>'▶️ En cours','completed'=>'✅ Terminés','cancelled'=>'❌ Annulés'] as $s=>$lbl): ?>
      <a href="index.php?action=filter&status=<?= $s ?>&sort=<?= $currentSort ?>&order=<?= $currentOrder ?>&category=<?= $currentCat ?>"
         class="filter-btn <?= $currentStatus===$s?'active':'' ?>"><?= $lbl ?></a>
    <?php endforeach; ?>

    <!-- Filtre catégorie -->
    <form method="GET" action="index.php" style="margin-left:auto;">
      <input type="hidden" name="action" value="filter">
      <input type="hidden" name="status" value="<?= htmlspecialchars($currentStatus) ?>">
      <input type="hidden" name="sort" value="<?= htmlspecialchars($currentSort) ?>">
      <input type="hidden" name="order" value="<?= htmlspecialchars($currentOrder) ?>">
      <select name="category" class="filter-select" onchange="this.form.submit()">
        <option value="">📁 Toutes catégories</option>
        <?php foreach($categories as $cat): ?>
          <option value="<?= $cat['id'] ?>" <?= $currentCat==$cat['id']?'selected':'' ?>><?= htmlspecialchars($cat['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </form>

    <!-- Tri -->
    <form method="GET" action="index.php">
      <input type="hidden" name="action" value="<?= htmlspecialchars($currentAction) ?>">
      <input type="hidden" name="status" value="<?= htmlspecialchars($currentStatus) ?>">
      <input type="hidden" name="category" value="<?= htmlspecialchars($currentCat) ?>">
      <input type="hidden" name="q" value="<?= htmlspecialchars($currentQ) ?>">
      <select name="sort" class="sort-select" onchange="
        var o = this.form.querySelector('[name=order]');
        o.value = 'asc';
        this.form.submit();">
        <option value="event_date" <?= $currentSort==='event_date'?'selected':'' ?>>📅 Trier par date</option>
        <option value="title"       <?= $currentSort==='title'?'selected':'' ?>>🔤 Trier par titre</option>
        <option value="max_participants" <?= $currentSort==='max_participants'?'selected':'' ?>>👥 Participants</option>
        <option value="status"      <?= $currentSort==='status'?'selected':'' ?>>🏷 Statut</option>
      </select>
      <input type="hidden" name="order" value="<?= htmlspecialchars($currentOrder) ?>">
    </form>

    <!-- Ordre -->
    <a href="index.php?<?= http_build_query(array_merge($_GET,['order'=>$currentOrder==='asc'?'desc':'asc'])) ?>"
       class="filter-btn" title="Inverser l'ordre">
      <?= $currentOrder==='asc'?'↑ Croissant':'↓ Décroissant' ?>
    </a>
  </div>

  <!-- Résumé -->
  <div style="font-size:.8rem;color:var(--muted);margin-bottom:14px;">
    <?= count($events) ?> événement<?= count($events)>1?'s':'' ?>
    <?php if($currentQ): ?> — Recherche : «&nbsp;<?= htmlspecialchars($currentQ) ?>&nbsp;»<?php endif; ?>
    <?php if($currentStatus): ?> — Statut : <b><?= ucfirst($currentStatus) ?></b><?php endif; ?>
    <?php if($currentQ||$currentStatus||$currentCat): ?>
      — <a href="index.php?action=list" style="color:var(--primary);">Effacer les filtres</a>
    <?php endif; ?>
  </div>

  <!-- Cards -->
  <?php if(empty($events)): ?>
    <div class="empty">
      <div class="empty-icon">📭</div>
      <p>Aucun événement trouvé.</p>
    </div>
  <?php else: ?>
    <div class="cards-grid">
      <?php foreach($events as $e): ?>
      <div class="card">
        <div class="card-img">
          <?php if(!empty($e['image_url'])): ?>
            <img src="<?= htmlspecialchars($e['image_url']) ?>" alt="<?= htmlspecialchars($e['title']) ?>">
          <?php else: ?>
            📅
          <?php endif; ?>
          <span class="card-img-badge">
            <span class="badge badge-<?= $e['status'] ?>"><?= match($e['status']){'upcoming'=>'🔜','ongoing'=>'▶️','completed'=>'✅','cancelled'=>'❌',default=>''} ?> <?= ucfirst($e['status']) ?></span>
          </span>
        </div>
        <div class="card-body">
          <span class="card-category"><?= htmlspecialchars($e['category_name']??'') ?></span>
          <div class="card-title"><?= htmlspecialchars($e['title']) ?></div>
          <div class="card-meta">
            <span>🗓 <?= date('d/m/Y à H:i', strtotime($e['event_date'])) ?></span>
            <span>📍 <?= htmlspecialchars($e['location']) ?></span>
            <span>👤 <?= htmlspecialchars($e['organizer_name']) ?></span>
            <span>👥 <?= $e['max_participants'] ?> participants max</span>
          </div>
          <p class="card-desc"><?= htmlspecialchars($e['description']) ?></p>
        </div>
        <div class="card-footer">
          <div>
            <span class="badge <?= $e['is_online']?'badge-online':'badge-onsite' ?>">
              <?= $e['is_online']?'🌐 En ligne':'📍 Présentiel' ?>
            </span>
          </div>
          <div class="card-actions">
            <a href="index.php?action=show&id=<?= $e['id'] ?>" class="btn-detail">Voir →</a>
            <a href="index.php?action=edit&id=<?= $e['id'] ?>" class="btn-sm-action btn-edit-pub" title="Modifier">✏️</a>
            <button onclick="confirmDelete(<?= $e['id'] ?>, '<?= addslashes(htmlspecialchars($e['title'])) ?>')" class="btn-sm-action btn-del-pub" title="Supprimer">🗑️</button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<div id="delModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:999;align-items:center;justify-content:center;">
  <div style="background:#fff;border-radius:14px;padding:32px;max-width:360px;width:90%;text-align:center;box-shadow:0 24px 60px rgba(0,0,0,.25);">
    <div style="font-size:2.5rem;margin-bottom:10px;">⚠️</div>
    <h3 style="margin-bottom:8px;">Supprimer l'événement ?</h3>
    <p id="delModalTitle" style="color:#6b7280;font-size:.88rem;margin-bottom:22px;"></p>
    <div style="display:flex;gap:10px;justify-content:center;">
      <button onclick="document.getElementById('delModal').style.display='none'" style="padding:9px 20px;border-radius:8px;border:1.5px solid #ddd;background:#fff;cursor:pointer;font-weight:600;">Annuler</button>
      <button onclick="document.getElementById('pubDelForm').submit()" style="padding:9px 20px;border-radius:8px;background:#ef4444;color:#fff;border:none;cursor:pointer;font-weight:600;">🗑️ Supprimer</button>
    </div>
  </div>
</div>
<form id="pubDelForm" method="GET" action="index.php" style="display:none;">
  <input type="hidden" name="action" value="delete">
  <input type="hidden" name="id" id="pubDelId" value="">
</form>

<footer>© <?= date('Y') ?> Workify — Tous droits réservés.</footer>

<script>
function confirmDelete(id, title) {
  document.getElementById('pubDelId').value = id;
  document.getElementById('delModalTitle').textContent = '«\u00a0' + title + '\u00a0» sera supprimé définitivement.';
  document.getElementById('delModal').style.display = 'flex';
}
document.getElementById('delModal').addEventListener('click', function(e){ if(e.target===this) this.style.display='none'; });
document.querySelectorAll('.alert').forEach(function(el){ setTimeout(function(){el.style.transition='opacity .5s';el.style.opacity='0';setTimeout(function(){el.remove();},500);},4500); });
</script>
</body>
</html>
