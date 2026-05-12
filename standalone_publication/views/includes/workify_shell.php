<?php
function wf_shell_icon($path)
{
    return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="' . htmlspecialchars($path, ENT_QUOTES) . '"/></svg>';
}

function wf_shell_badge($value)
{
    return ((int) $value > 0) ? '<span class="nav-badge">' . (int) $value . '</span>' : '';
}

function feed_public_header($activeModule = '')
{
    $links = [
        ['key' => 'jobs', 'label' => 'Jobs', 'href' => '../../../controller/JobC.php?office=front&action=list', 'icon' => 'M10 4h4a2 2 0 0 1 2 2v2h4v12H4V8h4V6a2 2 0 0 1 2-2zm4 4V6h-4v2h4z'],
        ['key' => 'publications', 'label' => 'Publication', 'href' => 'publications.php', 'icon' => 'M4 5h16v2H4V5zm0 6h16v2H4v-2zm0 6h10v2H4v-2z'],
        ['key' => 'events', 'label' => 'Evenement', 'href' => '../../../controller/HomeC.php#services', 'icon' => 'M7 2h2v3h6V2h2v3h3v17H4V5h3V2zm11 8H6v10h12V10z'],
        ['key' => 'messages', 'label' => 'Message', 'href' => 'messages.php', 'icon' => 'M4 4h16v12H7l-3 4V4z'],
        ['key' => 'formations', 'label' => 'Formation', 'href' => '../../../controller/FormationC.php?office=front&action=list', 'icon' => 'M4 4h16v14H7l-3 3V4zm4 4v2h8V8H8zm0 4v2h6v-2H8z']
    ];
    ?>
    <header class="public-header feed-public-header">
        <a class="public-brand" href="../../../controller/HomeC.php">
            <span class="brand-mark brand-briefcase" aria-hidden="true">
                <?= wf_shell_icon('M10 5h4a2 2 0 0 1 2 2v2h4v10H4V9h4V7a2 2 0 0 1 2-2zm4 4V7h-4v2h4zm-8 4v4h12v-4h-3v2H9v-2H6z'); ?>
            </span>
            <span class="brand-text">Workify</span>
        </a>
        <nav class="public-nav" aria-label="Navigation principale">
            <?php foreach ($links as $link) { ?>
                <a class="<?= $activeModule === $link['key'] ? 'active' : ''; ?>" data-module="<?= htmlspecialchars($link['key'], ENT_QUOTES); ?>" href="<?= htmlspecialchars($link['href'], ENT_QUOTES); ?>">
                    <?= wf_shell_icon($link['icon']); ?>
                    <?= htmlspecialchars($link['label'], ENT_QUOTES); ?>
                </a>
            <?php } ?>
        </nav>
        <div class="public-actions">
            <a class="btn" href="../../../controller/AuthController.php?action=signup">Inscription</a>
            <a class="btn btn-primary" href="../../../controller/AuthController.php?action=login">Connexion</a>
        </div>
    </header>
    <?php
}

function feed_public_footer()
{
    ?>
    <footer class="site-footer feed-site-footer">
        <div class="footer-grid">
            <div>
                <a class="public-brand footer-brand" href="../../../controller/HomeC.php">
                    <span class="brand-mark brand-briefcase" aria-hidden="true">
                        <?= wf_shell_icon('M10 5h4a2 2 0 0 1 2 2v2h4v10H4V9h4V7a2 2 0 0 1 2-2zm4 4V7h-4v2h4zm-8 4v4h12v-4h-3v2H9v-2H6z'); ?>
                    </span>
                    <span class="brand-text">Workify</span>
                </a>
                <p class="muted">Une plateforme professionnelle pour apprendre, recruter, postuler et collaborer avec une experience fluide.</p>
            </div>
            <div>
                <h3>Navigation</h3>
                <a href="../../../controller/HomeC.php#services">Services</a>
                <a href="../../../controller/HomeC.php#about">A propos</a>
                <a href="../../../controller/HomeC.php#projects">Projets</a>
                <a href="../../../controller/HomeC.php#contact">Contact</a>
            </div>
            <div>
                <h3>Modules</h3>
                <a href="../../../controller/JobC.php?office=front&action=list">Jobs</a>
                <a href="../../../controller/FormationC.php?office=front&action=list">Formations</a>
                <a href="publications.php">Publications</a>
                <a href="messages.php">Messages</a>
            </div>
            <div>
                <h3>Contact</h3>
                <span>workifytn@gmail.com</span>
                <span>Tunis, Tunisie</span>
                <div class="social-links">
                    <a href="../../../controller/HomeC.php#contact">LinkedIn</a>
                    <a href="../../../controller/HomeC.php#contact">Instagram</a>
                    <a href="../../../controller/HomeC.php#contact">Facebook</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; <?= date('Y'); ?> Workify. Tous droits reserves.</span>
            <a href="../../../controller/AuthController.php?action=login">Connexion</a>
        </div>
    </footer>
    <?php
}

function feed_admin_shell_start($pageTitle, $activeModule, $counts = [])
{
    $postsCount = isset($counts['posts']) ? (int) $counts['posts'] : 0;
    $messagesCount = isset($counts['messages']) ? (int) $counts['messages'] : 0;
    $links = [
        ['key' => 'dashboard', 'label' => 'Dashboard', 'href' => 'admin_dashboard.php', 'icon' => 'M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z', 'count' => 0],
        ['key' => 'jobs', 'label' => 'Jobs', 'href' => '../../../controller/JobC.php?office=back&action=list', 'icon' => 'M10 4h4a2 2 0 0 1 2 2v2h4v12H4V8h4V6a2 2 0 0 1 2-2zm4 4V6h-4v2h4z', 'count' => 0],
        ['key' => 'publications', 'label' => 'Publication', 'href' => 'admin.php', 'icon' => 'M4 5h16v2H4V5zm0 6h16v2H4v-2zm0 6h10v2H4v-2z', 'count' => $postsCount],
        ['key' => 'events', 'label' => 'Evenement', 'href' => '../../../controller/HomeC.php#services', 'icon' => 'M7 2h2v3h6V2h2v3h3v17H4V5h3V2zm11 8H6v10h12V10z', 'count' => 0],
        ['key' => 'messages', 'label' => 'Message', 'href' => 'admin_messages.php', 'icon' => 'M4 4h16v12H7l-3 4V4z', 'count' => $messagesCount],
        ['key' => 'formations', 'label' => 'Formation', 'href' => '../../../controller/FormationC.php?office=back&action=list', 'icon' => 'M4 4h16v14H7l-3 3V4zm4 4v2h8V8H8zm0 4v2h6v-2H8z', 'count' => 0],
        ['key' => 'users', 'label' => 'Users', 'href' => '../../../controller/UtilisateurC.php?action=list', 'icon' => 'M12 12c2.2 0 4-1.8 4-4s-1.8-4-4-4-4 1.8-4 4 1.8 4 4 4zm0 2c-2.7 0-8 1.4-8 4.2V20h16v-1.8c0-2.8-5.3-4.2-8-4.2z', 'count' => 0],
        ['key' => 'contacts', 'label' => 'Contacts', 'href' => '../../../controller/ContactC.php?action=list', 'icon' => 'M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z', 'count' => 0],
    ];
    ?>
    <div class="app-shell feed-app-shell">
        <aside class="sidebar">
            <a class="brand" href="admin_dashboard.php">
                <span class="brand-mark brand-briefcase" aria-hidden="true">
                    <?= wf_shell_icon('M10 5h4a2 2 0 0 1 2 2v2h4v10H4V9h4V7a2 2 0 0 1 2-2zm4 4V7h-4v2h4zm-8 4v4h12v-4h-3v2H9v-2H6z'); ?>
                </span>
                <span class="brand-text">Workify</span>
            </a>
            <div class="nav-title">Navigation</div>
            <?php foreach ($links as $link) { ?>
                <a class="nav-link <?= $activeModule === $link['key'] ? 'active' : ''; ?>" data-module="<?= htmlspecialchars($link['key'], ENT_QUOTES); ?>" href="<?= htmlspecialchars($link['href'], ENT_QUOTES); ?>">
                    <span class="nav-icon" aria-hidden="true"><?= wf_shell_icon($link['icon']); ?></span>
                    <span><?= htmlspecialchars($link['label'], ENT_QUOTES); ?></span>
                    <?= wf_shell_badge($link['count']); ?>
                </a>
            <?php } ?>
        </aside>
        <main class="main-content feed-main-content">
            <header class="topbar">
                <div>
                    <p class="eyebrow">Espace prive</p>
                    <h1><?= htmlspecialchars($pageTitle, ENT_QUOTES); ?></h1>
                </div>
                <div class="topbar-actions">
                    <span class="admin-pill">Workify</span>
                    <a class="btn btn-danger" href="../../../controller/AuthController.php?action=logout">Deconnexion</a>
                </div>
            </header>
            <section class="page feed-admin-page">
    <?php
}

function feed_admin_shell_end()
{
    echo '</section></main></div>';
}
?>
