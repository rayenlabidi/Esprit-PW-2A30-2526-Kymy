<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Workify — <?= isset($event) ? 'Modifier' : 'Créer' ?> un Événement</title>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --primary:      #6c63ff;
    --primary-dark: #574fd6;
    --accent:       #ff6584;
    --danger:       #ef4444;
    --bg:           #f8f7ff;
    --surface:      #ffffff;
    --border:       #e5e7eb;
    --text:         #1e1b4b;
    --muted:        #6b7280;
    --radius:       14px;
    --shadow:       0 4px 24px rgba(108,99,255,.1);
  }
  body { font-family:'Segoe UI',system-ui,sans-serif; background:var(--bg); color:var(--text); min-height:100vh; }

  nav {
    background:var(--surface); border-bottom:1px solid var(--border);
    padding:0 32px; display:flex; align-items:center; justify-content:space-between;
    height:64px; position:sticky; top:0; z-index:10;
    box-shadow:0 2px 8px rgba(108,99,255,.06);
  }
  .brand { font-size:1.4rem; font-weight:800; color:var(--primary); text-decoration:none; }
  .brand span { color:var(--accent); }
  .nav-links { display:flex; gap:24px; align-items:center; }
  .nav-links a { text-decoration:none; color:var(--muted); font-size:.9rem; font-weight:500; transition:.2s; }
  .nav-links a:hover { color:var(--primary); }

  .page { max-width:760px; margin:40px auto; padding:0 24px 60px; }

  .breadcrumb { font-size:.82rem; color:var(--muted); margin-bottom:24px; display:flex; align-items:center; gap:6px; }
  .breadcrumb a { color:var(--primary); text-decoration:none; }
  .breadcrumb a:hover { text-decoration:underline; }

  .form-card { background:var(--surface); border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; }

  .form-header {
    background:linear-gradient(135deg, var(--primary), var(--primary-dark));
    color:#fff; padding:28px 32px; display:flex; align-items:center; gap:16px;
  }
  .form-header-icon { font-size:2rem; }
  .form-header h1  { font-size:1.4rem; font-weight:700; }
  .form-header p   { font-size:.88rem; opacity:.8; margin-top:3px; }

  .form-body { padding:32px; }
  .form-row  { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
  .form-group { display:flex; flex-direction:column; gap:6px; margin-bottom:20px; }
  .form-group.full { grid-column:1/-1; }

  label { font-size:.85rem; font-weight:600; color:var(--text); }
  label .req { color:var(--danger); }

  input[type="text"],
  input[type="number"],
  input[type="datetime-local"],
  select,
  textarea {
    width:100%; padding:10px 14px; border:1.5px solid var(--border);
    border-radius:8px; font-size:.9rem; font-family:inherit;
    color:var(--text); outline:none; transition:.2s; background:#fff;
  }
  input:focus, select:focus, textarea:focus {
    border-color:var(--primary); box-shadow:0 0 0 3px rgba(108,99,255,.1);
  }
  textarea { resize:vertical; min-height:110px; }

  /* ── File upload zone ── */
  .upload-zone {
    border:2px dashed var(--border);
    border-radius:10px;
    padding:28px 20px;
    text-align:center;
    cursor:pointer;
    transition:.2s;
    position:relative;
    background:#fafafe;
  }
  .upload-zone:hover, .upload-zone.dragover { border-color:var(--primary); background:#f0eeff; }
  .upload-zone input[type="file"] {
    position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%;
  }
  .upload-icon  { font-size:2.2rem; margin-bottom:8px; }
  .upload-label { font-size:.88rem; color:var(--muted); }
  .upload-label strong { color:var(--primary); }
  .upload-hint  { font-size:.75rem; color:var(--muted); margin-top:4px; }

  /* Preview after selection */
  .img-preview {
    margin-top:14px; width:100%; max-height:200px;
    object-fit:cover; border-radius:8px;
    border:1px solid var(--border); display:none;
  }
  .preview-name {
    margin-top:8px; font-size:.8rem; color:var(--primary);
    font-weight:600; display:none; text-align:center;
  }

  .field-error { font-size:.78rem; color:var(--danger); margin-top:2px; display:none; }
  .field-error.show { display:block; }
  input.invalid, select.invalid, textarea.invalid { border-color:var(--danger); }

  .checkbox-wrap { display:flex; align-items:center; gap:10px; padding:10px 0; }
  .checkbox-wrap input[type="checkbox"] { width:18px; height:18px; accent-color:var(--primary); cursor:pointer; }
  .checkbox-wrap label { font-size:.9rem; font-weight:500; cursor:pointer; }

  .form-footer { display:flex; justify-content:flex-end; gap:12px; padding-top:8px; border-top:1px solid var(--border); margin-top:8px; }
  .btn { display:inline-flex; align-items:center; gap:6px; padding:11px 24px; border-radius:8px; font-size:.9rem; font-weight:600; cursor:pointer; text-decoration:none; border:none; transition:.2s; }
  .btn-primary   { background:var(--primary); color:#fff; }
  .btn-primary:hover { background:var(--primary-dark); }
  .btn-secondary { background:#f3f4f6; color:var(--muted); }
  .btn-secondary:hover { background:#e5e7eb; }

  @media(max-width:560px){
    .form-row  { grid-template-columns:1fr; }
    .form-body { padding:20px; }
    .form-header { padding:20px; }
  }
</style>
</head>
<body>

<?php $activeNav = 'events'; require BASE_PATH . '/View/shared/_nav.php'; ?>

<div class="page">

  <div class="breadcrumb">
    <a href="index.php">Événements</a>
    <span>›</span>
    <span><?= isset($event) ? 'Modifier l\'événement' : 'Nouvel événement' ?></span>
  </div>

  <div class="form-card">

    <div class="form-header">
      <span class="form-header-icon">📅</span>
      <div>
        <h1><?= isset($event) ? 'Modifier l\'événement' : 'Nouvel événement' ?></h1>
        <p><?= isset($event) ? 'Mettez à jour les informations ci-dessous.' : 'Remplissez le formulaire pour créer un événement.' ?></p>
      </div>
    </div>

    <div class="form-body">
      <form
        id="eventForm"
        method="POST"
        action="index.php?action=<?= isset($event) ? 'update' : 'store' ?>"
        enctype="multipart/form-data"
        novalidate
      >
        <?php if (isset($event)): ?>
          <input type="hidden" name="id" value="<?= $event['id'] ?>">
          <input type="hidden" name="existing_image_url" value="<?= htmlspecialchars($event['image_url'] ?? '') ?>">
        <?php endif; ?>

        <div class="form-row">

          <!-- Titre -->
          <div class="form-group full">
            <label for="title">Titre <span class="req">*</span></label>
            <input type="text" id="title" name="title"
              placeholder="Ex: Workshop Laravel & MVC"
              value="<?= htmlspecialchars($event['title'] ?? '') ?>">
            <span class="field-error" id="err-title">Le titre est obligatoire (3 à 180 caractères).</span>
          </div>

          <!-- Description -->
          <div class="form-group full">
            <label for="description">Description <span class="req">*</span></label>
            <textarea id="description" name="description"
              placeholder="Décrivez l'événement en détail…"><?= htmlspecialchars($event['description'] ?? '') ?></textarea>
            <span class="field-error" id="err-description">La description est obligatoire (min. 10 caractères).</span>
          </div>

          <!-- Date -->
          <div class="form-group">
            <label for="event_date">Date & heure <span class="req">*</span></label>
            <input type="datetime-local" id="event_date" name="event_date"
              value="<?= isset($event['event_date']) ? date('Y-m-d\TH:i', strtotime($event['event_date'])) : '' ?>">
            <span class="field-error" id="err-event_date">Veuillez choisir une date valide.</span>
          </div>

          <!-- Lieu -->
          <div class="form-group">
            <label for="location">Lieu <span class="req">*</span></label>
            <input type="text" id="location" name="location"
              placeholder="Ex: Tunis, Centre de conférence…"
              value="<?= htmlspecialchars($event['location'] ?? '') ?>">
            <span class="field-error" id="err-location">Le lieu est obligatoire.</span>
          </div>

          <!-- Nom de l'entreprise (remplace organizer_id) -->
          <div class="form-group">
            <label for="organizer_name">Nom de l'entreprise <span class="req">*</span></label>
            <input type="text" id="organizer_name" name="organizer_name"
              placeholder="Ex: TechCorp, StartupXYZ…"
              value="<?= htmlspecialchars($event['organizer_name'] ?? '') ?>">
            <span class="field-error" id="err-organizer_name">Le nom de l'entreprise est obligatoire.</span>
          </div>

          <!-- Catégorie -->
          <div class="form-group">
            <label for="category_id">Catégorie <span class="req">*</span></label>
            <select id="category_id" name="category_id">
              <option value="">— Choisir —</option>
              <?php
                $categories = $categories ?? [];
                foreach ($categories as $cat):
                  $sel = (isset($event['category_id']) && $event['category_id'] == $cat['id']) ? 'selected' : '';
              ?>
                <option value="<?= $cat['id'] ?>" <?= $sel ?>><?= htmlspecialchars($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
            <span class="field-error" id="err-category_id">Veuillez choisir une catégorie.</span>
          </div>

          <!-- Participants max -->
          <div class="form-group">
            <label for="max_participants">Participants max <span class="req">*</span></label>
            <input type="number" id="max_participants" name="max_participants"
              min="1" max="10000" placeholder="50"
              value="<?= htmlspecialchars($event['max_participants'] ?? '50') ?>">
            <span class="field-error" id="err-max_participants">Nombre entre 1 et 10 000.</span>
          </div>

          <!-- Statut -->
          <div class="form-group">
            <label for="status">Statut</label>
            <select id="status" name="status">
              <?php
                $statuses = ['upcoming'=>'À venir','ongoing'=>'En cours','completed'=>'Terminé','cancelled'=>'Annulé'];
                foreach ($statuses as $val => $lbl):
                  $sel = (isset($event['status']) && $event['status'] === $val) ? 'selected' : '';
                  if (!isset($event) && $val === 'upcoming') $sel = 'selected';
              ?>
                <option value="<?= $val ?>" <?= $sel ?>><?= $lbl ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Upload image -->
          <div class="form-group full">
            <label>Image de l'événement <?= !isset($event) ? '<span class="req">*</span>' : '(optionnel — laisser vide pour garder l\'image actuelle)' ?></label>

            <div class="upload-zone" id="uploadZone">
              <input type="file" name="image" id="imageInput" accept="image/jpeg,image/png,image/gif,image/webp">
              <div class="upload-icon">🖼️</div>
              <div class="upload-label"><strong>Cliquez pour choisir</strong> ou glissez-déposez une image</div>
              <div class="upload-hint">JPG, PNG, GIF, WEBP — max 5 Mo</div>
            </div>

            <!-- Current image (edit mode) -->
            <?php if (isset($event) && !empty($event['image_url'])): ?>
              <div style="margin-top:10px;font-size:.8rem;color:var(--muted);">Image actuelle :</div>
              <img id="imgPreview" class="img-preview"
                src="<?= htmlspecialchars($event['image_url']) ?>"
                alt="Aperçu" style="display:block;">
            <?php else: ?>
              <img id="imgPreview" class="img-preview" alt="Aperçu">
            <?php endif; ?>

            <div class="preview-name" id="previewName"></div>
            <span class="field-error" id="err-image">Veuillez choisir une image (JPG, PNG, GIF ou WEBP, max 5 Mo).</span>
          </div>

          <!-- En ligne ? -->
          <div class="form-group full">
            <div class="checkbox-wrap">
              <input type="checkbox" id="is_online" name="is_online" value="1"
                <?= (!empty($event['is_online'])) ? 'checked' : '' ?>>
              <label for="is_online">🌐 Événement en ligne</label>
            </div>
          </div>

        </div><!-- /.form-row -->

        <div class="form-footer">
          <a href="index.php?action=list" class="btn btn-secondary">Annuler</a>
          <button type="submit" class="btn btn-primary">
            <?= isset($event) ? '💾 Mettre à jour' : '✅ Créer l\'événement' ?>
          </button>
        </div>

      </form>
    </div>
  </div>

</div>

<script>
(function () {
  "use strict";

  var isEdit = <?= isset($event) ? 'true' : 'false' ?>;

  function show(id)  { var el = document.getElementById(id); if(el) el.classList.add('show'); }
  function hide(id)  { var el = document.getElementById(id); if(el) el.classList.remove('show'); }
  function markInvalid(el) { el.classList.add('invalid'); }
  function markValid(el)   { el.classList.remove('invalid'); }

  function validateField(id, errId, checkFn) {
    var el = document.getElementById(id);
    if (!el) return true;
    var val = el.value.trim();
    if (!checkFn(val)) { markInvalid(el); show(errId); return false; }
    markValid(el); hide(errId); return true;
  }

  var rules = [
    { id:'title',            err:'err-title',            fn: function(v){ return v.length>=3 && v.length<=180; } },
    { id:'description',      err:'err-description',      fn: function(v){ return v.length>=10; } },
    { id:'event_date',       err:'err-event_date',       fn: function(v){ return v.length>0; } },
    { id:'location',         err:'err-location',         fn: function(v){ return v.length>=2; } },
    { id:'organizer_name',   err:'err-organizer_name',   fn: function(v){ return v.length>=2; } },
    { id:'category_id',      err:'err-category_id',      fn: function(v){ return v!==''; } },
    { id:'max_participants', err:'err-max_participants',  fn: function(v){ var n=parseInt(v); return !isNaN(n)&&n>=1&&n<=10000; } }
  ];

  rules.forEach(function(r) {
    var el = document.getElementById(r.id);
    if (!el) return;
    ['blur','input'].forEach(function(ev){
      el.addEventListener(ev, function(){ validateField(r.id, r.err, r.fn); });
    });
  });

  /* ── Drag & drop + file preview ── */
  var zone      = document.getElementById('uploadZone');
  var fileInput = document.getElementById('imageInput');
  var preview   = document.getElementById('imgPreview');
  var prevName  = document.getElementById('previewName');

  zone.addEventListener('dragover',  function(e){ e.preventDefault(); zone.classList.add('dragover'); });
  zone.addEventListener('dragleave', function()  { zone.classList.remove('dragover'); });
  zone.addEventListener('drop', function(e) {
    e.preventDefault();
    zone.classList.remove('dragover');
    if (e.dataTransfer.files[0]) {
      fileInput.files = e.dataTransfer.files;
      showPreview(e.dataTransfer.files[0]);
    }
  });

  fileInput.addEventListener('change', function() {
    if (this.files[0]) showPreview(this.files[0]);
  });

  function showPreview(file) {
    var allowed = ['image/jpeg','image/png','image/gif','image/webp'];
    if (!allowed.includes(file.type) || file.size > 5*1024*1024) {
      show('err-image'); return;
    }
    hide('err-image');
    var reader = new FileReader();
    reader.onload = function(e) {
      preview.src = e.target.result;
      preview.style.display = 'block';
      prevName.textContent = '📎 ' + file.name;
      prevName.style.display = 'block';
    };
    reader.readAsDataURL(file);
  }

  /* ── Submit ── */
  document.getElementById('eventForm').addEventListener('submit', function(e) {
    var valid = true;
    rules.forEach(function(r){ if (!validateField(r.id, r.err, r.fn)) valid = false; });

    // Image required only on create
    if (!isEdit && !fileInput.files[0]) {
      show('err-image'); valid = false;
    }

    if (!valid) {
      e.preventDefault();
      var first = document.querySelector('.invalid, .field-error.show');
      if (first) first.scrollIntoView({ behavior:'smooth', block:'center' });
    }
  });

})();
</script>

</body>
</html>
