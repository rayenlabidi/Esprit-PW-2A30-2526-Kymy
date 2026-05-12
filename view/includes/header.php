<?php
require_once __DIR__ . '/../../controller/AuthC.php';
AuthC::startSession();

$pageTitle = isset($pageTitle) ? $pageTitle : 'Workify';
$office = isset($office) ? $office : 'front';
$activeModule = isset($activeModule) ? $activeModule : '';
$isAdmin = AuthC::isAdmin();
$isLoggedIn = AuthC::isLoggedIn();
$isBackOffice = $office === 'back' && $isAdmin;

$frontLinks = [
    ['key' => 'jobs', 'label' => 'Jobs', 'href' => '../controller/JobC.php?office=front&action=list', 'icon' => 'M10 4h4a2 2 0 0 1 2 2v2h4v12H4V8h4V6a2 2 0 0 1 2-2zm4 4V6h-4v2h4z'],
    ['key' => 'publications', 'label' => 'Publication', 'href' => '../controller/PublicationC.php?office=front&action=list', 'icon' => 'M4 5h16v2H4V5zm0 6h16v2H4v-2zm0 6h10v2H4v-2z'],
    ['key' => 'events', 'label' => 'Evenement', 'href' => '#events', 'icon' => 'M7 2h2v3h6V2h2v3h3v17H4V5h3V2zm11 8H6v10h12V10z'],
    ['key' => 'messages', 'label' => 'Message', 'href' => '#messages', 'icon' => 'M4 4h16v12H7l-3 4V4z'],
    ['key' => 'formations', 'label' => 'Formation', 'href' => '../controller/FormationC.php?office=front&action=list', 'icon' => 'M4 4h16v14H7l-3 3V4zm4 4v2h8V8H8zm0 4v2h6v-2H8z']
];

$backLinks = [
    ['key' => 'dashboard', 'label' => 'Dashboard', 'href' => '../controller/BackDashboardC.php', 'icon' => 'M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z'],
    ['key' => 'jobs', 'label' => 'Jobs', 'href' => '../controller/JobC.php?office=back&action=list', 'icon' => 'M10 4h4a2 2 0 0 1 2 2v2h4v12H4V8h4V6a2 2 0 0 1 2-2zm4 4V6h-4v2h4z'],
    ['key' => 'publications', 'label' => 'Publication', 'href' => '../controller/PublicationC.php?office=back&action=list', 'icon' => 'M4 5h16v2H4V5zm0 6h16v2H4v-2zm0 6h10v2H4v-2z'],
    ['key' => 'events', 'label' => 'Evenement', 'href' => '#events', 'icon' => 'M7 2h2v3h6V2h2v3h3v17H4V5h3V2zm11 8H6v10h12V10z'],
    ['key' => 'messages', 'label' => 'Message', 'href' => '#messages', 'icon' => 'M4 4h16v12H7l-3 4V4z'],
    ['key' => 'formations', 'label' => 'Formation', 'href' => '../controller/FormationC.php?office=back&action=list', 'icon' => 'M4 4h16v14H7l-3 3V4zm4 4v2h8V8H8zm0 4v2h6v-2H8z'],
    ['key' => 'users', 'label' => 'Users', 'href' => '../controller/UtilisateurC.php?action=list', 'icon' => 'M12 12c2.2 0 4-1.8 4-4s-1.8-4-4-4-4 1.8-4 4 1.8 4 4 4zm0 2c-2.7 0-8 1.4-8 4.2V20h16v-1.8c0-2.8-5.3-4.2-8-4.2z']
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES); ?> - Workify</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="<?= $isBackOffice ? 'back-mode' : 'front-mode'; ?>">
    <div class="<?= $isBackOffice ? 'app-shell' : 'front-shell'; ?>">
        <?php if ($isBackOffice) { ?>
            <aside class="sidebar">
                <a class="brand" href="../controller/BackDashboardC.php">
                    <span class="brand-mark brand-briefcase" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M10 5h4a2 2 0 0 1 2 2v2h4v10H4V9h4V7a2 2 0 0 1 2-2zm4 4V7h-4v2h4zm-8 4v4h12v-4h-3v2H9v-2H6z"/></svg>
                    </span>
                    <span class="brand-text">Workify</span>
                </a>

                <div class="nav-title">Navigation</div>
                <?php foreach ($backLinks as $link) { ?>
                    <a class="nav-link <?= $activeModule === $link['key'] ? 'active' : ''; ?>" href="<?= htmlspecialchars($link['href'], ENT_QUOTES); ?>">
                        <span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="<?= htmlspecialchars($link['icon'], ENT_QUOTES); ?>"/></svg></span>
                        <?= htmlspecialchars($link['label'], ENT_QUOTES); ?>
                    </a>
                <?php } ?>
            </aside>

            <main class="main-content">
                <header class="topbar">
                    <div>
                        <p class="eyebrow">Espace prive</p>
                        <h1><?= htmlspecialchars($pageTitle, ENT_QUOTES); ?></h1>
                    </div>
                    <div class="topbar-actions">
                        <span class="admin-pill"><?= htmlspecialchars(AuthC::currentUserName(), ENT_QUOTES); ?></span>
                        <a class="btn btn-danger" href="../controller/AuthController.php?action=logout">Deconnexion</a>
                    </div>
                </header>
                <section class="page">
        <?php } else { ?>
            <header class="public-header">
                <a class="public-brand" href="../controller/HomeC.php">
                    <span class="brand-mark brand-briefcase" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M10 5h4a2 2 0 0 1 2 2v2h4v10H4V9h4V7a2 2 0 0 1 2-2zm4 4V7h-4v2h4zm-8 4v4h12v-4h-3v2H9v-2H6z"/></svg>
                    </span>
                    <span class="brand-text">Workify</span>
                </a>
                <nav class="public-nav" aria-label="Navigation principale">
                    <?php foreach ($frontLinks as $link) { ?>
                        <a class="<?= $activeModule === $link['key'] ? 'active' : ''; ?>" href="<?= htmlspecialchars($link['href'], ENT_QUOTES); ?>">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="<?= htmlspecialchars($link['icon'], ENT_QUOTES); ?>"/></svg>
                            <?= htmlspecialchars($link['label'], ENT_QUOTES); ?>
                        </a>
                    <?php } ?>
                </nav>
                <div class="public-actions">
                    <?php if ($isLoggedIn) { ?>
                        <span class="admin-pill"><?= htmlspecialchars(AuthC::currentUserName(), ENT_QUOTES); ?></span>
                        <?php if ($isAdmin) { ?>
                            <a class="btn btn-primary" href="../controller/BackDashboardC.php">Espace prive</a>
                        <?php } ?>
                        <a class="btn" href="../controller/AuthController.php?action=logout">Deconnexion</a>
                    <?php } else { ?>
                        <a class="btn btn-primary" href="../controller/AuthController.php?action=login">Connexion</a>
                    <?php } ?>
                </div>
            </header>
            <main class="public-main">
                <section class="page">
        <?php } ?>
