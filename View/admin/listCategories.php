<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Workify Admin — Catégories</title>
<?php require BASE_PATH . '/View/shared/_styles_admin.php'; ?>
</head>
<body>
<div class="layout">
  <?php $activeModule = 'categories'; require BASE_PATH . '/View/shared/_sidebar.php'; ?>
  <main class="main">
    <div class="topbar">
      <h1 class="page-title">Gestion des <span>Catégories</span></h1>
      <div class="topbar-actions">
        <a href="index.php?module=categories&action=create" class="btn btn-primary">＋ Nouvelle catégorie</a>
      </div>
    </div>
    <div class="page-content">
      <?php if(!empty($_GET['success'])): ?>
        <div class="alert alert-success">✅ <?= match($_GET['success']){'created'=>'Catégorie créée.','updated'=>'Catégorie mise à jour.','deleted'=>'Catégorie supprimée.',default=>'Opération réussie.'} ?></div>
      <?php endif; ?>
      <?php if(!empty($_GET['error'])): ?>
        <div class="alert alert-error">❌ <?= match($_GET['error']){'has_events'=>'Impossible de supprimer : contient des événements.','not_found'=>'Catégorie introuvable.','save_failed'=>'Échec enregistrement.','invalid_id'=>'ID invalide.',default=>'Erreur.'} ?></div>
      <?php endif; ?>

      <?php
        $totalCats   = count($categories);
        $totalEvents = array_sum(array_column($categories,'event_count'));
        $currentSort = $_GET['sort'] ?? 'name';
        $currentOrder= $_GET['order'] ?? 'asc';
        function csi(string $col,string $cs,string $co):string{if($cs!==$col)return '<span style="opacity:.35;margin-left:3px;">↕</span>';return '<span style="color:var(--primary);margin-left:3px;">'.($co==='asc'?'↑':'↓').'</span>';}
        function csu(string $col,string $cs,string $co):string{$o=($cs===$col&&$co==='asc')?'desc':'asc';return'index.php?module=categories&action=list&sort='.$col.'&order='.$o;}
      ?>

      <div class="stats-grid" style="grid-template-columns:repeat(3,1fr);max-width:600px;margin-bottom:20px;">
        <div class="stat-card" style="border-left-color:#8b5cf6;">
          <div class="stat-icon">📁</div>
          <span class="stat-label">Total catégories</span>
          <span class="stat-value" style="color:#8b5cf6;"><?= $totalCats ?></span>
        </div>
        <div class="stat-card total">
          <div class="stat-icon">📅</div>
          <span class="stat-label">Total événements</span>
          <span class="stat-value"><?= $totalEvents ?></span>
        </div>
        <div class="stat-card" style="border-left-color:#22c55e;">
          <div class="stat-icon">📊</div>
          <span class="stat-label">Moy. par catégorie</span>
          <span class="stat-value" style="color:#22c55e;"><?= $totalCats > 0 ? round($totalEvents/$totalCats,1) : 0 ?></span>
        </div>
      </div>

      <div class="card">
        <table>
          <thead>
            <tr>
              <th style="width:42px;">#</th>
              <th>
                <a href="<?= csu('name',$currentSort,$currentOrder) ?>" style="text-decoration:none;color:inherit;display:flex;align-items:center;">
                  Catégorie<?= csi('name',$currentSort,$currentOrder) ?>
                </a>
              </th>
              <th>Description</th>
              <th>
                <a href="<?= csu('event_count',$currentSort,$currentOrder) ?>" style="text-decoration:none;color:inherit;display:flex;align-items:center;">
                  Événements<?= csi('event_count',$currentSort,$currentOrder) ?>
                </a>
              </th>
              <th>Part</th>
              <th style="width:130px;">Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php if(empty($categories)): ?>
            <tr><td colspan="6"><div class="empty-state"><div class="empty-icon">📭</div><p>Aucune catégorie.</p></div></td></tr>
          <?php else: ?>
            <?php foreach($categories as $c): ?>
            <?php $pct = $totalEvents > 0 ? round($c['event_count']/$totalEvents*100) : 0; ?>
            <tr>
              <td style="color:var(--muted);font-size:.78rem;"><?= $c['id'] ?></td>
              <td>
                <div class="event-title"><?= htmlspecialchars($c['name']) ?></div>
              </td>
              <td style="font-size:.83rem;color:var(--muted);max-width:260px;">
                <?= htmlspecialchars($c['description'] ? (strlen($c['description'])>80?substr($c['description'],0,80).'…':$c['description']) : '—') ?>
              </td>
              <td>
                <span class="badge" style="background:var(--info-light);color:#1d4ed8;"><?= $c['event_count'] ?> événement<?= $c['event_count']>1?'s':'' ?></span>
              </td>
              <td style="min-width:100px;">
                <div style="display:flex;align-items:center;gap:8px;font-size:.78rem;color:var(--muted);">
                  <div style="flex:1;height:6px;background:#f0eeff;border-radius:3px;overflow:hidden;">
                    <div style="height:6px;background:var(--primary);border-radius:3px;width:<?= $pct ?>%;transition:.3s;"></div>
                  </div>
                  <?= $pct ?>%
                </div>
              </td>
              <td>
                <div class="actions">
                  <a href="index.php?module=categories&action=edit&id=<?= $c['id'] ?>" class="btn btn-secondary btn-sm">✏️</a>
                  <button onclick="confirmCatDelete(<?= $c['id'] ?>, <?= $c['event_count'] ?>, '<?= addslashes(htmlspecialchars($c['name'])) ?>')" class="btn btn-danger btn-sm">🗑️</button>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>

<div id="catDeleteModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:999;align-items:center;justify-content:center;">
  <div style="background:#fff;border-radius:14px;padding:32px;max-width:380px;width:90%;text-align:center;box-shadow:0 24px 60px rgba(0,0,0,.25);">
    <div style="font-size:2.5rem;margin-bottom:12px;">⚠️</div>
    <h3 style="font-size:1.1rem;margin-bottom:8px;" id="catDeleteTitle"></h3>
    <p id="catDeleteMsg" style="color:#6b7280;font-size:.88rem;margin-bottom:24px;"></p>
    <div style="display:flex;gap:10px;justify-content:center;">
      <button onclick="document.getElementById('catDeleteModal').style.display='none'" class="btn btn-ghost">Annuler</button>
      <button id="catDeleteBtn" class="btn btn-danger">🗑️ Supprimer</button>
    </div>
  </div>
</div>
<form id="catDeleteForm" method="GET" action="index.php" style="display:none;">
  <input type="hidden" name="module" value="categories">
  <input type="hidden" name="action" value="delete">
  <input type="hidden" name="id" id="catDeleteId" value="">
</form>

<script>
function confirmCatDelete(id, eventCount, name) {
  var modal = document.getElementById('catDeleteModal');
  var btn = document.getElementById('catDeleteBtn');
  document.getElementById('catDeleteId').value = id;
  document.getElementById('catDeleteTitle').textContent = 'Supprimer «\u00a0' + name + '\u00a0» ?';
  if (eventCount > 0) {
    document.getElementById('catDeleteMsg').textContent = 'Impossible : cette catégorie contient ' + eventCount + ' événement(s). Réaffectez-les d\'abord.';
    btn.disabled = true; btn.style.opacity = '.5';
  } else {
    document.getElementById('catDeleteMsg').textContent = 'Cette action est irréversible.';
    btn.disabled = false; btn.style.opacity = '1';
    btn.onclick = function() { document.getElementById('catDeleteForm').submit(); };
  }
  modal.style.display = 'flex';
}
document.querySelectorAll('.alert').forEach(function(el) {
  setTimeout(function(){el.style.transition='opacity .5s';el.style.opacity='0';setTimeout(function(){el.remove();},500);},4500);
});
</script>
</body>
</html>
