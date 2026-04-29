<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Workify — <?= htmlspecialchars($event['title'] ?? 'Événement') ?></title>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --primary:      #6c63ff;
    --primary-dark: #574fd6;
    --accent:       #ff6584;
    --success:      #22c55e;
    --danger:       #ef4444;
    --info:         #3b82f6;
    --bg:           #f8f7ff;
    --surface:      #ffffff;
    --border:       #e5e7eb;
    --text:         #1e1b4b;
    --muted:        #6b7280;
    --radius:       14px;
    --shadow:       0 4px 24px rgba(108,99,255,.09);
  }
  body { font-family:'Segoe UI',system-ui,sans-serif; background:var(--bg); color:var(--text); min-height:100vh; }

  /* ── Nav ── */
  nav {
    background:var(--surface);
    border-bottom:1px solid var(--border);
    padding:0 32px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    height:64px;
    position:sticky;
    top:0;
    z-index:10;
    box-shadow:0 2px 8px rgba(108,99,255,.06);
  }
  .brand { font-size:1.4rem; font-weight:800; color:var(--primary); text-decoration:none; }
  .brand span { color:var(--accent); }
  .nav-links { display:flex; gap:24px; align-items:center; }
  .nav-links a { text-decoration:none; color:var(--muted); font-size:.9rem; font-weight:500; transition:.2s; }
  .nav-links a:hover { color:var(--primary); }

  /* ── Hero image ── */
  .event-hero {
    width:100%;
    height:320px;
    background:linear-gradient(135deg, var(--primary), var(--primary-dark));
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:5rem;
    overflow:hidden;
  }
  .event-hero img { width:100%; height:100%; object-fit:cover; }

  /* ── Layout ── */
  .container { max-width:860px; margin:0 auto; padding:40px 24px; }

  /* ── Breadcrumb ── */
  .breadcrumb { font-size:.82rem; color:var(--muted); margin-bottom:24px; display:flex; align-items:center; gap:6px; }
  .breadcrumb a { color:var(--primary); text-decoration:none; }
  .breadcrumb a:hover { text-decoration:underline; }

  /* ── Main card ── */
  .detail-card {
    background:var(--surface);
    border-radius:var(--radius);
    box-shadow:var(--shadow);
    overflow:hidden;
  }
  .detail-body { padding:36px; }

  /* ── Header ── */
  .event-header { margin-bottom:24px; }
  .event-category { font-size:.8rem; font-weight:700; color:var(--primary); text-transform:uppercase; letter-spacing:.5px; margin-bottom:8px; }
  .event-title { font-size:1.9rem; font-weight:800; line-height:1.3; margin-bottom:12px; }
  .badge-row { display:flex; gap:8px; flex-wrap:wrap; }
  .badge { display:inline-block; padding:4px 14px; border-radius:50px; font-size:.78rem; font-weight:700; }
  .badge-upcoming  { background:#dbeafe; color:#1d4ed8; }
  .badge-ongoing   { background:#dcfce7; color:#15803d; }
  .badge-completed { background:#f3f4f6; color:#6b7280; }
  .badge-cancelled { background:#fee2e2; color:#b91c1c; }
  .badge-online    { background:#ede9fe; color:#7c3aed; }
  .badge-onsite    { background:#fef3c7; color:#b45309; }

  /* ── Info grid ── */
  .info-grid {
    display:grid;
    grid-template-columns:repeat(auto-fill, minmax(180px, 1fr));
    gap:16px;
    background:#f5f3ff;
    border-radius:var(--radius);
    padding:24px;
    margin:28px 0;
  }
  .info-item { display:flex; flex-direction:column; gap:4px; }
  .info-label { font-size:.75rem; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:.5px; }
  .info-value { font-size:.95rem; font-weight:600; color:var(--text); }

  /* ── Description ── */
  .section-title { font-size:1rem; font-weight:700; margin-bottom:12px; color:var(--text); }
  .event-description { font-size:.93rem; line-height:1.75; color:var(--muted); white-space:pre-line; }

  /* ── CTA ── */
  .cta-section {
    margin-top:32px;
    padding:28px;
    background:linear-gradient(135deg, #ede9fe, #dbeafe);
    border-radius:var(--radius);
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    flex-wrap:wrap;
  }
  .cta-text h3 { font-size:1.05rem; font-weight:700; margin-bottom:4px; }
  .cta-text p  { font-size:.85rem; color:var(--muted); }
  .btn-register {
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:13px 28px;
    background:var(--primary);
    color:#fff;
    border-radius:10px;
    font-size:.95rem;
    font-weight:700;
    text-decoration:none;
    white-space:nowrap;
    transition:.2s;
  }
  .btn-register:hover { background:var(--primary-dark); }
  .btn-register:disabled, .btn-register.disabled { background:var(--muted); cursor:not-allowed; }

  /* ── Back ── */
  .btn-back {
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:9px 20px;
    border-radius:8px;
    background:var(--surface);
    border:1.5px solid var(--border);
    color:var(--muted);
    font-size:.88rem;
    font-weight:600;
    text-decoration:none;
    transition:.2s;
    margin-bottom:24px;
  }
  .btn-back:hover { border-color:var(--primary); color:var(--primary); }

  footer { text-align:center; padding:40px 24px; color:var(--muted); font-size:.82rem; border-top:1px solid var(--border); margin-top:40px; }

  @media(max-width:600px){
    .event-title { font-size:1.4rem; }
    .detail-body { padding:20px; }
    .cta-section { flex-direction:column; }
  }
</style>
</head>
<body>

<!-- ════ NAV ════ -->
<?php $activeNav = 'events'; require BASE_PATH . '/View/shared/_nav.php'; ?>

<!-- ════ HERO IMAGE ════ -->
<div class="event-hero">
  <?php if (!empty($event['image_url'])): ?>
    <img src="<?= htmlspecialchars($event['image_url']) ?>" alt="<?= htmlspecialchars($event['title']) ?>">
  <?php else: ?>
    📅
  <?php endif; ?>
</div>

<!-- ════ CONTENT ════ -->
<div class="container">

  <!-- Breadcrumb -->
  <div class="breadcrumb">
    <a href="index.php">Événements</a>
    <span>›</span>
    <span><?= htmlspecialchars($event['title']) ?></span>
  </div>

  <a href="index.php" class="btn-back">← Retour aux événements</a>

  <div class="detail-card">
    <div class="detail-body">

      <!-- Header -->
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

      <!-- Info grid -->
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

      <!-- Description -->
      <div>
        <div class="section-title">À propos de l'événement</div>
        <p class="event-description"><?= htmlspecialchars($event['description']) ?></p>
      </div>

      <!-- CTA -->
      <?php $canRegister = in_array($event['status'], ['upcoming', 'ongoing']); ?>
      <div class="cta-section">
        <div class="cta-text">
          <h3><?= $canRegister ? 'Inscrivez-vous maintenant !' : 'Inscriptions fermées' ?></h3>
          <p>
            <?php if ($canRegister): ?>
              Places limitées — <?= $event['max_participants'] ?> participants maximum.
            <?php else: ?>
              Cet événement est <?= $event['status'] === 'completed' ? 'terminé' : 'annulé' ?>.
            <?php endif; ?>
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

</body>
</html>
