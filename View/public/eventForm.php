<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Workify - <?= isset($event) ? 'Modifier' : 'Creer' ?> un evenement</title>
<link rel="stylesheet" href="assets/workify-template.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
  .field-error{display:none}.field-error.show{display:block}.invalid,.field-invalid{border-color:rgba(220,38,38,.5)!important;box-shadow:0 0 0 4px rgba(220,38,38,.08)!important}
  .upload-zone{position:relative;cursor:pointer}.upload-zone input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer}.img-preview,.preview-name{display:none}
  .img-preview{width:100%;max-height:240px;object-fit:cover;border-radius:18px;border:1px solid var(--line);margin-top:14px}
  .map-picker-section.hidden{display:none}#pickerMap{width:100%;height:320px;border-radius:18px}.map-picker-toolbar{margin-top:14px}
</style>
</head>
<body>

<?php $activeNav = 'events'; require BASE_PATH . '/View/shared/_nav.php'; ?>

<main class="page-shell">
  <div class="container">
    <div class="page-head">
      <div>
        <span class="eyebrow">Evenements</span>
        <h1><?= isset($event) ? 'Modifier l\'evenement' : 'Nouvel evenement' ?></h1>
        <p><?= isset($event) ? 'Mettez a jour les informations ci-dessous.' : 'Remplissez le formulaire pour creer un evenement.' ?></p>
      </div>
      <a href="index.php?action=list" class="btn btn-outline">Retour</a>
    </div>

    <section class="section-card form-card">
      <form id="eventForm" class="stack-form" method="POST"
            action="index.php?action=<?= isset($event) ? 'update' : 'store' ?>"
            enctype="multipart/form-data" novalidate>

        <?php if (isset($event)): ?>
          <input type="hidden" name="id" value="<?= $event['id'] ?>">
          <input type="hidden" name="existing_image_url" value="<?= htmlspecialchars($event['image_url'] ?? '') ?>">
        <?php endif; ?>

        <input type="hidden" id="latitude" name="latitude" value="<?= htmlspecialchars($event['latitude'] ?? '') ?>">
        <input type="hidden" id="longitude" name="longitude" value="<?= htmlspecialchars($event['longitude'] ?? '') ?>">

        <div class="form-grid">
          <div class="form-group" style="grid-column:1/-1;">
            <label for="title">Titre <span class="req">*</span></label>
            <input class="form-control" type="text" id="title" name="title" placeholder="Ex: Workshop Laravel & MVC" value="<?= htmlspecialchars($event['title'] ?? '') ?>">
            <span class="field-error" id="err-title">Le titre est obligatoire (3 a 180 caracteres).</span>
          </div>

          <div class="form-group" style="grid-column:1/-1;">
            <label for="description">Description <span class="req">*</span></label>
            <textarea class="form-control" id="description" name="description" placeholder="Decrivez l'evenement en detail..."><?= htmlspecialchars($event['description'] ?? '') ?></textarea>
            <span class="field-error" id="err-description">La description est obligatoire (min. 10 caracteres).</span>
          </div>

          <div class="form-group">
            <label for="event_date">Date & heure <span class="req">*</span></label>
            <input class="form-control" type="datetime-local" id="event_date" name="event_date" value="<?= isset($event['event_date']) ? date('Y-m-d\TH:i', strtotime($event['event_date'])) : '' ?>">
            <span class="field-error" id="err-event_date">Veuillez choisir une date valide.</span>
          </div>

          <div class="form-group">
            <label for="location">Lieu <span class="req">*</span></label>
            <input class="form-control" type="text" id="location" name="location" placeholder="Ex: Tunis, centre de conference" value="<?= htmlspecialchars($event['location'] ?? '') ?>">
            <span class="field-error" id="err-location">Le lieu est obligatoire.</span>
          </div>

          <div class="form-group">
            <label for="organizer_name">Nom de l'entreprise <span class="req">*</span></label>
            <input class="form-control" type="text" id="organizer_name" name="organizer_name" placeholder="Ex: TechCorp" value="<?= htmlspecialchars($event['organizer_name'] ?? '') ?>">
            <span class="field-error" id="err-organizer_name">Le nom de l'entreprise est obligatoire.</span>
          </div>

          <div class="form-group">
            <label for="category_id">Categorie <span class="req">*</span></label>
            <select class="form-control" id="category_id" name="category_id">
              <option value="">Choisir</option>
              <?php foreach ($categories ?? [] as $cat): ?>
                <?php $sel = (isset($event['category_id']) && $event['category_id'] == $cat['id']) ? 'selected' : ''; ?>
                <option value="<?= $cat['id'] ?>" <?= $sel ?>><?= htmlspecialchars($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
            <span class="field-error" id="err-category_id">Veuillez choisir une categorie.</span>
          </div>

          <div class="form-group">
            <label for="max_participants">Participants max <span class="req">*</span></label>
            <input class="form-control" type="number" id="max_participants" name="max_participants" min="1" max="10000" value="<?= htmlspecialchars($event['max_participants'] ?? '50') ?>">
            <span class="field-error" id="err-max_participants">Nombre entre 1 et 10 000.</span>
          </div>

          <div class="form-group">
            <label for="status">Statut</label>
            <select class="form-control" id="status" name="status">
              <?php foreach (['upcoming'=>'A venir','ongoing'=>'En cours','completed'=>'Termine','cancelled'=>'Annule'] as $val=>$lbl): ?>
                <?php $sel = (isset($event['status']) && $event['status'] === $val) ? 'selected' : ((!isset($event) && $val === 'upcoming') ? 'selected' : ''); ?>
                <option value="<?= $val ?>" <?= $sel ?>><?= $lbl ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="apply-box upload-zone" id="uploadZone">
          <strong>Image de l'evenement <?= !isset($event) ? '*' : '(optionnel)' ?></strong>
          <p class="muted">Cliquez pour choisir ou glissez-deposez une image JPG, PNG, GIF ou WEBP.</p>
          <input type="file" name="image" id="imageInput" accept="image/jpeg,image/png,image/gif,image/webp">
        </div>
        <?php if (isset($event) && !empty($event['image_url'])): ?>
          <img id="imgPreview" class="img-preview" src="<?= htmlspecialchars($event['image_url']) ?>" alt="Apercu" style="display:block;">
        <?php else: ?>
          <img id="imgPreview" class="img-preview" alt="Apercu">
        <?php endif; ?>
        <div class="preview-name" id="previewName"></div>
        <span class="field-error" id="err-image">Veuillez choisir une image valide, max 5 Mo.</span>

        <label class="checkbox-row">
          <input type="checkbox" id="is_online" name="is_online" value="1" <?= !empty($event['is_online']) ? 'checked' : '' ?>>
          Evenement en ligne
        </label>

        <section class="section-card map-picker-section <?= !empty($event['is_online']) ? 'hidden' : '' ?>" id="mapPickerSection">
          <div class="section-head">
            <div>
              <span class="eyebrow">Localisation</span>
              <h2>Position GPS</h2>
              <p>Cliquez sur la carte pour placer le marqueur, ou geolocalisez l'adresse.</p>
            </div>
          </div>
          <div id="pickerMap"></div>
          <div class="map-picker-toolbar chip-row">
            <button type="button" id="geocodeBtn" class="btn btn-primary btn-small">Geolocaliser l'adresse</button>
            <span class="muted" id="coordsDisplay">
              <?php if (!empty($event['latitude']) && !empty($event['longitude'])): ?>
                <?= round($event['latitude'], 5) ?>, <?= round($event['longitude'], 5) ?>
              <?php else: ?>
                Aucune coordonnee.
              <?php endif; ?>
            </span>
          </div>
        </section>

        <div class="card-actions">
          <a href="index.php?action=list" class="btn btn-outline">Annuler</a>
          <button type="submit" class="btn btn-primary"><?= isset($event) ? 'Mettre a jour' : 'Creer l\'evenement' ?></button>
        </div>
      </form>
    </section>
  </div>
</main>

<?php require BASE_PATH . '/View/shared/_footer.php'; ?>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="assets/workify-template.js"></script>
<script>
(function(){
  "use strict";
  var isEdit = <?= isset($event) ? 'true' : 'false' ?>;
  var initLat = <?= (!empty($event['latitude'])) ? json_encode((float)$event['latitude']) : 'null' ?>;
  var initLng = <?= (!empty($event['longitude'])) ? json_encode((float)$event['longitude']) : 'null' ?>;
  function show(id){ var e=document.getElementById(id); if(e) e.classList.add('show'); }
  function hide(id){ var e=document.getElementById(id); if(e) e.classList.remove('show'); }
  function vf(id,err,fn){ var el=document.getElementById(id); if(!el) return true; var v=el.value.trim(); if(!fn(v)){el.classList.add('invalid');show(err);return false;} el.classList.remove('invalid');hide(err);return true; }
  var rules=[
    {id:'title',err:'err-title',fn:function(v){return v.length>=3&&v.length<=180;}},
    {id:'description',err:'err-description',fn:function(v){return v.length>=10;}},
    {id:'event_date',err:'err-event_date',fn:function(v){return v.length>0;}},
    {id:'location',err:'err-location',fn:function(v){return v.length>=2;}},
    {id:'organizer_name',err:'err-organizer_name',fn:function(v){return v.length>=2;}},
    {id:'category_id',err:'err-category_id',fn:function(v){return v!=='';}},
    {id:'max_participants',err:'err-max_participants',fn:function(v){var n=parseInt(v,10);return !isNaN(n)&&n>=1&&n<=10000;}}
  ];
  rules.forEach(function(r){ var el=document.getElementById(r.id); if(el) ['blur','input'].forEach(function(ev){ el.addEventListener(ev,function(){ vf(r.id,r.err,r.fn); }); }); });
  var zone=document.getElementById('uploadZone'),fi=document.getElementById('imageInput'),prev=document.getElementById('imgPreview'),pn=document.getElementById('previewName');
  zone.addEventListener('dragover',function(e){e.preventDefault();});
  zone.addEventListener('drop',function(e){e.preventDefault();if(e.dataTransfer.files[0]){fi.files=e.dataTransfer.files;showPrev(e.dataTransfer.files[0]);}});
  fi.addEventListener('change',function(){if(this.files[0]) showPrev(this.files[0]);});
  function showPrev(f){var ok=['image/jpeg','image/png','image/gif','image/webp'];if(ok.indexOf(f.type)===-1||f.size>5*1024*1024){show('err-image');return;}hide('err-image');var r=new FileReader();r.onload=function(e){prev.src=e.target.result;prev.style.display='block';pn.textContent=f.name;pn.style.display='block';};r.readAsDataURL(f);}
  var chk=document.getElementById('is_online'),sec=document.getElementById('mapPickerSection');
  chk.addEventListener('change',function(){ if(this.checked){sec.classList.add('hidden');}else{sec.classList.remove('hidden'); if(pickerMap) setTimeout(function(){pickerMap.invalidateSize();},150);} });
  var pickerMap=null,pickerMarker=null,latInput=document.getElementById('latitude'),lngInput=document.getElementById('longitude');
  function initMap(){ if(pickerMap) return; var startLat=initLat||36.8,startLng=initLng||10.18,startZoom=initLat?15:7; pickerMap=L.map('pickerMap').setView([startLat,startLng],startZoom); L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'OpenStreetMap',maxZoom:19}).addTo(pickerMap); if(initLat) placeMarker(initLat,initLng); pickerMap.on('click',function(e){placeMarker(e.latlng.lat,e.latlng.lng);}); }
  function placeMarker(lat,lng){ if(pickerMarker) pickerMap.removeLayer(pickerMarker); pickerMarker=L.marker([lat,lng],{draggable:true}).addTo(pickerMap); pickerMarker.on('dragend',function(e){var p=e.target.getLatLng();updateCoords(p.lat,p.lng);}); updateCoords(lat,lng); }
  function updateCoords(lat,lng){ latInput.value=lat.toFixed(7); lngInput.value=lng.toFixed(7); var d=document.getElementById('coordsDisplay'); if(d) d.textContent=lat.toFixed(5)+', '+lng.toFixed(5); }
  document.getElementById('geocodeBtn').addEventListener('click',function(){ var locVal=document.getElementById('location').value.trim(); if(!locVal){alert('Saisissez d\'abord le champ Lieu.');return;} var btn=this; btn.disabled=true; btn.textContent='Recherche...'; fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&q='+encodeURIComponent(locVal),{headers:{'Accept-Language':'fr'}}).then(function(r){return r.json();}).then(function(data){btn.disabled=false;btn.textContent='Geolocaliser l\'adresse'; if(!data||!data.length){alert('Adresse introuvable.');return;} var lat=parseFloat(data[0].lat),lng=parseFloat(data[0].lon); if(!pickerMap) initMap(); pickerMap.setView([lat,lng],15); placeMarker(lat,lng);}).catch(function(){btn.disabled=false;btn.textContent='Geolocaliser l\'adresse';alert('Erreur reseau.');}); });
  if(!chk.checked) initMap();
  document.getElementById('eventForm').addEventListener('submit',function(e){ var ok=true; rules.forEach(function(r){if(!vf(r.id,r.err,r.fn)) ok=false;}); if(!isEdit&&!fi.files[0]){show('err-image');ok=false;} if(!ok){e.preventDefault(); var f=document.querySelector('.invalid,.field-error.show'); if(f) f.scrollIntoView({behavior:'smooth',block:'center'});} });
})();
</script>
</body>
</html>
