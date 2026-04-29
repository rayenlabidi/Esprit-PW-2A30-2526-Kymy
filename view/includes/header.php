<?php
$pageTitle = isset($pageTitle) ? $pageTitle : 'Workify';
$office = isset($office) ? $office : 'front';
$activeModule = isset($activeModule) ? $activeModule : '';
$officeLabel = $office === 'back' ? 'BackOffice' : 'FrontOffice';

if ($office === 'back') {
    $switchLabel = 'FrontOffice';
    $switchHref = $activeModule === 'formations'
        ? '../controller/FormationC.php?office=front&action=list'
        : '../controller/HomeC.php';
} else {
    $switchLabel = 'BackOffice';
    $switchHref = $activeModule === 'formations'
        ? '../controller/FormationC.php?office=back&action=list'
        : '../controller/BackDashboardC.php';
}
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
            <span class="nav-link nav-disabled">
                <span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 5h16v2H4V5zm0 6h16v2H4v-2zm0 6h10v2H4v-2z"/></svg></span>
                Publications
            </span>
            <span class="nav-link nav-disabled">
                <span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M10 4h4a2 2 0 0 1 2 2v2h4v12H4V8h4V6a2 2 0 0 1 2-2zm4 4V6h-4v2h4z"/></svg></span>
                Jobs
            </span>
            <span class="nav-link nav-disabled">
                <span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M7 2h2v3h6V2h2v3h3v17H4V5h3V2zm11 8H6v10h12V10z"/></svg></span>
                Events
            </span>
            <a class="nav-link <?= $activeModule === 'formations' ? 'active' : ''; ?>" href="../controller/FormationC.php?office=<?= $office; ?>&action=list">
                <span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 4h16v14H7l-3 3V4zm4 4v2h8V8H8zm0 4v2h6v-2H8z"/></svg></span>
                Browse Formations
            </a>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <div>
                    <p class="eyebrow"><?= htmlspecialchars($officeLabel, ENT_QUOTES); ?></p>
                    <h1><?= htmlspecialchars($pageTitle, ENT_QUOTES); ?></h1>
                </div>
                <a class="switch-btn" href="<?= htmlspecialchars($switchHref, ENT_QUOTES); ?>" title="Basculer vers <?= htmlspecialchars($switchLabel, ENT_QUOTES); ?>" aria-label="Basculer vers <?= htmlspecialchars($switchLabel, ENT_QUOTES); ?>">
                    <span aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="20" height="20">
                            <path d="M7 7h10l-2.6-2.6L16 2.8 21.2 8 16 13.2l-1.6-1.6L17 9H7V7z"></path>
                            <path d="M17 17H7l2.6 2.6L8 21.2 2.8 16 8 10.8l1.6 1.6L7 15h10v2z"></path>
                        </svg>
                    </span>
                </a>
            </header>
            <section class="page">
