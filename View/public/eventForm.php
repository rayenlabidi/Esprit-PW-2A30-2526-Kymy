<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Workify — <?= isset($event) ? 'Modifier' : 'Créer' ?> un Événement</title>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
  *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
  :root{--primary:#6c63ff;--primary-dark:#574fd6;--accent:#ff6584;--danger:#ef4444;--bg:#f8f7ff;--surface:#fff;--border:#e5e7eb;--text:#1e1b4b;--muted:#6b7280;--radius:14px;--shadow:0 4px 24px rgba(108,99,255,.1);}
  body{font-family:'Segoe UI',system-ui,sans-serif;background:var(--bg);color:var(--text);min-height:100vh;}
  nav{background:var(--surface);border-bottom:1px solid var(--border);padding:0 32px;display:flex;align-items:center;justify-content:space-between;height:64px;position:sticky;top:0;z-index:400;box-shadow:0 2px 8px rgba(108,99,255,.06);}
  .brand{font-size:1.4rem;font-weight:800;color:var(--primary);text-decoration:none;}.brand span{color:var(--accent);}
  .nav-links{display:flex;gap:24px;align-items:center;}.nav-links a{text-decoration:none;color:var(--muted);font-size:.9rem;font-weight:500;transition:.2s;}.nav-links a:hover{color:var(--primary);}
  .page{max-width:760px;margin:40px auto;padding:0 24px 60px;}
  .breadcrumb{font-size:.82rem;color:var(--muted);margin-bottom:24px;display:flex;align-items:center;gap:6px;}.breadcrumb a{color:var(--primary);text-decoration:none;}
  .form-card{background:var(--surface);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden;}
  .form-header{background:linear-gradient(135deg,var(--primary),var(--primary-dark));color:#fff;padding:28px 32px;display:flex;align-items:center;gap:16px;}
  .form-header-icon{font-size:2rem;}.form-header h1{font-size:1.4rem;font-weight:700;}.form-header p{font-size:.88rem;opacity:.8;margin-top:3px;}
  .form-body{padding:32px;}
  .form-row{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
  .form-group{display:flex;flex-direction:column;gap:6px;margin-bottom:20px;}.form-group.full{grid-column:1/-1;}
  label{font-size:.85rem;font-weight:600;color:var(--text);}label .req{color:var(--danger);}
  input[type="text"],input[type="number"],input[type="datetime-local"],select,textarea{width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:.9rem;font-family:inherit;color:var(--text);outline:none;transition:.2s;background:#fff;}
  input:focus,select:focus,textarea:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(108,99,255,.1);}
  textarea{resize:vertical;min-height:110px;}
  .upload-zone{border:2px dashed var(--border);border-radius:10px;padding:28px 20px;text-align:center;cursor:pointer;transition:.2s;position:relative;background:#fafafe;}
  .upload-zone:hover,.upload-zone.dragover{border-color:var(--primary);background:#f0eeff;}
  .upload-zone input[type="file"]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;}
  .upload-icon{font-size:2.2rem;margin-bottom:8px;}.upload-label{font-size:.88rem;color:var(--muted);}.upload-label strong{color:var(--primary);}.upload-hint{font-size:.75rem;color:var(--muted);margin-top:4px;}
  .img-preview{margin-top:14px;width:100%;max-height:200px;object-fit:cover;border-radius:8px;border:1px solid var(--border);display:none;}
  .preview-name{margin-top:8px;font-size:.8rem;color:var(--primary);font-weight:600;display:none;text-align:center;}
  .field-error{font-size:.78rem;color:var(--danger);margin-top:2px;display:none;}.field-error.show{display:block;}
  input.invalid,select.invalid,textarea.invalid{border-color:var(--danger);}
  .checkbox-wrap{display:flex;align-items:center;gap:10px;padding:10px 0;}
  .checkbox-wrap input[type="checkbox"]{width:18px;height:18px;accent-color:var(--primary);cursor:pointer;}
  .checkbox-wrap label{font-size:.9rem;font-weight:500;cursor:pointer;}
  .form-footer{display:flex;justify-content:flex-end;gap:12px;padding-top:8px;border-top:1px solid var(--border);margin-top:8px;}
  .btn{display:inline-flex;align-items:center;gap:6px;padding:11px 24px;border-radius:8px;font-size:.9rem;font-weight:600;cursor:pointer;text-decoration:none;border:none;transition:.2s;}
  .btn-primary{background:var(--primary);color:#fff;}.btn-primary:hover{background:var(--primary-dark);}
  .btn-secondary{background:#f3f4f6;color:var(--muted);}.btn-secondary:hover{background:#e5e7eb;}

  /* MAP PICKER */
  .map-picker-section{border:1.5px solid var(--border);border-radius:12px;overflow:hidden;transition:.3s;}
  .map-picker-section.hidden{display:none;}
  .map-picker-header{background:linear-gradient(135deg,#fef3c7,#fde68a);padding:14px 20px;display:flex;align-items:center;gap:10px;border-bottom:1px solid #fcd34d;}
  .map-picker-header h4{font-size:.9rem;font-weight:700;color:#92400e;}
  .map-picker-header p{font-size:.78rem;color:#b45309;margin-top:2px;}
  #pickerMap{width:100%;height:300px;}
  .map-picker-toolbar{padding:12px 16px;background:#fffbeb;border-top:1px solid #fde68a;display:flex;gap:8px;flex-wrap:wrap;align-items:center;}
  .btn-geocode{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:var(--primary);color:#fff;border:none;border-radius:7px;font-size:.82rem;font-weight:600;cursor:pointer;transition:.2s;}
  .btn-geocode:hover{background:var(--primary-dark);}
  .btn-geocode:disabled{background:var(--muted);cursor:not-allowed;}
  .coords-display{font-size:.8rem;color:var(--muted);flex:1;}
  .coords-display strong{color:var(--text);}
  .map-hint{font-size:.76rem;color:#b45309;background:#fef9c3;padding:6px 12px;border-radius:6px;width:100%;}

  @media(max-width:560px){.form-row{grid-template-columns:1fr;}.form-body{padding:20px;}.form-header{padding:20px;}#pickerMap{height:220px;}}
</style>
</head>
<body>

<?php $activeNav = 'events'; require BASE_PATH . '/View/shared/_nav.php'; ?>

<div class="page">
  <div class="breadcrumb">
    <a href="index.php">Événements</a><span>›</span>
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
      <form id="eventForm" method="POST"
            action="index.php?action=<?= isset($event) ? 'update' : 'store' ?>"
            enctype="multipart/form-data" novalidate>

        <?php if (isset($event)): ?>
          <input type="hidden" name="id" value="<?= $event['id'] ?>">
          <input type="hidden" name="existing_image_url" value="<?= htmlspecialchars($event['image_url']??'') ?>">
        <?php endif; ?>

        <!-- Champs GPS cachés — remplis par la carte -->
        <input type="hidden" id="latitude"  name="latitude"  value="<?= htmlspecialchars($event['latitude']  ?? '') ?>">
        <input type="hidden" id="longitude" name="longitude" value="<?= htmlspecialchars($event['longitude'] ?? '') ?>">

        <div class="form-row">

          <div class="form-group full">
            <label for="title">Titre <span class="req">*</span></label>
            <input type="text" id="title" name="title" placeholder="Ex: Workshop Laravel & MVC"
              value="<?= htmlspecialchars($event['title']??'') ?>">
            <span class="field-error" id="err-title">Le titre est obligatoire (3 à 180 caractères).</span>
          </div>

          <div class="form-group full">
            <label for="description">Description <span class="req">*</span></label>
            <textarea id="description" name="description" placeholder="Décrivez l'événement en détail…"><?= htmlspecialchars($event['description']??'') ?></textarea>
            <span class="field-error" id="err-description">La description est obligatoire (min. 10 caractères).</span>
          </div>

          <div class="form-group">
            <label for="event_date">Date & heure <span class="req">*</span></label>
            <input type="datetime-local" id="event_date" name="event_date"
              value="<?= isset($event['event_date']) ? date('Y-m-d\TH:i', strtotime($event['event_date'])) : '' ?>">
            <span class="field-error" id="err-event_date">Veuillez choisir une date valide.</span>
          </div>

          <div class="form-group">
            <label for="location">Lieu <span class="req">*</span></label>
            <input type="text" id="location" name="location"
              placeholder="Ex: Tunis, Centre de conférence…"
              value="<?= htmlspecialchars($event['location']??'') ?>">
            <span class="field-error" id="err-location">Le lieu est obligatoire.</span>
          </div>

          <div class="form-group">
            <label for="organizer_name">Nom de l'entreprise <span class="req">*</span></label>
            <input type="text" id="organizer_name" name="organizer_name"
              placeholder="Ex: TechCorp, StartupXYZ…"
              value="<?= htmlspecialchars($event['organizer_name']??'') ?>">
            <span class="field-error" id="err-organizer_name">Le nom de l'entreprise est obligatoire.</span>
          </div>

          <div class="form-group">
            <label for="category_id">Catégorie <span class="req">*</span></label>
            <select id="category_id" name="category_id">
              <option value="">— Choisir —</option>
              <?php foreach ($categories??[] as $cat):
                $sel = (isset($event['category_id']) && $event['category_id']==$cat['id']) ? 'selected' : ''; ?>
                <option value="<?= $cat['id'] ?>" <?= $sel ?>><?= htmlspecialchars($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
            <span class="field-error" id="err-category_id">Veuillez choisir une catégorie.</span>
          </div>

          <div class="form-group">
            <label for="max_participants">Participants max <span class="req">*</span></label>
            <input type="number" id="max_participants" name="max_participants"
              min="1" max="10000" placeholder="50"
              value="<?= htmlspecialchars($event['max_participants']??'50') ?>">
            <span class="field-error" id="err-max_participants">Nombre entre 1 et 10 000.</span>
          </div>

          <div class="form-group">
            <label for="status">Statut</label>
            <select id="status" name="status">
              <?php foreach (['upcoming'=>'À venir','ongoing'=>'En cours','completed'=>'Terminé','cancelled'=>'Annulé'] as $val=>$lbl):
                $sel = (isset($event['status'])&&$event['status']===$val)?'selected':((!isset($event)&&$val==='upcoming')?'selected':''); ?>
                <option value="<?= $val ?>" <?= $sel ?>><?= $lbl ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group full">
            <label>Image de l'événement <?= !isset($event) ? '<span class="req">*</span>' : '(optionnel)' ?></label>
            <div class="upload-zone" id="uploadZone">
              <input type="file" name="image" id="imageInput" accept="image/jpeg,image/png,image/gif,image/webp">
              <div class="upload-icon">🖼️</div>
              <div class="upload-label"><strong>Cliquez pour choisir</strong> ou glissez-déposez une image</div>
              <div class="upload-hint">JPG, PNG, GIF, WEBP — max 5 Mo</div>
            </div>
            <?php if (isset($event) && !empty($event['image_url'])): ?>
              <div style="margin-top:10px;font-size:.8rem;color:var(--muted);">Image actuelle :</div>
              <img id="imgPreview" class="img-preview" src="<?= htmlspecialchars($event['image_url']) ?>" alt="Aperçu" style="display:block;">
            <?php else: ?>
              <img id="imgPreview" class="img-preview" alt="Aperçu">
            <?php endif; ?>
            <div class="preview-name" id="previewName"></div>
            <span class="field-error" id="err-image">Veuillez choisir une image (JPG, PNG, GIF ou WEBP, max 5 Mo).</span>
          </div>

          <!-- Événement en ligne -->
          <div class="form-group full">
            <div class="checkbox-wrap">
              <input type="checkbox" id="is_online" name="is_online" value="1"
                <?= !empty($event['is_online']) ? 'checked' : '' ?>>
              <label for="is_online">🌐 Événement en ligne</label>
            </div>
          </div>

          <!-- ════ CARTE GPS (présentiel uniquement) ════ -->
          <div class="form-group full map-picker-section <?= !empty($event['is_online']) ? 'hidden' : '' ?>" id="mapPickerSection">
            <div class="map-picker-header">
              <span style="font-size:1.4rem;">🗺️</span>
              <div>
                <h4>Localisation GPS de l'événement</h4>
                <p>Cliquez sur la carte pour placer le marqueur, ou utilisez « Géolocaliser l'adresse ».</p>
              </div>
            </div>
            <div id="pickerMap"></div>
            <div class="map-picker-toolbar">
              <button type="button" id="geocodeBtn" class="btn-geocode">🔍 Géolocaliser l'adresse</button>
              <span class="coords-display" id="coordsDisplay">
                <?php if (!empty($event['latitude']) && !empty($event['longitude'])): ?>
                  <strong>📌 <?= round($event['latitude'],5) ?>, <?= round($event['longitude'],5) ?></strong>
                <?php else: ?>
                  Aucune coordonnée — cliquez sur la carte ou géolocalisez.
                <?php endif; ?>
              </span>
              <span class="map-hint">💡 Vous pouvez aussi cliquer directement sur la carte pour affiner la position.</span>
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

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function(){
  "use strict";
  var isEdit = <?= isset($event) ? 'true' : 'false' ?>;
  var initLat = <?= (!empty($event['latitude']))  ? json_encode((float)$event['latitude'])  : 'null' ?>;
  var initLng = <?= (!empty($event['longitude'])) ? json_encode((float)$event['longitude']) : 'null' ?>;

  /* ── Validation ── */
  function show(id){ var e=document.getElementById(id); if(e) e.classList.add('show'); }
  function hide(id){ var e=document.getElementById(id); if(e) e.classList.remove('show'); }
  function vf(id,err,fn){ var el=document.getElementById(id); if(!el) return true; var v=el.value.trim(); if(!fn(v)){el.classList.add('invalid');show(err);return false;} el.classList.remove('invalid');hide(err);return true; }
  var rules=[
    {id:'title',           err:'err-title',           fn:function(v){return v.length>=3&&v.length<=180;}},
    {id:'description',     err:'err-description',     fn:function(v){return v.length>=10;}},
    {id:'event_date',      err:'err-event_date',      fn:function(v){return v.length>0;}},
    {id:'location',        err:'err-location',        fn:function(v){return v.length>=2;}},
    {id:'organizer_name',  err:'err-organizer_name',  fn:function(v){return v.length>=2;}},
    {id:'category_id',     err:'err-category_id',     fn:function(v){return v!=='';}},
    {id:'max_participants',err:'err-max_participants', fn:function(v){var n=parseInt(v);return !isNaN(n)&&n>=1&&n<=10000;}}
  ];
  rules.forEach(function(r){
    var el=document.getElementById(r.id); if(!el) return;
    ['blur','input'].forEach(function(ev){ el.addEventListener(ev,function(){ vf(r.id,r.err,r.fn); }); });
  });

  /* ── Upload preview ── */
  var zone=document.getElementById('uploadZone'),fi=document.getElementById('imageInput'),
      prev=document.getElementById('imgPreview'),pn=document.getElementById('previewName');
  zone.addEventListener('dragover',function(e){e.preventDefault();zone.classList.add('dragover');});
  zone.addEventListener('dragleave',function(){zone.classList.remove('dragover');});
  zone.addEventListener('drop',function(e){e.preventDefault();zone.classList.remove('dragover');if(e.dataTransfer.files[0]){fi.files=e.dataTransfer.files;showPrev(e.dataTransfer.files[0]);}});
  fi.addEventListener('change',function(){if(this.files[0]) showPrev(this.files[0]);});
  function showPrev(f){var ok=['image/jpeg','image/png','image/gif','image/webp'];if(!ok.includes(f.type)||f.size>5*1024*1024){show('err-image');return;}hide('err-image');var r=new FileReader();r.onload=function(e){prev.src=e.target.result;prev.style.display='block';pn.textContent='📎 '+f.name;pn.style.display='block';};r.readAsDataURL(f);}

  /* ── is_online toggle ── */
  var chk = document.getElementById('is_online');
  var sec = document.getElementById('mapPickerSection');
  chk.addEventListener('change', function(){
    if (this.checked) { sec.classList.add('hidden'); }
    else              { sec.classList.remove('hidden'); if (pickerMap) setTimeout(function(){ pickerMap.invalidateSize(); },150); }
  });

  /* ── Carte Leaflet (sélecteur GPS) ── */
  var pickerMap = null, pickerMarker = null;
  var latInput = document.getElementById('latitude');
  var lngInput = document.getElementById('longitude');

  function initMap(){
    if (pickerMap) return;
    var startLat = initLat || 36.8;
    var startLng = initLng || 10.18;
    var startZoom= initLat ? 15 : 7;

    pickerMap = L.map('pickerMap').setView([startLat, startLng], startZoom);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{
      attribution:'© <a href="https://openstreetmap.org/copyright">OpenStreetMap</a> contributors',
      maxZoom:19
    }).addTo(pickerMap);

    /* Si coordonnées existantes → placer marqueur */
    if (initLat) placeMarker(initLat, initLng, false);

    /* Clic sur la carte → placer marqueur */
    pickerMap.on('click', function(e){ placeMarker(e.latlng.lat, e.latlng.lng, false); });
  }

  function makeIcon(){
    return L.divIcon({
      className:'',
      html:'<div style="background:#6c63ff;width:30px;height:30px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.3);"></div>',
      iconSize:[30,30],iconAnchor:[15,30],popupAnchor:[0,-32]
    });
  }

  function placeMarker(lat, lng, approx){
    if (pickerMarker) pickerMap.removeLayer(pickerMarker);
    pickerMarker = L.marker([lat,lng],{icon:makeIcon(),draggable:true}).addTo(pickerMap);
    pickerMarker.bindPopup(
      '<b style="color:#6c63ff;">📌 Position sélectionnée</b><br>' +
      '<small>' + lat.toFixed(6) + ', ' + lng.toFixed(6) + '</small>' +
      (approx ? '<br><em style="font-size:.72rem;color:#f59e0b;">⚠️ Approximatif — déplacez pour affiner</em>' : '<br><em style="font-size:.72rem;color:#22c55e;">✅ Faites glisser pour ajuster</em>')
    ).openPopup();
    /* Drag du marqueur → mise à jour des champs */
    pickerMarker.on('dragend', function(e){
      var pos = e.target.getLatLng();
      updateCoords(pos.lat, pos.lng);
    });
    updateCoords(lat, lng);
  }

  function updateCoords(lat, lng){
    latInput.value = lat.toFixed(7);
    lngInput.value = lng.toFixed(7);
    var d = document.getElementById('coordsDisplay');
    if (d) d.innerHTML = '<strong>📌 ' + lat.toFixed(5) + ', ' + lng.toFixed(5) + '</strong>';
  }

  /* ── Bouton Géolocaliser (Nominatim) ── */
  document.getElementById('geocodeBtn').addEventListener('click', function(){
    var locVal = document.getElementById('location').value.trim();
    if (!locVal){ alert('Saisissez d\'abord le champ "Lieu".'); return; }
    var btn = this;
    btn.disabled = true;
    btn.textContent = '⏳ Recherche…';
    fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' +
          encodeURIComponent(locVal),
          { headers: { 'Accept-Language': 'fr' } })
      .then(function(r){ return r.json(); })
      .then(function(data){
        btn.disabled = false; btn.textContent = '🔍 Géolocaliser l\'adresse';
        if (!data || !data.length){
          alert('Adresse introuvable. Essayez une formulation plus précise.'); return;
        }
        var lat = parseFloat(data[0].lat), lng = parseFloat(data[0].lon);
        if (!pickerMap) initMap();
        pickerMap.setView([lat, lng], 15);
        placeMarker(lat, lng, true);
      })
      .catch(function(){
        btn.disabled = false; btn.textContent = '🔍 Géolocaliser l\'adresse';
        alert('Erreur réseau. Vérifiez votre connexion.');
      });
  });

  /* ── Init carte si présentiel au chargement ── */
  if (!chk.checked) initMap();

  /* ── Submit ── */
  document.getElementById('eventForm').addEventListener('submit', function(e){
    var ok = true;
    rules.forEach(function(r){ if(!vf(r.id,r.err,r.fn)) ok=false; });
    if (!isEdit && !fi.files[0]){ show('err-image'); ok=false; }
    if (!ok){ e.preventDefault(); var f=document.querySelector('.invalid,.field-error.show'); if(f) f.scrollIntoView({behavior:'smooth',block:'center'}); }
  });
})();
</script>
</body>
</html>
