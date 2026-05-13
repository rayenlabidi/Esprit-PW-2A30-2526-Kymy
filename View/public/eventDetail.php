<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Workify - <?= htmlspecialchars($event['title'] ?? 'Evenement') ?></title>
<link rel="stylesheet" href="assets/workify-template.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>#eventMap{width:100%;height:380px;border-radius:18px}.map-section{margin-top:24px}</style>
</head>
<body>

<?php $activeNav = 'events'; require BASE_PATH . '/View/shared/_nav.php'; ?>

<main class="page-shell">
  <div class="container">
    <a href="index.php" class="ghost-link">Retour aux evenements</a>

    <section class="detail-card" style="margin-top:18px;overflow:hidden;">
      <div class="detail-banner" style="background-image:url('<?= htmlspecialchars(!empty($event['image_url']) ? $event['image_url'] : 'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=1400&q=80') ?>');"></div>
      <div class="detail-content">
        <span class="eyebrow"><?= htmlspecialchars($event['category_name']) ?></span>
        <h1><?= htmlspecialchars($event['title']) ?></h1>
        <div class="chip-row">
          <span class="badge badge-info"><?= ucfirst($event['status']) ?></span>
          <span class="badge <?= $event['is_online'] ? 'badge-success' : 'badge-warning' ?>"><?= $event['is_online'] ? 'En ligne' : 'Presentiel' ?></span>
        </div>

        <div class="detail-grid">
          <div><span class="stat-label">Date & heure</span><strong><?= date('d/m/Y H:i', strtotime($event['event_date'])) ?></strong></div>
          <div><span class="stat-label">Lieu</span><strong><?= htmlspecialchars($event['location']) ?></strong></div>
          <div><span class="stat-label">Organisateur</span><strong><?= htmlspecialchars($event['organizer_name']) ?></strong></div>
          <div><span class="stat-label">Places max</span><strong><?= (int)$event['max_participants'] ?></strong></div>
        </div>

        <section>
          <h2>A propos de l'evenement</h2>
          <p><?= nl2br(htmlspecialchars($event['description'])) ?></p>
        </section>

        <?php if (!$event['is_online']): ?>
          <?php
            $hasCoords = isset($event['latitude'], $event['longitude'])
              && $event['latitude'] !== null && $event['latitude'] !== ''
              && $event['longitude'] !== null && $event['longitude'] !== '';
            $lat = $hasCoords ? (float)$event['latitude'] : null;
            $lng = $hasCoords ? (float)$event['longitude'] : null;
            $locEnc = urlencode($event['location']);
          ?>
          <section class="apply-box map-section">
            <div class="section-head">
              <div>
                <span class="eyebrow">Localisation</span>
                <h2><?= htmlspecialchars($event['location']) ?></h2>
              </div>
            </div>
            <div id="eventMap"></div>
            <div class="card-actions" style="margin-top:16px;">
              <span id="coordsDisplay" class="muted">
                <?= $hasCoords ? round($lat,5).', '.round($lng,5) : 'Localisation en cours...' ?>
              </span>
              <a id="gmapsBtn" href="https://www.google.com/maps/<?= $hasCoords ? '?q='.$lat.','.$lng : 'search/'.$locEnc ?>" target="_blank" rel="noopener" class="btn btn-primary btn-small">Google Maps</a>
              <a id="osmBtn" href="https://www.openstreetmap.org/<?= $hasCoords ? '?mlat='.$lat.'&mlon='.$lng.'&zoom=16' : 'search?query='.$locEnc ?>" target="_blank" rel="noopener" class="btn btn-outline btn-small">OpenStreetMap</a>
              <button id="copyBtn" onclick="copyCoords()" class="btn btn-outline btn-small" <?= $hasCoords ? '' : 'style="display:none;"' ?> type="button">Copier coords</button>
            </div>
          </section>
        <?php endif; ?>

        <?php $canRegister = in_array($event['status'], ['upcoming','ongoing'], true); ?>
        <section class="apply-box">
          <div class="section-head">
            <div>
              <h2><?= $canRegister ? 'Inscrivez-vous maintenant' : 'Inscriptions fermees' ?></h2>
              <p><?= $canRegister ? 'Places limitees - '.$event['max_participants'].' participants maximum.' : 'Cet evenement n\'est plus disponible.' ?></p>
            </div>
            <?php if ($canRegister): ?>
              <a href="#" class="btn btn-primary">S'inscrire</a>
            <?php else: ?>
              <span class="btn btn-outline">Indisponible</span>
            <?php endif; ?>
          </div>
        </section>
      </div>
    </section>
  </div>
</main>

<?php require BASE_PATH . '/View/shared/_footer.php'; ?>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<?php if (!$event['is_online']): ?>
<script>
(function(){
  var mapEl=document.getElementById('eventMap'); if(!mapEl) return;
  var knownLat=<?= $hasCoords ? json_encode($lat) : 'null' ?>;
  var knownLng=<?= $hasCoords ? json_encode($lng) : 'null' ?>;
  var locQuery=<?= json_encode($event['location']) ?>;
  var map=L.map('eventMap',{scrollWheelZoom:false}).setView(knownLat?[knownLat,knownLng]:[36.8,10.18],knownLat?15:7);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'OpenStreetMap',maxZoom:19}).addTo(map);
  function place(lat,lng,approx){
    map.setView([lat,lng],15);
    L.marker([lat,lng]).addTo(map).bindPopup(locQuery).openPopup();
    window._eventCoords={lat:lat,lng:lng};
    document.getElementById('gmapsBtn').href='https://www.google.com/maps?q='+lat+','+lng;
    document.getElementById('osmBtn').href='https://www.openstreetmap.org/?mlat='+lat+'&mlon='+lng+'&zoom=16';
    document.getElementById('copyBtn').style.display='';
    document.getElementById('coordsDisplay').textContent=lat.toFixed(5)+', '+lng.toFixed(5)+(approx?' (approximatif)':'');
  }
  if(knownLat!==null){ place(knownLat,knownLng,false); }
  else {
    fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&q='+encodeURIComponent(locQuery),{headers:{'Accept-Language':'fr'}})
      .then(function(r){return r.json();}).then(function(data){ if(data&&data.length) place(parseFloat(data[0].lat),parseFloat(data[0].lon),true); else document.getElementById('coordsDisplay').textContent='Adresse introuvable.'; })
      .catch(function(){document.getElementById('coordsDisplay').textContent='Erreur de geolocalisation.';});
  }
})();
function copyCoords(){
  if(!window._eventCoords) return;
  navigator.clipboard.writeText(window._eventCoords.lat.toFixed(7)+', '+window._eventCoords.lng.toFixed(7));
}
</script>
<?php endif; ?>
</body>
</html>
