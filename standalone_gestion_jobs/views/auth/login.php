<?php $pageTitle = 'Connexion'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="auth-shell">
    <div class="auth-card">
        <div>
            <p class="eyebrow">Session Workify</p>
            <h1><?= t('auth.login_title') ?></h1>
            <p class="muted"><?= t('auth.login_copy') ?></p>
        </div>
        <form method="POST" action="<?= url(['module' => 'auth', 'action' => 'login']) ?>" class="stack-form js-validate" novalidate>
            <div class="form-group">
                <label for="email"><?= t('auth.email') ?></label>
                <input id="email" name="email" class="form-control" type="email" data-label="Email" data-required="1" data-email="1">
                <small class="field-error"></small>
            </div>
            <div class="form-group">
                <label for="password"><?= t('auth.password') ?></label>
                <input id="password" name="password" class="form-control" type="password" data-label="Mot de passe" data-required="1" data-minlength="6">
                <small class="field-error"></small>
            </div>
            <button class="btn btn-primary btn-block" type="submit"><?= t('nav.login') ?></button>
        </form>
        <p class="muted"><?= t('auth.no_account') ?> <a href="<?= url(['module' => 'auth', 'action' => 'register']) ?>"><?= t('nav.register') ?></a></p>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
