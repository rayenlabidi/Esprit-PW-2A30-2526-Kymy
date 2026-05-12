<?php
require_once __DIR__ . '/../../controller/AuthC.php';
AuthC::startSession();

$pageTitle = isset($pageTitle) ? $pageTitle : 'Workify';
$office = isset($office) ? $office : 'front';
$activeModule = isset($activeModule) ? $activeModule : '';
$officeLabel = $office === 'back' ? 'BackOffice' : 'FrontOffice';
$isAdmin = AuthC::isAdmin();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES); ?> - Workify</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <a class="brand" href="../controller/HomeC.php">
                <span class="brand-mark">W</span>
                <span class="brand-text">Workify</span>
            </a>

            <div class="nav-title">Navigation</div>
            <?php if ($isAdmin && $office === 'back') { ?>
                <a class="nav-link <?= $activeModule === 'dashboard' ? 'active' : ''; ?>" href="../controller/BackDashboardC.php">
                    <span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg></span>
                    Dashboard
                </a>
            <?php } ?>
            <a class="nav-link <?= $activeModule === 'publications' ? 'active' : ''; ?>" href="../controller/PublicationC.php?office=<?= $office; ?>&action=list">
                <span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 5h16v2H4V5zm0 6h16v2H4v-2zm0 6h10v2H4v-2z"/></svg></span>
                Publications
            </a>
            <a class="nav-link <?= $activeModule === 'jobs' ? 'active' : ''; ?>" href="../controller/JobC.php?office=<?= $office; ?>&action=list">
                <span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M10 4h4a2 2 0 0 1 2 2v2h4v12H4V8h4V6a2 2 0 0 1 2-2zm4 4V6h-4v2h4z"/></svg></span>
                Jobs
            </a>
            <span class="nav-link nav-disabled">
                <span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M7 2h2v3h6V2h2v3h3v17H4V5h3V2zm11 8H6v10h12V10z"/></svg></span>
                Events
            </span>
            <a class="nav-link <?= $activeModule === 'formations' ? 'active' : ''; ?>" href="../controller/FormationC.php?office=<?= $office; ?>&action=list">
                <span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 4h16v14H7l-3 3V4zm4 4v2h8V8H8zm0 4v2h6v-2H8z"/></svg></span>
                Browse Formations
            </a>
            <?php if ($isAdmin && $office === 'back') { ?>
                <a class="nav-link <?= $activeModule === 'users' ? 'active' : ''; ?>" href="../controller/UtilisateurC.php?action=list">
                    <span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 12c2.2 0 4-1.8 4-4s-1.8-4-4-4-4 1.8-4 4 1.8 4 4 4zm0 2c-2.7 0-8 1.4-8 4.2V20h16v-1.8c0-2.8-5.3-4.2-8-4.2z"/></svg></span>
                    Users
                </a>
            <?php } ?>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <div>
                    <p class="eyebrow"><?= htmlspecialchars($officeLabel, ENT_QUOTES); ?></p>
                    <h1><?= htmlspecialchars($pageTitle, ENT_QUOTES); ?></h1>
                </div>
                <div class="topbar-actions">
                    <?php if ($isAdmin) { ?>
                        <span class="admin-pill"><?= htmlspecialchars(AuthC::currentUserName(), ENT_QUOTES); ?></span>
                        <?php if ($office !== 'back') { ?>
                            <a class="btn" href="../controller/BackDashboardC.php">Admin</a>
                        <?php } ?>
                        <a class="btn btn-danger" href="../controller/AuthController.php?action=logout">Logout</a>
                    <?php } else { ?>
                        <a class="btn btn-primary" href="../controller/AuthController.php?action=login">Admin login</a>
                    <?php } ?>
                </div>
            </header>
            <section class="page">
