<?php
/**
 * Public Workify shell for the Events module.
 * Keep this file layout-only so event pages can be merged independently.
 */
$activeNav = $activeNav ?? 'events';

if (!function_exists('workifyIcon')) {
    function workifyIcon(string $path): string
    {
        return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="' . htmlspecialchars($path, ENT_QUOTES) . '"/></svg>';
    }
}

$publicLinks = [
    ['key' => 'jobs', 'label' => 'Jobs', 'href' => '#', 'icon' => 'M10 4h4a2 2 0 0 1 2 2v2h4v12H4V8h4V6a2 2 0 0 1 2-2zm4 4V6h-4v2h4z'],
    ['key' => 'publications', 'label' => 'Publication', 'href' => '../../feed/public/index.php', 'icon' => 'M4 5h16v2H4V5zm0 6h16v2H4v-2zm0 6h10v2H4v-2z'],
    ['key' => 'events', 'label' => 'Evenement', 'href' => 'index.php', 'icon' => 'M7 2h2v3h6V2h2v3h3v17H4V5h3V2zm11 8H6v10h12V10z'],
    ['key' => 'calendar', 'label' => 'Calendrier', 'href' => 'index.php?action=calendar', 'icon' => 'M5 4h1V2h2v2h8V2h2v2h1a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2zm14 7H5v9h14v-9z'],
    ['key' => 'formations', 'label' => 'Formation', 'href' => '../../controller/FormationC.php?office=front&action=list', 'icon' => 'M4 4h16v14H7l-3 3V4zm4 4v2h8V8H8zm0 4v2h6v-2H8z'],
];
?>
<header class="public-header events-public-header">
    <a class="public-brand" href="../../controller/HomeC.php">
        <span class="brand-mark brand-briefcase" aria-hidden="true">
            <?= workifyIcon('M10 5h4a2 2 0 0 1 2 2v2h4v10H4V9h4V7a2 2 0 0 1 2-2zm4 4V7h-4v2h4zm-8 4v4h12v-4h-3v2H9v-2H6z'); ?>
        </span>
        <span class="brand-text">Workify</span>
    </a>

    <nav class="public-nav" aria-label="Navigation principale">
        <?php foreach ($publicLinks as $link): ?>
            <a class="<?= $activeNav === $link['key'] ? 'active' : ''; ?>" data-module="<?= htmlspecialchars($link['key'], ENT_QUOTES); ?>" href="<?= htmlspecialchars($link['href'], ENT_QUOTES); ?>">
                <?= workifyIcon($link['icon']); ?>
                <?= htmlspecialchars($link['label'], ENT_QUOTES); ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="public-actions">
        <a class="btn" href="index.php?action=create"><?= workifyIcon('M12 5v14m-7-7h14'); ?> Creer</a>
        <a class="btn btn-primary" href="../admin/index.php"><?= workifyIcon('M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8zm8.7 4a7.9 7.9 0 0 0-.2-1.7l2-1.5-2-3.4-2.4 1a8 8 0 0 0-1.5-.9L16.3 3h-4l-.4 2.5a8 8 0 0 0-1.5.9l-2.4-1-2 3.4 2 1.5A7.9 7.9 0 0 0 7.8 12c0 .6.1 1.2.2 1.7l-2 1.5 2 3.4 2.4-1c.5.4 1 .7 1.5.9l.4 2.5h4l.4-2.5c.5-.2 1-.5 1.5-.9l2.4 1 2-3.4-2-1.5c.1-.5.2-1.1.2-1.7z'); ?> Admin</a>
    </div>
</header>
