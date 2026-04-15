<?php $pageTitle = $mode === 'create' ? 'Ajouter un utilisateur' : 'Modifier un utilisateur'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="page-head">
    <div>
        <p class="eyebrow">Gestion des utilisateurs</p>
        <h1><?= $mode === 'create' ? 'Ajouter un utilisateur' : 'Modifier un utilisateur' ?></h1>
    </div>
    <a class="btn btn-outline" href="<?= url(['module' => 'users', 'action' => 'index']) ?>">Retour</a>
</section>

<section class="section-card form-card">
    <form method="POST" action="<?= $mode === 'create' ? url(['module' => 'users', 'action' => 'create']) : url(['module' => 'users', 'action' => 'edit', 'id' => $user['id']]) ?>" class="stack-form js-validate" novalidate>
        <div class="form-grid">
            <div class="form-group">
                <label for="first_name">Prenom</label>
                <input id="first_name" name="first_name" class="form-control" type="text" value="<?= h($user['first_name'] ?? '') ?>" data-label="Prenom" data-required="1" data-minlength="2">
                <small class="field-error"></small>
            </div>
            <div class="form-group">
                <label for="last_name">Nom</label>
                <input id="last_name" name="last_name" class="form-control" type="text" value="<?= h($user['last_name'] ?? '') ?>" data-label="Nom" data-required="1" data-minlength="2">
                <small class="field-error"></small>
            </div>
        </div>
        <div class="form-grid">
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" name="email" class="form-control" type="email" value="<?= h($user['email'] ?? '') ?>" data-label="Email" data-required="1" data-email="1">
                <small class="field-error"></small>
            </div>
            <div class="form-group">
                <label for="role_id">Role</label>
                <select id="role_id" name="role_id" class="form-control" data-label="Role" data-required="1">
                    <option value="">Choisir un role</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role['id'] ?>" <?= (($user['role_id'] ?? '') == $role['id']) ? 'selected' : '' ?>><?= h($role['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="field-error"></small>
            </div>
        </div>
        <div class="form-grid">
            <div class="form-group">
                <label for="password">Mot de passe <?= $mode === 'edit' ? '(laisser vide pour garder l actuel)' : '' ?></label>
                <input id="password" name="password" class="form-control" type="password" data-label="Mot de passe" <?= $mode === 'create' ? 'data-required="1"' : '' ?> data-minlength="6">
                <small class="field-error"></small>
            </div>
            <div class="form-group">
                <label for="status">Statut</label>
                <select id="status" name="status" class="form-control" data-label="Statut" data-required="1">
                    <?php foreach (['active', 'pending', 'blocked'] as $status): ?>
                        <option value="<?= $status ?>" <?= (($user['status'] ?? 'active') === $status) ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="field-error"></small>
            </div>
        </div>
        <div class="form-group">
            <label for="headline">Headline</label>
            <input id="headline" name="headline" class="form-control" type="text" value="<?= h($user['headline'] ?? '') ?>" data-label="Headline" data-required="1" data-minlength="4">
            <small class="field-error"></small>
        </div>
        <div class="form-grid">
            <div class="form-group">
                <label for="avatar_url">Photo URL</label>
                <input id="avatar_url" name="avatar_url" class="form-control" type="url" value="<?= h($user['avatar_url'] ?? '') ?>" data-label="Photo URL">
                <small class="field-error"></small>
            </div>
            <div class="form-group">
                <label for="bio">Bio</label>
                <textarea id="bio" name="bio" class="form-control" data-label="Bio" data-required="1" data-minlength="15"><?= h($user['bio'] ?? '') ?></textarea>
                <small class="field-error"></small>
            </div>
        </div>
        <button class="btn btn-primary" type="submit"><?= $mode === 'create' ? 'Enregistrer' : 'Mettre a jour' ?></button>
    </form>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
