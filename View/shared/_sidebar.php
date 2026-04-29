<?php
/**
 * View/shared/_sidebar.php
 * Barre latérale partagée côté admin.
 * Variables attendues : $activeModule (string), $activeAction (string)
 */
$activeModule = $activeModule ?? 'events';
$activeAction = $activeAction ?? 'list';

function sidebarLink(string $href, string $icon, string $label, bool $active): string {
    $cls = $active ? 'nav-item active' : 'nav-item';
    return "<a href=\"$href\" class=\"$cls\">$icon <span>$label</span></a>";
}
?>
<aside class="sidebar">
  <div class="sidebar-brand">
    <a href="index.php" style="color:inherit;text-decoration:none;">Work<span>ify</span></a>
    <div class="sidebar-role">Administration</div>
  </div>

  <div class="sidebar-section-title">Général</div>
  <?= sidebarLink('index.php?module=dashboard', '📊', 'Tableau de bord', $activeModule === 'dashboard') ?>

  <div class="sidebar-section-title">Gestion</div>
  <?= sidebarLink('index.php?module=events&action=list', '📅', 'Événements', $activeModule === 'events') ?>
  <?= sidebarLink('index.php?module=categories&action=list', '📁', 'Catégories', $activeModule === 'categories') ?>

  <div class="sidebar-section-title">Liens</div>
  <a href="../public/index.php" class="nav-item" target="_blank">🌐 <span>Vue utilisateur</span></a>

  <div class="sidebar-footer">
    <span>Admin Panel v2.0</span>
  </div>
</aside>
