<?php
$isEdit = isset($formData['id']);
$pageTitle = $isEdit ? 'Modifier Utilisateur' : 'Ajouter Utilisateur';
$activeModule = 'users';
$formData = isset($formData) ? $formData : [];
$errors = isset($errors) ? $errors : [];
$roles = isset($roles) ? $roles : [];
$id = $isEdit ? (int) $formData['id'] : 0;
$action = $isEdit ? 'edit&id=' . $id : 'add';
include __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <div>
        <p class="eyebrow">Gestion utilisateurs</p>
        <h2><?= $isEdit ? 'Edit user account' : 'Create a user account'; ?></h2>
    </div>
    <a class="btn" href="../controller/UtilisateurC.php?action=list">Retour</a>
</div>

<form class="form-box" data-validate="user" action="../controller/UtilisateurC.php?action=<?= $action; ?>" method="post">
    <div class="error-box">
        <?php if (!empty($errors)) { ?>
            <ul>
                <?php foreach ($errors as $error) { ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES); ?></li>
                <?php } ?>
            </ul>
        <?php } ?>
    </div>

    <div class="form-grid">
        <div>
            <label for="role_id">Role</label>
            <select id="role_id" name="role_id">
                <option value="">Choisir</option>
                <?php foreach ($roles as $roleItem) { ?>
                    <option value="<?= (int) $roleItem['id']; ?>" <?= (isset($formData['role_id']) && (string) $formData['role_id'] === (string) $roleItem['id']) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($roleItem['name'], ENT_QUOTES); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div>
            <label for="status">Statut</label>
            <select id="status" name="status">
                <?php $selectedStatus = isset($formData['status']) ? $formData['status'] : 'active'; ?>
                <option value="active" <?= $selectedStatus === 'active' ? 'selected' : ''; ?>>Active</option>
                <option value="pending" <?= $selectedStatus === 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="blocked" <?= $selectedStatus === 'blocked' ? 'selected' : ''; ?>>Blocked</option>
            </select>
        </div>

        <div>
            <label for="first_name">Prenom</label>
            <input id="first_name" name="first_name" value="<?= htmlspecialchars(isset($formData['first_name']) ? $formData['first_name'] : '', ENT_QUOTES); ?>">
        </div>

        <div>
            <label for="last_name">Nom</label>
            <input id="last_name" name="last_name" value="<?= htmlspecialchars(isset($formData['last_name']) ? $formData['last_name'] : '', ENT_QUOTES); ?>">
        </div>

        <div>
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="<?= htmlspecialchars(isset($formData['email']) ? $formData['email'] : '', ENT_QUOTES); ?>">
        </div>

        <div>
            <label for="phone">Telephone</label>
            <input id="phone" name="phone" value="<?= htmlspecialchars(isset($formData['phone']) ? $formData['phone'] : '', ENT_QUOTES); ?>">
        </div>

        <div>
            <label for="password"><?= $isEdit ? 'Nouveau mot de passe' : 'Mot de passe'; ?></label>
            <input id="password" name="password" type="password" placeholder="<?= $isEdit ? 'Laisser vide pour garder le meme' : ''; ?>">
        </div>

        <div>
            <label for="headline">Titre</label>
            <input id="headline" name="headline" value="<?= htmlspecialchars(isset($formData['headline']) ? $formData['headline'] : '', ENT_QUOTES); ?>">
        </div>

        <div class="field-full">
            <label for="bio">Bio</label>
            <textarea id="bio" name="bio"><?= htmlspecialchars(isset($formData['bio']) ? $formData['bio'] : '', ENT_QUOTES); ?></textarea>
        </div>
    </div>

    <div class="actions" style="margin-top: 18px;">
        <button class="btn btn-primary" type="submit">Enregistrer</button>
        <a class="btn" href="../controller/UtilisateurC.php?action=list">Annuler</a>
    </div>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
