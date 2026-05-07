<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Workify Admin — <?= isset($event)?'Modifier':'Créer' ?> un Événement</title>
<?php require BASE_PATH . '/View/shared/_styles_admin.php'; ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
.form-wrap{max-width:760px;margin:0 auto;}
.form-card{background:var(--surface);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden;}
.form-header{background:linear-gradient(135deg,var(--primary),var(--primary-dark));color:#fff;padding:24px 28px;display:flex;align-items:center;gap:14px;}
.form-header-icon{font-size:2rem;}
.form-header h1{font-size:1.3rem;font-weight:700;}
.form-header p{font-size:.84rem;opacity:.8;margin-top:2px;}
.form-body{padding:28px;}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:18px;}
.form-group{display:flex;flex-direction:column;gap:5px;margin-bottom:18px;}
.form-group.full{grid-column:1/-1;}
label{font-size:.82rem;font-weight:600;color:var(--text);}label .req{color:var(--danger);}
input[type="text"],input[type="url"],input[type="number"],input[type="datetime-local"],select,textarea{width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:.88rem;font-family:inherit;color:var(--text);outline:none;transition:.2s;background:#fff;}
input:focus,select:focus,textarea:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(108,99,255,.08);}
textarea{resize:vertical;min-height:100px;}
.field-error{font-size:.76rem;color:var(--danger);margin-top:2px;display:none;}.field-error.show{display:block;}
input.invalid,select.invalid,textarea.invalid{border-color:var(--danger);}
.checkbox-wrap{display:flex;align-items:center;gap:10px;padding:8px 0;}
.checkbox-wrap input[type="checkbox"]{width:18px;height:18px;accent-color:var(--primary);cursor:pointer;}
.checkbox-wrap label{font-size:.88rem;font-weight:500;cursor:pointer;margin:0;}
.form-footer{display:flex;justify-content:flex-end;gap:10px;padding-top:16px;border-top:1px solid var(--border);}
.img-preview{margin-top:10px;max-height:160px;border-radius:8px;object-fit:cover;width:100%;display:none;border:1px solid var(--border);}
/* MAP PICKER */
.map-picker-section{border:1.5px solid var(--border);border-radius:10px;overflow:hidden;margin-bottom:18px;}
.map-picker-section.hidden{display:none;}
.map-picker-header{background:linear-gradient(135deg,#fef3c7,#fde68a);padding:12px 18px;display:flex;align-items:center;gap:10px;border-bottom:1px solid #fcd34d;}
.map-picker-header h4{font-size:.88rem;font-weight:700;color:#92400e;}
.map-picker-header p{font-size:.76rem;color:#b45309;margin-top:1px;}
#pickerMap{width:100%;height:280px;}
.map-toolbar{padding:10px 14px;background:#fffbeb;border-top:1px solid #fde68a;display:flex;gap:8px;flex-wrap:wrap;align-items:center;}
.btn-geocode{display:inline-flex;align-items:center;gap:5px;padding:7px 14px;background:var(--primary);color:#fff;border:none;border-radius:6px;font-size:.8rem;font-weight:600;cursor:pointer;transition:.2s;}
.btn-geocode:hover{background:var(--primary-dark);}.btn-geocode:disabled{background:var(--muted);cursor:not-allowed;}
.coords-disp{font-size:.78rem;color:var(--muted);flex:1;}
.map-hint{font-size:.74rem;color:#b45309;background:#fef9c3;padding:5px 10px;border-radius:5px;width:100%;}
@media(max-width:560px){.form-row{grid-template-columns:1fr;}.form-body{padding:20px;}#pickerMap{height:200px;}}
</style>
</head>
<body>
<div class="layout">
  <?php $activeModule = 'events'; require BASE_PATH . '/View/shared/_sidebar.php'; ?>
  <main class="main">
    <div class="topbar">
      <h1 class="page-title"><?= isset($event)?'Modifier un':'Créer un' ?> <span>Événement</span></h1>
      <div class="topbar-actions">
        <a href="index.php?action=list" class="btn btn-ghost">← Retour à la liste</a>
      </div>
    </div>
    <div class="page-content">
      <div class="form-wrap">
        <div class="form-card">
          <div class="form-header">
            <span class="form-header-icon">📅</span>
            <div>
              <h1><?= isset($event)?'Modifier l\'événement':'Nouvel événement' ?></h1>
              <p><?= isset($event)?'Mettez à jour les informations ci-dessous.':'Remplissez le formulaire pour créer un événement.' ?></p>
            </div>
          </div>
          <div class="form-body">
            <form id="eventForm" method="POST"
                  action="index.php?action=<?= isset($event)?'update':'store' ?>"
                  novalidate>
              <?php if(isset($event)): ?>
                <input type="hidden" name="id" value="<?= $event['id'] ?>">
              <?php endif; ?>
              <!-- Champs GPS cachés -->
              <input type="hidden" id="latitude"  name="latitude"  value="<?= htmlspecialchars($event['latitude']  ?? '') ?>">
              <input type="hidden" id="longitude" name="longitude" value="<?= htmlspecialchars($event['longitude'] ?? '') ?>">

              <div class="form-row">
                <div class="form-group full">
                  <label for="title">Titre <span class="req">*</span></label>
                  <input type="text" id="title" name="title" placeholder="Ex: Workshop Laravel & MVC" value="<?= htmlspecialchars($event['title']??'') ?>">
                  <span class="field-error" id="err-title">Le titre est obligatoire (3 à 180 caractères).</span>
                </div>
                <div class="form-group full">
                  <label for="description">Description <span class="req">*</span></label>
                  <textarea id="description" name="description" placeholder="Décrivez l'événement…"><?= htmlspecialchars($event['description']??'') ?></textarea>
                  <span class="field-error" id="err-description">La description est obligatoire (min. 10 caractères).</span>
                </div>
                <div class="form-group">
                  <label for="event_date">Date & heure <span class="req">*</span></label>
                  <input type="datetime-local" id="event_date" name="event_date" value="<?= isset($event['event_date'])?date('Y-m-d\TH:i',strtotime($event['event_date'])):'' ?>">
                  <span class="field-error" id="err-event_date">Date obligatoire.</span>
                </div>
                <div class="form-group">
                  <label for="location">Lieu <span class="req">*</span></label>
                  <input type="text" id="location" name="location" placeholder="Ex: Tunis, Centre de conférence…" value="<?= htmlspecialchars($event['location']??'') ?>">
                  <span class="field-error" id="err-location">Le lieu est obligatoire.</span>
                </div>
                <div class="form-group">
                  <label for="category_id">Catégorie <span class="req">*</span></label>
                  <select id="category_id" name="category_id">
                    <option value="">— Choisir —</option>
                    <?php foreach($categories??[] as $cat): $sel=(isset($event['event_category_id'])&&$event['event_category_id']==$cat['id'])?'selected':''; ?>
                      <option value="<?= $cat['id'] ?>" <?= $sel ?>><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <span class="field-error" id="err-category_id">Catégorie obligatoire.</span>
                </div>
                <div class="form-group">
                  <label for="max_participants">Participants max <span class="req">*</span></label>
                  <input type="number" id="max_participants" name="max_participants" min="1" max="10000" value="<?= htmlspecialchars($event['max_participants']??'50') ?>">
                  <span class="field-error" id="err-max_participants">Nombre entre 1 et 10 000.</span>
                </div>
                <div class="form-group">
                  <label for="status">Statut <span class="req">*</span></label>
                  <select id="status" name="status">
                    <?php foreach(['upcoming'=>'À venir','ongoing'=>'En cours','completed'=>'Terminé','cancelled'=>'Annulé'] as $val=>$lbl): $sel=(isset($event['status'])&&$event['status']===$val)?'selected':((!isset($event)&&$val==='upcoming')?'selected':''); ?>
                      <option value="<?= $val ?>" <?= $sel ?>><?= $lbl ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="organizer_id">ID Organisateur <span class="req">*</span></label>
                  <input type="number" id="organizer_id" name="organizer_id" min="1" placeholder="Ex: 1" value="<?= htmlspecialchars($event['organizer_id']??'1') ?>">
                  <span class="field-error" id="err-organizer_id">ID invalide.</span>
                </div>
                <div class="form-group full">
                  <label for="image_url">URL de l'image (optionnel)</label>
                  <input type="url" id="image_url" name="image_url" placeholder="https://example.com/image.jpg" value="<?= htmlspecialchars($event['image_url']??'') ?>">
                  <img id="imgPreview" class="img-preview" src="<?= htmlspecialchars($event['image_url']??'') ?>" alt="Aperçu">
                </div>
                <div class="form-group full">
                  <div class="checkbox-wrap">
                    <input type="checkbox" id="is_online" name="is_online" value="1" <?= !empty($event['is_online'])?'checked':'' ?>>
                    <label for="is_online">🌐 Événement en ligne</label>
                  </div>
                </div>

                <!-- ════ CARTE GPS (présentiel uniquement) ════ -->
                <div class="form-group full">
                  <div class="map-picker-section <?= !empty($event['is_online']) ? 'hidden' : '' ?>" id="mapPickerSection">
                    <div class="map-picker-header">
                      <span style="font-size:1.3rem;">🗺️</span>
                      <div>
                        <h4>Localisation GPS — Événement Présentiel</h4>
                        <p>Cliquez sur la carte ou géolocalisez l'adresse. Faites glisser le marqueur pour affiner.</p>
                      </div>
                    </div>
                    <div id="pickerMap"></div>
                    <div class="map-toolbar">
                      <button type="button" id="geocodeBtn" class="btn-geocode">🔍 Géolocaliser l'adresse</button>
                      <span class="coords-disp" id="coordsDisplay">
                        <?php if(!empty($event['latitude'])&&!empty($event['longitude'])): ?>
                          📌 <?= round($event['latitude'],5) ?>, <?= round($event['longitude'],5) ?>
                        <?php else: ?>
                          Aucune coordonnée enregistrée.
                        <?php endif; ?>
                      </span>
                      <span class="map-hint">💡 Cliquez sur la carte pour placer le marqueur — faites-le glisser pour ajuster précisément.</span>
                    </div>
                  </div>
                </div>

              </div>
              <div class="form-footer">
                <a href="index.php?action=list" class="btn btn-ghost">Annuler</a>
                <button type="submit" class="btn btn-primary"><?= isset($event)?'💾 Mettre à jour':'✅ Créer l\'événement' ?></button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function(){
  var initLat = <?= (!empty($event['latitude']))  ? json_encode((float)$event['latitude'])  : 'null' ?>;
  var initLng = <?= (!empty($event['longitude'])) ? json_encode((float)$event['longitude']) : 'null' ?>;

  /* Validation */
  var rules=[
    {id:'title',err:'err-title',fn:function(v){return v.length>=3&&v.length<=180;}},
    {id:'description',err:'err-description',fn:function(v){return v.length>=10;}},
    {id:'event_date',err:'err-event_date',fn:function(v){return v.length>0;}},
    {id:'location',err:'err-location',fn:function(v){return v.length>=2;}},
    {id:'category_id',err:'err-category_id',fn:function(v){return v!=='';}},
    {id:'max_participants',err:'err-max_participants',fn:function(v){var n=parseInt(v);return !isNaN(n)&&n>=1&&n<=10000;}},
    {id:'organizer_id',err:'err-organizer_id',fn:function(v){var n=parseInt(v);return !isNaN(n)&&n>=1;}}
  ];
  function vf(id,err,fn){var el=document.getElementById(id);if(!el)return true;var v=el.value.trim();if(!fn(v)){el.classList.add('invalid');document.getElementById(err).classList.add('show');return false;}el.classList.remove('invalid');document.getElementById(err).classList.remove('show');return true;}
  rules.forEach(function(r){var el=document.getElementById(r.id);if(!el)return;['blur','input'].forEach(function(ev){el.addEventListener(ev,function(){vf(r.id,r.err,r.fn);});});});

  /* Image preview */
  var imgInput=document.getElementById('image_url'),preview=document.getElementById('imgPreview');
  if(imgInput){imgInput.addEventListener('input',function(){var u=this.value.trim();if(u){preview.src=u;preview.style.display='block';}else preview.style.display='none';});if(imgInput.value.trim()) preview.style.display='block';}

  /* is_online toggle */
  var chk=document.getElementById('is_online');
  var sec=document.getElementById('mapPickerSection');
  chk.addEventListener('change',function(){
    if(this.checked){sec.classList.add('hidden');}
    else{sec.classList.remove('hidden');if(pickerMap) setTimeout(function(){pickerMap.invalidateSize();},150);}
  });

  /* Carte Leaflet */
  var pickerMap=null, pickerMarker=null;
  var latIn=document.getElementById('latitude'), lngIn=document.getElementById('longitude');

  function initMap(){
    if(pickerMap) return;
    var sLat=initLat||36.8, sLng=initLng||10.18, sZ=initLat?15:7;
    pickerMap=L.map('pickerMap').setView([sLat,sLng],sZ);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{
      attribution:'© <a href="https://openstreetmap.org/copyright">OpenStreetMap</a>',maxZoom:19
    }).addTo(pickerMap);
    if(initLat) placeMarker(initLat,initLng,false);
    pickerMap.on('click',function(e){placeMarker(e.latlng.lat,e.latlng.lng,false);});
  }

  function makeIcon(){return L.divIcon({className:'',html:'<div style="background:#6c63ff;width:28px;height:28px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.3);"></div>',iconSize:[28,28],iconAnchor:[14,28],popupAnchor:[0,-30]});}

  function placeMarker(lat,lng,approx){
    if(pickerMarker) pickerMap.removeLayer(pickerMarker);
    pickerMarker=L.marker([lat,lng],{icon:makeIcon(),draggable:true}).addTo(pickerMap);
    pickerMarker.bindPopup('<b style="color:#6c63ff;">📌 Position</b><br><small>'+lat.toFixed(6)+', '+lng.toFixed(6)+'</small>'+(approx?'<br><em style="font-size:.7rem;color:#f59e0b;">⚠️ Approximatif</em>':'<br><em style="font-size:.7rem;color:#22c55e;">✅ Déplacez pour affiner</em>')).openPopup();
    pickerMarker.on('dragend',function(e){var p=e.target.getLatLng();setCoords(p.lat,p.lng);});
    setCoords(lat,lng);
  }

  function setCoords(lat,lng){
    latIn.value=lat.toFixed(7); lngIn.value=lng.toFixed(7);
    var d=document.getElementById('coordsDisplay');
    if(d) d.textContent='📌 '+lat.toFixed(5)+', '+lng.toFixed(5);
  }

  document.getElementById('geocodeBtn').addEventListener('click',function(){
    var loc=document.getElementById('location').value.trim();
    if(!loc){alert('Saisissez d\'abord le champ "Lieu".');return;}
    var btn=this; btn.disabled=true; btn.textContent='⏳ Recherche…';
    fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&q='+encodeURIComponent(loc),{headers:{'Accept-Language':'fr'}})
      .then(function(r){return r.json();})
      .then(function(data){
        btn.disabled=false; btn.textContent='🔍 Géolocaliser l\'adresse';
        if(!data||!data.length){alert('Adresse introuvable.');return;}
        var lat=parseFloat(data[0].lat),lng=parseFloat(data[0].lon);
        if(!pickerMap) initMap();
        pickerMap.setView([lat,lng],15);
        placeMarker(lat,lng,true);
      })
      .catch(function(){btn.disabled=false;btn.textContent='🔍 Géolocaliser l\'adresse';alert('Erreur réseau.');});
  });

  if(!chk.checked) initMap();

  document.getElementById('eventForm').addEventListener('submit',function(e){
    var ok=true;
    rules.forEach(function(r){if(!vf(r.id,r.err,r.fn))ok=false;});
    if(!ok){e.preventDefault();var f=document.querySelector('.invalid');if(f)f.scrollIntoView({behavior:'smooth',block:'center'});}
  });
})();
</script>
</body>
</html>
