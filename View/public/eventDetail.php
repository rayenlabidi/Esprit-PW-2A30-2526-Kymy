<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Workify — <?= htmlspecialchars($event['title'] ?? 'Événement') ?></title>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
  *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
  :root{--primary:#6c63ff;--primary-dark:#574fd6;--accent:#ff6584;--success:#22c55e;--danger:#ef4444;--info:#3b82f6;--bg:#f8f7ff;--surface:#fff;--border:#e5e7eb;--text:#1e1b4b;--muted:#6b7280;--radius:14px;--shadow:0 4px 24px rgba(108,99,255,.09);}
  body{font-family:'Segoe UI',system-ui,sans-serif;background:var(--bg);color:var(--text);min-height:100vh;}
  nav{background:var(--surface);border-bottom:1px solid var(--border);padding:0 32px;display:flex;align-items:center;justify-content:space-between;height:64px;position:sticky;top:0;z-index:400;box-shadow:0 2px 8px rgba(108,99,255,.06);}
  .brand{font-size:1.4rem;font-weight:800;color:var(--primary);text-decoration:none;}
  .brand span{color:var(--accent);}
  .nav-links{display:flex;gap:24px;align-items:center;}
  .nav-links a{text-decoration:none;color:var(--muted);font-size:.9rem;font-weight:500;transition:.2s;}
  .nav-links a:hover{color:var(--primary);}
  .event-hero{width:100%;height:320px;background:linear-gradient(135deg,var(--primary),var(--primary-dark));display:flex;align-items:center;justify-content:center;font-size:5rem;overflow:hidden;}
  .event-hero img{width:100%;height:100%;object-fit:cover;}
  .container{max-width:860px;margin:0 auto;padding:40px 24px;}
  .breadcrumb{font-size:.82rem;color:var(--muted);margin-bottom:24px;display:flex;align-items:center;gap:6px;}
  .breadcrumb a{color:var(--primary);text-decoration:none;}
  .detail-card{background:var(--surface);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden;}
  .detail-body{padding:36px;}
  .event-header{margin-bottom:24px;}
  .event-category{font-size:.8rem;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;}
  .event-title{font-size:1.9rem;font-weight:800;line-height:1.3;margin-bottom:12px;}
  .badge-row{display:flex;gap:8px;flex-wrap:wrap;}
  .badge{display:inline-block;padding:4px 14px;border-radius:50px;font-size:.78rem;font-weight:700;}
  .badge-upcoming{background:#dbeafe;color:#1d4ed8;}.badge-ongoing{background:#dcfce7;color:#15803d;}
  .badge-completed{background:#f3f4f6;color:#6b7280;}.badge-cancelled{background:#fee2e2;color:#b91c1c;}
  .badge-online{background:#ede9fe;color:#7c3aed;}.badge-onsite{background:#fef3c7;color:#b45309;}
  .info-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:16px;background:#f5f3ff;border-radius:var(--radius);padding:24px;margin:28px 0;}
  .info-item{display:flex;flex-direction:column;gap:4px;}
  .info-label{font-size:.75rem;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.5px;}
  .info-value{font-size:.95rem;font-weight:600;color:var(--text);}
  .section-title{font-size:1rem;font-weight:700;margin-bottom:12px;color:var(--text);}
  .event-description{font-size:.93rem;line-height:1.75;color:var(--muted);white-space:pre-line;}

  /* MAP */
  .map-section{margin-top:32px;border-radius:var(--radius);overflow:hidden;border:1px solid var(--border);box-shadow:0 2px 12px rgba(108,99,255,.08);}
  .map-header{background:linear-gradient(135deg,#fef3c7,#fde68a);padding:16px 24px;display:flex;align-items:center;gap:12px;border-bottom:1px solid #fcd34d;}
  .map-header h3{font-size:1rem;font-weight:700;color:#92400e;}
  .map-header p{font-size:.82rem;color:#b45309;margin-top:2px;}
  #eventMap{width:100%;height:380px;background:#e8e0f0;}
  .map-actions{padding:14px 20px;background:#fffbeb;border-top:1px solid #fde68a;display:flex;gap:10px;flex-wrap:wrap;align-items:center;}
  .btn-map{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:8px;font-size:.85rem;font-weight:600;text-decoration:none;transition:.2s;cursor:pointer;border:none;}
  .btn-gmaps{background:#4285F4;color:#fff;}.btn-gmaps:hover{background:#3367d6;}
  .btn-osm{background:#7ebc6f;color:#fff;}.btn-osm:hover{background:#5ea84e;}
  .btn-copy{background:#f3f4f6;color:var(--text);border:1.5px solid var(--border);}.btn-copy:hover{background:#e5e7eb;}

  /* CTA */
  .cta-section{margin-top:32px;padding:28px;background:linear-gradient(135deg,#ede9fe,#dbeafe);border-radius:var(--radius);display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;}
  .cta-text h3{font-size:1.05rem;font-weight:700;margin-bottom:4px;}
  .cta-text p{font-size:.85rem;color:var(--muted);}
  .btn-register{display:inline-flex;align-items:center;gap:8px;padding:13px 28px;background:var(--primary);color:#fff;border-radius:10px;font-size:.95rem;font-weight:700;text-decoration:none;white-space:nowrap;transition:.2s;}
  .btn-register:hover{background:var(--primary-dark);}
  .btn-register.disabled{background:var(--muted);cursor:not-allowed;}
  .btn-back{display:inline-flex;align-items:center;gap:6px;padding:9px 20px;border-radius:8px;background:var(--surface);border:1.5px solid var(--border);color:var(--muted);font-size:.88rem;font-weight:600;text-decoration:none;transition:.2s;margin-bottom:24px;}
  .btn-back:hover{border-color:var(--primary);color:var(--primary);}
  footer{text-align:center;padding:40px 24px;color:var(--muted);font-size:.82rem;border-top:1px solid var(--border);margin-top:40px;}
  @media(max-width:600px){.event-title{font-size:1.4rem;}.detail-body{padding:20px;}.cta-section{flex-direction:column;}#eventMap{height:260px;}}
</style>
</head>
<body>

<?php $activeNav = 'events'; require BASE_PATH . '/View/shared/_nav.php'; ?>

<div class="event-hero">
  <?php if (!empty($event['image_url'])): ?>
    <img src="<?= htmlspecialchars($event['image_url']) ?>" alt="<?= htmlspecialchars($event['title']) ?>">
  <?php else: ?>📅<?php endif; ?>
</div>

<div class="container">
  <div class="breadcrumb">
    <a href="index.php">Événements</a><span>›</span>
    <span><?= htmlspecialchars($event['title']) ?></span>
  </div>
  <a href="index.php" class="btn-back">← Retour aux événements</a>

  <div class="detail-card">
    <div class="detail-body">

      <div class="event-header">
        <div class="event-category"><?= htmlspecialchars($event['category_name']) ?></div>
        <h1 class="event-title"><?= htmlspecialchars($event['title']) ?></h1>
        <div class="badge-row">
          <span class="badge badge-<?= $event['status'] ?>"><?= ucfirst($event['status']) ?></span>
          <span class="badge <?= $event['is_online'] ? 'badge-online' : 'badge-onsite' ?>">
            <?= $event['is_online'] ? '🌐 En ligne' : '📍 Présentiel' ?>
          </span>
        </div>
      </div>

      <div class="info-grid">
        <div class="info-item">
          <span class="info-label">📅 Date & heure</span>
          <span class="info-value"><?= date('d/m/Y à H:i', strtotime($event['event_date'])) ?></span>
        </div>
        <div class="info-item">
          <span class="info-label">📍 Lieu</span>
          <span class="info-value"><?= htmlspecialchars($event['location']) ?></span>
        </div>
        <div class="info-item">
          <span class="info-label">👤 Organisateur</span>
          <span class="info-value"><?= htmlspecialchars($event['organizer_name']) ?></span>
        </div>
        <div class="info-item">
          <span class="info-label">👥 Places max</span>
          <span class="info-value"><?= $event['max_participants'] ?> participants</span>
        </div>
      </div>

      <div>
        <div class="section-title">À propos de l'événement</div>
        <p class="event-description"><?= htmlspecialchars($event['description']) ?></p>
      </div>

      <?php if (!$event['is_online']): ?>
      <?php
        $hasCoords = isset($event['latitude'], $event['longitude'])
                     && $event['latitude'] !== null && $event['latitude'] !== ''
                     && $event['longitude'] !== null && $event['longitude'] !== '';
        $lat = $hasCoords ? (float)$event['latitude']  : null;
        $lng = $hasCoords ? (float)$event['longitude'] : null;
        $locEnc = urlencode($event['location']);
      ?>
      <div class="map-section">
        <div class="map-header">
          <span style="font-size:1.6rem;">🗺️</span>
          <div>
            <h3>Localisation de l'événement</h3>
            <p><?= htmlspecialchars($event['location']) ?></p>
          </div>
        </div>
        <div id="eventMap"></div>
        <div class="map-actions">
          <span id="coordsDisplay" style="font-size:.82rem;color:var(--muted);flex:1;">
            <?= $hasCoords ? '📌 '.round($lat,5).', '.round($lng,5) : '🔍 Localisation en cours…' ?>
          </span>
          <a id="gmapsBtn" href="https://www.google.com/maps/<?= $hasCoords ? '?q='.$lat.','.$lng : 'search/'.$locEnc ?>"
             target="_blank" rel="noopener" class="btn-map btn-gmaps">🗺️ Google Maps</a>
          <a id="osmBtn" href="https://www.openstreetmap.org/<?= $hasCoords ? '?mlat='.$lat.'&mlon='.$lng.'&zoom=16' : 'search?query='.$locEnc ?>"
             target="_blank" rel="noopener" class="btn-map btn-osm">🌍 OpenStreetMap</a>
          <button id="copyBtn" onclick="copyCoords()" class="btn-map btn-copy" <?= $hasCoords ? '' : 'style="display:none;"' ?>>📋 Copier coords</button>
        </div>
      </div>
      <?php endif; ?>

      <?php $canRegister = in_array($event['status'], ['upcoming','ongoing']); ?>
      <div class="cta-section">
        <div class="cta-text">
          <h3><?= $canRegister ? 'Inscrivez-vous maintenant !' : 'Inscriptions fermées' ?></h3>
          <p>
            <?php if ($canRegister): ?>Places limitées — <?= $event['max_participants'] ?> participants maximum.
            <?php else: ?>Cet événement est <?= $event['status']==='completed' ? 'terminé' : 'annulé' ?>.<?php endif; ?>
          </p>
        </div>
        <?php if ($canRegister): ?>
          <a href="#" class="btn-register">✅ S'inscrire</a>
        <?php else: ?>
          <span class="btn-register disabled">Indisponible</span>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>

<footer>© <?= date('Y') ?> Workify — Tous droits réservés.</footer>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<?php if (!$event['is_online']): ?>
<script>
(function(){
  var mapEl = document.getElementById('eventMap');
  if (!mapEl) return;

  var KNOWN_LAT  = <?= $hasCoords ? json_encode($lat) : 'null' ?>;
  var KNOWN_LNG  = <?= $hasCoords ? json_encode($lng) : 'null' ?>;
  var LOC_QUERY  = <?= json_encode($event['location']) ?>;

  /* Icône marqueur violet Workify */
  function makeIcon() {
    return L.divIcon({
      className:'',
      html:'<div style="background:#6c63ff;width:36px;height:36px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #fff;box-shadow:0 3px 12px rgba(0,0,0,.3);"></div>',
      iconSize:[36,36], iconAnchor:[18,36], popupAnchor:[0,-38]
    });
  }

  function buildPopup(lat, lng, name, approx) {
    return '<div style="min-width:200px;font-family:Segoe UI,sans-serif;">' +
      '<strong style="font-size:.95rem;color:#6c63ff;">📍 ' + name + '</strong><br>' +
      '<span style="font-size:.8rem;color:#6b7280;">' + lat.toFixed(6) + ', ' + lng.toFixed(6) + '</span>' +
      (approx ? '<br><em style="font-size:.75rem;color:#f59e0b;">⚠️ Position approximative</em>' : '') +
      '</div>';
  }

  function placeMarker(map, lat, lng, name, approx) {
    map.setView([lat, lng], 15);
    L.marker([lat, lng], { icon: makeIcon() })
      .addTo(map)
      .bindPopup(buildPopup(lat, lng, name, approx), { maxWidth: 280 })
      .openPopup();
    window._eventCoords = { lat: lat, lng: lng };
    /* mettre à jour les boutons */
    var g = document.getElementById('gmapsBtn');
    var o = document.getElementById('osmBtn');
    var c = document.getElementById('copyBtn');
    var d = document.getElementById('coordsDisplay');
    if (g) g.href = 'https://www.google.com/maps?q=' + lat + ',' + lng;
    if (o) o.href = 'https://www.openstreetmap.org/?mlat=' + lat + '&mlon=' + lng + '&zoom=16';
    if (c) c.style.display = '';
    if (d) d.textContent = '📌 ' + lat.toFixed(5) + ', ' + lng.toFixed(5) + (approx ? ' (approximatif)' : '');
  }

  var defaultView = KNOWN_LAT ? [KNOWN_LAT, KNOWN_LNG] : [36.8, 10.18];
  var defaultZoom = KNOWN_LAT ? 15 : 7;

  var map = L.map('eventMap', { scrollWheelZoom: false }).setView(defaultView, defaultZoom);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    maxZoom: 19
  }).addTo(map);

  mapEl.addEventListener('click',     function(){ map.scrollWheelZoom.enable(); });
  mapEl.addEventListener('mouseleave',function(){ map.scrollWheelZoom.disable(); });

  if (KNOWN_LAT !== null) {
    /* Coordonnées enregistrées en base */
    placeMarker(map, KNOWN_LAT, KNOWN_LNG, LOC_QUERY, false);
  } else {
    /* Géocodage automatique via Nominatim (OSM) — aucune clé API requise */
    var loadingMarker = L.marker([36.8, 10.18])
      .addTo(map)
      .bindPopup('<em>🔍 Géolocalisation de l\'adresse…</em>')
      .openPopup();

    fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' +
          encodeURIComponent(LOC_QUERY),
          { headers: { 'Accept-Language': 'fr' } })
      .then(function(r){ return r.json(); })
      .then(function(data){
        map.removeLayer(loadingMarker);
        if (!data || !data.length) {
          var d = document.getElementById('coordsDisplay');
          if (d) d.textContent = '❌ Adresse introuvable — vérifiez le champ Lieu.';
          L.popup({ maxWidth:260 })
            .setLatLng([36.8,10.18])
            .setContent('<b>❌</b> Adresse "<em>' + LOC_QUERY + '</em>" introuvable.')
            .openOn(map);
          return;
        }
        placeMarker(map, parseFloat(data[0].lat), parseFloat(data[0].lon), LOC_QUERY, true);
      })
      .catch(function(){
        map.removeLayer(loadingMarker);
        var d = document.getElementById('coordsDisplay');
        if (d) d.textContent = '❌ Erreur de géolocalisation.';
      });
  }
})();

function copyCoords(){
  if (!window._eventCoords) return;
  var t = window._eventCoords.lat.toFixed(7) + ', ' + window._eventCoords.lng.toFixed(7);
  navigator.clipboard.writeText(t).then(function(){
    var b = document.getElementById('copyBtn');
    if (b){ b.textContent='✅ Copié !'; setTimeout(function(){ b.textContent='📋 Copier coords'; },2000); }
  });
}
</script>
<?php endif; ?>

</body>
</html>
