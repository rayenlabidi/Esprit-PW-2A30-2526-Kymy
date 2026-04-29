<?php
/**
 * View/shared/_nav.php
 * Barre de navigation partagée côté public.
 * Variable attendue : $activeNav (string)
 */
$activeNav = $activeNav ?? 'events';
?>
<nav class="pub-nav">
  <a href="index.php" class="pub-brand">Work<span>ify</span></a>
  <div class="pub-nav-links">
    <a href="index.php" class="<?= $activeNav==='events'?'active':'' ?>">📅 Événements</a>
    <a href="#" class="<?= $activeNav==='jobs'?'active':'' ?>">💼 Jobs</a>
    <a href="#" class="<?= $activeNav==='formations'?'active':'' ?>">🎓 Formations</a>
    <div class="pub-nav-sep"></div>
    <a href="index.php?action=create" class="pub-btn-create">＋ Créer un événement</a>
    <a href="../admin/index.php" class="pub-btn-admin" target="_blank" title="Interface admin">⚙️</a>
  </div>
</nav>
<style>
.pub-nav {
  background: #fff;
  border-bottom: 1px solid #e5e7eb;
  padding: 0 32px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 64px;
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: 0 2px 12px rgba(108,99,255,.07);
}
.pub-brand { font-size: 1.4rem; font-weight: 800; color: #6c63ff; text-decoration: none; }
.pub-brand span { color: #ff6584; }
.pub-nav-links { display: flex; gap: 6px; align-items: center; }
.pub-nav-links a { text-decoration: none; color: #6b7280; font-size: .88rem; font-weight: 500; transition: .18s; padding: 6px 10px; border-radius: 7px; }
.pub-nav-links a:hover, .pub-nav-links a.active { color: #6c63ff; background: #f0eeff; }
.pub-nav-sep { width: 1px; height: 20px; background: #e5e7eb; margin: 0 6px; }
.pub-btn-create { background: #6c63ff !important; color: #fff !important; padding: 8px 16px !important; border-radius: 8px !important; font-weight: 700 !important; }
.pub-btn-create:hover { background: #574fd6 !important; }
.pub-btn-admin { background: #f4f3ff !important; color: #6c63ff !important; border: 1.5px solid #ddd8ff !important; padding: 7px 12px !important; border-radius: 8px !important; font-size:.95rem !important; }
.pub-btn-admin:hover { background: #6c63ff !important; color: #fff !important; }
@media(max-width:600px) {
  .pub-nav { padding: 0 16px; }
  .pub-nav-links a:not(.pub-btn-create):not(.pub-btn-admin) { display: none; }
}
</style>
