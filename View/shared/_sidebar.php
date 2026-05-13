<?php
/**
 * Admin Workify sidebar shared by Events admin screens.
 */
$activeModule = $activeModule ?? 'events';

if (!function_exists('workifyAdminIcon')) {
    function workifyAdminIcon(string $path): string
    {
        return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="' . htmlspecialchars($path, ENT_QUOTES) . '"/></svg>';
    }
}

if (!function_exists('workifyAdminLink')) {
    function workifyAdminLink(string $key, string $href, string $label, string $icon, string $activeModule): string
    {
        $active = $activeModule === $key ? ' active' : '';
        return '<a class="nav-link' . $active . '" data-module="' . htmlspecialchars($key, ENT_QUOTES) . '" href="' . htmlspecialchars($href, ENT_QUOTES) . '">'
            . '<span class="nav-icon" aria-hidden="true">' . workifyAdminIcon($icon) . '</span>'
            . '<span>' . htmlspecialchars($label, ENT_QUOTES) . '</span>'
            . '</a>';
    }
}

$links = [
    ['dashboard', 'index.php?module=dashboard', 'Dashboard', 'M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z'],
    ['jobs', '#', 'Jobs', 'M10 4h4a2 2 0 0 1 2 2v2h4v12H4V8h4V6a2 2 0 0 1 2-2zm4 4V6h-4v2h4z'],
    ['publications', '../../feed/views/back/admin.php', 'Publication', 'M4 5h16v2H4V5zm0 6h16v2H4v-2zm0 6h10v2H4v-2z'],
    ['events', 'index.php?module=events&action=list', 'Evenement', 'M7 2h2v3h6V2h2v3h3v17H4V5h3V2zm11 8H6v10h12V10z'],
    ['messages', '../../feed/views/back/admin_messages.php', 'Message', 'M4 4h16v12H7l-3 4V4z'],
    ['categories', 'index.php?module=categories&action=list', 'Categories', 'M3 5a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5z'],
    ['formations', '../../controller/FormationC.php?office=back&action=list', 'Formation', 'M4 4h16v14H7l-3 3V4zm4 4v2h8V8H8zm0 4v2h6v-2H8z'],
    ['users', '#', 'Users', 'M12 12c2.2 0 4-1.8 4-4s-1.8-4-4-4-4 1.8-4 4 1.8 4 4 4zm0 2c-2.7 0-8 1.4-8 4.2V20h16v-1.8c0-2.8-5.3-4.2-8-4.2z'],
    ['contacts', '#', 'Contacts', 'M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z'],
];
?>
<aside class="sidebar events-admin-sidebar">
    <a class="brand" href="index.php?module=dashboard">
        <span class="brand-mark brand-briefcase" aria-hidden="true">
            <?= workifyAdminIcon('M10 5h4a2 2 0 0 1 2 2v2h4v10H4V9h4V7a2 2 0 0 1 2-2zm4 4V7h-4v2h4zm-8 4v4h12v-4h-3v2H9v-2H6z'); ?>
        </span>
        <span class="brand-text">Workify</span>
    </a>

    <div class="nav-title">Navigation</div>
    <?php foreach ($links as [$key, $href, $label, $icon]): ?>
        <?= workifyAdminLink($key, $href, $label, $icon, $activeModule); ?>
    <?php endforeach; ?>
</aside>
