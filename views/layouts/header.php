<?php $pageTitle = $pageTitle ?? 'Workify'; ?>
<!DOCTYPE html>
<html lang="<?= h(lang()) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle) ?> - Workify</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="module-<?= h(current_module()) ?>">
<header class="site-header">
    <div class="container nav-shell">
        <a class="brand" href="<?= url(['module' => 'dashboard', 'action' => 'index']) ?>">
            <span class="brand-logo" aria-hidden="true" title="Workify">W</span>
            <span>Workify</span>
        </a>
        <p class="nav-label">Navigation</p>
        <nav class="main-nav">
            <a class="nav-link nav-link-dashboard <?= is_active_module('dashboard') ? 'active' : '' ?>" href="<?= url(['module' => 'dashboard', 'action' => 'index']) ?>"><?= t('nav.home') ?></a>
            <a class="nav-link nav-link-jobs <?= is_active_module('jobs') ? 'active' : '' ?>" href="<?= url(['module' => 'jobs', 'action' => 'index']) ?>"><?= t('nav.jobs') ?></a>
            <a class="nav-link nav-link-formations <?= is_active_module('formations') ? 'active' : '' ?>" href="<?= url(['module' => 'formations', 'action' => 'index']) ?>"><?= t('nav.formations') ?></a>
            <?php if (has_role(['admin'])): ?>
                <a class="nav-link nav-link-users <?= is_active_module('users') ? 'active' : '' ?>" href="<?= url(['module' => 'users', 'action' => 'index']) ?>"><?= t('nav.users') ?></a>
            <?php endif; ?>
        </nav>
        <div class="nav-actions">
            <div class="lang-switch">
                <a class="<?= lang() === 'fr' ? 'active' : '' ?>" href="<?= url(['module' => current_module(), 'action' => current_action(), 'id' => $_GET['id'] ?? null, 'lang' => 'fr']) ?>">FR</a>
                <span>/</span>
                <a class="<?= lang() === 'en' ? 'active' : '' ?>" href="<?= url(['module' => current_module(), 'action' => current_action(), 'id' => $_GET['id'] ?? null, 'lang' => 'en']) ?>">EN</a>
            </div>
            <?php if (is_logged_in()): ?>
                <a class="ghost-link" href="<?= url(['module' => 'users', 'action' => 'show', 'id' => auth_user()['id']]) ?>">
                    <?= h(auth_user()['first_name']) ?> (<?= h(auth_user()['role_name']) ?>)
                </a>
                <a class="btn btn-outline" href="<?= url(['module' => 'auth', 'action' => 'logout']) ?>"><?= t('nav.logout') ?></a>
            <?php else: ?>
                <a class="ghost-link" href="<?= url(['module' => 'auth', 'action' => 'login']) ?>"><?= t('nav.login') ?></a>
                <a class="btn btn-primary" href="<?= url(['module' => 'auth', 'action' => 'register']) ?>"><?= t('nav.register') ?></a>
            <?php endif; ?>
        </div>
    </div>
</header>
<main class="page-shell">
    <div class="container">
        <?php if (!$dbConnected): ?>
            <div class="flash flash-error">Connexion MySQL indisponible. Importez `database.sql` dans phpMyAdmin puis rechargez la page.</div>
        <?php endif; ?>
        <?php foreach ($flashMessages as $flash): ?>
            <div class="flash flash-<?= h($flash['type']) ?>"><?= h($flash['message']) ?></div>
        <?php endforeach; ?>
