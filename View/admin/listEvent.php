<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Workify Admin — Gestion des Événements</title>
<?php require BASE_PATH . '/View/shared/_styles_admin.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
</head>
<body>
<div class="layout">
  <?php $activeModule = 'events'; require BASE_PATH . '/View/shared/_sidebar.php'; ?>
  <main class="main">
    <div class="topbar">
      <h1 class="page-title">Gestion des <span>Événements</span></h1>
      <div class="topbar-actions">
        <button class="btn btn-pdf" onclick="exportPDF()">📄 Export PDF</button>
        <a href="index.php?action=create" class="btn btn-primary">＋ Nouvel événement</a>
      </div>
    </div>
    <div class="page-content">
      <?php if (!empty($_GET['success'])): ?>
        <div class="alert alert-success">✅ <?= match($_GET['success']) { 'created'=>'Événement créé.','updated'=>'Événement mis à jour.','deleted'=>'Événement supprimé.',default=>'Opération réussie.' } ?></div>
      <?php endif; ?>
      <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-error">❌ <?= match($_GET['error']) { 'not_found'=>'Introuvable.','save_failed'=>'Échec enregistrement.','invalid_id'=>'ID invalide.',default=>'Erreur.' } ?></div>
      <?php endif; ?>

      <?php
        $statConf = ['upcoming'=>['À venir','upcoming','🔜'],'ongoing'=>['En cours','ongoing','▶️'],'completed'=>['Terminés','completed','✅'],'cancelled'=>['Annulés','cancelled','❌']];
        $stats = $stats ?? ['upcoming'=>0,'ongoing'=>0,'completed'=>0,'cancelled'=>0];
        $total = array_sum($stats);
      ?>
      <div class="stats-grid" style="grid-template-columns:repeat(5,1fr);">
        <div class="stat-card total">
          <div class="stat-icon">📅</div>
          <span class="stat-label">Total</span>
          <span class="stat-value"><?= $total ?></span>
        </div>
        <?php foreach($statConf as $key=>[$lbl,$cls,$ico]): ?>
        <div class="stat-card <?= $cls ?>">
          <div class="stat-icon"><?= $ico ?></div>
          <span class="stat-label"><?= $lbl ?></span>
          <span class="stat-value"><?= $stats[$key]??0 ?></span>
        </div>
        <?php endforeach; ?>
      </div>

      <?php
        $currentSort   = $_GET['sort']     ?? 'event_date';
        $currentOrder  = $_GET['order']    ?? 'desc';
        $currentStatus = $_GET['status']   ?? '';
        $currentCat    = $_GET['category'] ?? '';
        $currentQ      = $_GET['q']        ?? '';
        $currentAction = $_GET['action']   ?? 'list';
        $categories    = $categories ?? [];

        function si(string $col, string $cs, string $co): string {
          if ($cs !== $col) return '<span style="opacity:.35;margin-left:3px;">↕</span>';
          return '<span style="color:var(--primary);margin-left:3px;">'.($co==='asc'?'↑':'↓').'</span>';
        }
        function su(string $col, string $cs, string $co, array $extra=[]): string {
          $o = ($cs===$col && $co==='asc') ? 'desc' : 'asc';
          return 'index.php?'.http_build_query(array_merge($extra,['sort'=>$col,'order'=>$o]));
        }
        $ex = ['action'=>$currentAction,'status'=>$currentStatus,'category'=>$currentCat,'q'=>$currentQ];
      ?>
      <div class="toolbar">
        <form class="search-wrap" action="index.php" method="GET" style="max-width:300px;">
          <input type="hidden" name="action" value="search">
          <input type="hidden" name="sort" value="<?= htmlspecialchars($currentSort) ?>">
          <input type="hidden" name="order" value="<?= htmlspecialchars($currentOrder) ?>">
          <input type="hidden" name="category" value="<?= htmlspecialchars($currentCat) ?>">
          <span class="search-icon">🔍</span>
          <input type="text" name="q" placeholder="Rechercher…" value="<?= htmlspecialchars($currentQ) ?>">
        </form>
        <form method="GET" action="index.php">
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
        <div class="filter-group">
          <a href="index.php?action=list&sort=<?= $currentSort ?>&order=<?= $currentOrder ?>" class="filter-btn <?= $currentAction==='list'&&!$currentStatus?'active':'' ?>">Tous</a>
          <?php foreach(['upcoming'=>'🔜 À venir','ongoing'=>'▶️ En cours','completed'=>'✅ Terminés','cancelled'=>'❌ Annulés'] as $s=>$lbl): ?>
            <a href="index.php?action=filter&status=<?= $s ?>&sort=<?= $currentSort ?>&order=<?= $currentOrder ?>&category=<?= $currentCat ?>" class="filter-btn <?= $currentStatus===$s?'active':'' ?>"><?= $lbl ?></a>
          <?php endforeach; ?>
        </div>
      </div>

      <div style="font-size:.8rem;color:var(--muted);margin-bottom:10px;">
        <?= count($events) ?> événement<?= count($events)>1?'s':'' ?> trouvé<?= count($events)>1?'s':'' ?>
        <?php if($currentQ): ?> — Recherche : «&nbsp;<?= htmlspecialchars($currentQ) ?>&nbsp;»<?php endif; ?>
        <?php if($currentStatus): ?> — Statut : <b><?= ucfirst($currentStatus) ?></b><?php endif; ?>
        <?php if($currentQ||$currentStatus||$currentCat): ?> — <a href="index.php?action=list" style="color:var(--primary);">Réinitialiser</a><?php endif; ?>
      </div>

      <div class="card">
        <table id="eventsTable">
          <thead>
            <tr>
              <th>#</th>
              <th><a href="<?= su('title',$currentSort,$currentOrder,$ex) ?>" style="text-decoration:none;color:inherit;display:flex;align-items:center;">Titre<?= si('title',$currentSort,$currentOrder) ?></a></th>
              <th><a href="<?= su('event_date',$currentSort,$currentOrder,$ex) ?>" style="text-decoration:none;color:inherit;display:flex;align-items:center;">Date<?= si('event_date',$currentSort,$currentOrder) ?></a></th>
              <th>Lieu</th>
              <th>Catégorie</th>
              <th><a href="<?= su('max_participants',$currentSort,$currentOrder,$ex) ?>" style="text-decoration:none;color:inherit;display:flex;align-items:center;">Participants<?= si('max_participants',$currentSort,$currentOrder) ?></a></th>
              <th><a href="<?= su('status',$currentSort,$currentOrder,$ex) ?>" style="text-decoration:none;color:inherit;display:flex;align-items:center;">Statut<?= si('status',$currentSort,$currentOrder) ?></a></th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php if(empty($events)): ?>
            <tr><td colspan="8"><div class="empty-state"><div class="empty-icon">📭</div><p>Aucun événement trouvé.</p></div></td></tr>
          <?php else: ?>
            <?php foreach($events as $e): ?>
            <tr>
              <td style="color:var(--muted);font-size:.78rem;"><?= $e['id'] ?></td>
              <td>
                <div class="event-title"><?= htmlspecialchars($e['title']) ?></div>
                <div class="event-meta">👤 <?= htmlspecialchars($e['organizer_name']) ?></div>
              </td>
              <td style="white-space:nowrap;font-size:.85rem;">
                <?= date('d/m/Y', strtotime($e['event_date'])) ?><br>
                <span style="color:var(--muted);font-size:.75rem;"><?= date('H:i', strtotime($e['event_date'])) ?></span>
              </td>
              <td>
                <div style="font-size:.85rem;"><?= htmlspecialchars($e['location']) ?></div>
                <span class="badge <?= $e['is_online']?'badge-online':'badge-onsite' ?>" style="margin-top:3px;">
                  <?= $e['is_online']?'🌐 Ligne':'📍 Site' ?>
                </span>
              </td>
              <td style="font-size:.85rem;"><?= htmlspecialchars($e['category_name']??'—') ?></td>
              <td>
                <div style="display:flex;align-items:center;gap:8px;font-size:.85rem;">
                  <span><?= $e['max_participants'] ?></span>
                </div>
              </td>
              <td>
                <span class="badge badge-<?= $e['status'] ?>">
                  <?= match($e['status']){'upcoming'=>'🔜 À venir','ongoing'=>'▶️ En cours','completed'=>'✅ Terminé','cancelled'=>'❌ Annulé',default=>ucfirst($e['status'])} ?>
                </span>
              </td>
              <td>
                <div class="actions">
                  <a href="index.php?action=edit&id=<?= $e['id'] ?>" class="btn btn-secondary btn-sm" title="Modifier">✏️</a>
                  <button onclick="confirmDelete(<?= $e['id'] ?>, '<?= addslashes(htmlspecialchars($e['title'])) ?>')" class="btn btn-danger btn-sm" title="Supprimer">🗑️</button>
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

<div id="deleteModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:999;align-items:center;justify-content:center;">
  <div style="background:#fff;border-radius:14px;padding:32px;max-width:380px;width:90%;text-align:center;box-shadow:0 24px 60px rgba(0,0,0,.25);">
    <div style="font-size:2.5rem;margin-bottom:12px;">⚠️</div>
    <h3 style="font-size:1.1rem;margin-bottom:8px;">Supprimer l'événement ?</h3>
    <p id="deleteModalTitle" style="color:#6b7280;font-size:.88rem;margin-bottom:24px;"></p>
    <div style="display:flex;gap:10px;justify-content:center;">
      <button onclick="closeDeleteModal()" class="btn btn-ghost">Annuler</button>
      <button onclick="document.getElementById('deleteForm').submit()" class="btn btn-danger">🗑️ Supprimer</button>
    </div>
  </div>
</div>
<form id="deleteForm" method="GET" action="index.php" style="display:none;">
  <input type="hidden" name="action" value="delete">
  <input type="hidden" name="id" id="deleteId" value="">
</form>

<script>
function confirmDelete(id, title) {
  document.getElementById('deleteId').value = id;
  document.getElementById('deleteModalTitle').textContent = '«\u00a0' + title + '\u00a0» sera supprimé définitivement.';
  document.getElementById('deleteModal').style.display = 'flex';
}
function closeDeleteModal() {
  document.getElementById('deleteModal').style.display = 'none';
}
document.getElementById('deleteModal').addEventListener('click', function(e) { if(e.target===this) closeDeleteModal(); });
document.querySelectorAll('.alert').forEach(function(el) {
  setTimeout(function() { el.style.transition='opacity .5s'; el.style.opacity='0'; setTimeout(function(){el.remove();},500); }, 4500);
});

function exportPDF() {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });
  doc.setFillColor(108, 99, 255);
  doc.rect(0, 0, 297, 22, 'F');
  doc.setTextColor(255, 255, 255);
  doc.setFontSize(14); doc.setFont('helvetica', 'bold');
  doc.text('WORKIFY \u2014 Liste des \u00c9v\u00e9nements', 14, 14);
  doc.setFontSize(8); doc.setFont('helvetica', 'normal');
  doc.text('Export\u00e9 le ' + new Date().toLocaleDateString('fr-FR'), 240, 14);
  const rows = [];
  document.querySelectorAll('#eventsTable tbody tr').forEach(function(tr) {
    const tds = tr.querySelectorAll('td');
    if (tds.length < 7) return;
    rows.push([
      tds[0].innerText.trim(),
      (tds[1].querySelector('.event-title')?.innerText||'').trim(),
      (tds[1].querySelector('.event-meta')?.innerText||'').replace('👤 ','').trim(),
      tds[2].innerText.replace('\n',' ').trim(),
      tds[3].firstChild?.textContent?.trim()||'',
      tds[4].innerText.trim(),
      tds[5].innerText.trim(),
      tds[6].innerText.replace(/[▶️🔜✅❌]/g,'').trim(),
    ]);
  });
  doc.autoTable({
    startY: 26,
    head: [['#','Titre','Organisateur','Date','Lieu','Catégorie','Participants','Statut']],
    body: rows,
    styles: { fontSize: 8, cellPadding: 3 },
    headStyles: { fillColor: [108,99,255], textColor: 255, fontStyle: 'bold' },
    alternateRowStyles: { fillColor: [248,247,255] },
    columnStyles: { 0:{cellWidth:10,halign:'center'}, 1:{cellWidth:52}, 2:{cellWidth:34}, 3:{cellWidth:28}, 4:{cellWidth:34}, 5:{cellWidth:26}, 6:{cellWidth:20,halign:'center'}, 7:{cellWidth:24} },
    didDrawPage: function() {
      doc.setFontSize(8); doc.setTextColor(150);
      doc.text('Page ' + doc.internal.getCurrentPageInfo().pageNumber, 148, doc.internal.pageSize.getHeight()-6, {align:'center'});
    }
  });
  doc.save('workify_evenements_' + new Date().toISOString().slice(0,10) + '.pdf');
}
</script>
</body>
</html>
