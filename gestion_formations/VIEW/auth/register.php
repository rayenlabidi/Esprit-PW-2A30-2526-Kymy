<?php $pageTitle = 'Inscription'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="auth-shell">
    <div class="auth-card wide">
        <div>
            <p class="eyebrow">Nouvel utilisateur</p>
            <h1><?= t('auth.register_title') ?></h1>
            <p class="muted"><?= t('auth.register_copy') ?></p>
        </div>
        <form method="POST" action="<?= url(['module' => 'auth', 'action' => 'register']) ?>" class="stack-form js-validate" novalidate>
            <div class="form-grid">
                <div class="form-group">
                    <label for="first_name"><?= t('auth.first_name') ?></label>
                    <input id="first_name" name="first_name" class="form-control" type="text" data-label="Prenom" data-required="1" data-minlength="2">
                    <small class="field-error"></small>
                </div>
                <div class="form-group">
                    <label for="last_name"><?= t('auth.last_name') ?></label>
                    <input id="last_name" name="last_name" class="form-control" type="text" data-label="Nom" data-required="1" data-minlength="2">
                    <small class="field-error"></small>
                </div>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label for="role_slug"><?= t('auth.role') ?></label>
                    <select id="role_slug" name="role_slug" class="form-control" data-label="Role" data-required="1">
                        <option value=""><?= t('auth.choose_role') ?></option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= h($role['slug']) ?>"><?= h($role['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="field-error"></small>
                </div>
                <div class="form-group">
                    <label for="headline"><?= t('auth.headline') ?></label>
                    <input id="headline" name="headline" class="form-control" type="text" data-label="Headline" data-required="1" data-minlength="4">
                    <small class="field-error"></small>
                </div>
            </div>
            <div class="form-group">
                <label for="email_register"><?= t('auth.email') ?></label>
                <input id="email_register" name="email" class="form-control" type="email" data-label="Email" data-required="1" data-email="1">
                <small class="field-error"></small>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label for="password_register"><?= t('auth.password') ?></label>
                    <input id="password_register" name="password" class="form-control" type="password" data-label="Mot de passe" data-required="1" data-minlength="6">
                    <small class="field-error"></small>
                </div>
                <div class="form-group">
                    <label for="avatar_url"><?= t('auth.photo_url') ?></label>
                    <input id="avatar_url" name="avatar_url" class="form-control" type="url" data-label="Photo URL">
                    <small class="field-error"></small>
                </div>
            </div>
            <div class="form-group">
                <label for="bio"><?= t('auth.bio') ?></label>
                <textarea id="bio" name="bio" class="form-control" data-label="Bio" data-required="1" data-minlength="15"></textarea>
                <small class="field-error"></small>
            </div>
            <button class="btn btn-primary btn-block" type="submit"><?= t('auth.create_my_account') ?></button>
        </form>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
