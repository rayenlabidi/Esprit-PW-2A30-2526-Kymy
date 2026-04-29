<?php
include_once '../controller/UtilisateurController.php';
$pageData = (new UtilisateurController())->prepareUpdatePage();
$roles = $pageData['roles'];
$userToForm = $pageData['userToForm'];
include_once 'header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 text-dark fw-bold" style="font-family: 'Outfit', sans-serif;"><i class="fa-solid fa-user-pen me-2 text-primary"></i>Modifier l'Utilisateur</h2>
    <a href="listeUtilisateurs.php" class="btn btn-outline-secondary px-4"><i class="fa-solid fa-arrow-left me-2"></i>Retour a la liste</a>
</div>

<?php if ($userToForm) { ?>
<div class="card mb-5 animate-fade-in border-0" style="background: rgba(255, 255, 255, 0.7); box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
    <div class="card-body p-4 p-md-5">
        <form action="../controller/UtilisateurRouter.php?action=update" method="POST" class="row g-4">
            <input type="hidden" name="id" value="<?= htmlspecialchars($userToForm['id']) ?>">

            <div class="col-md-6">
                <label class="form-label fw-bold text-muted">Role</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fa-solid fa-user-shield text-primary"></i></span>
                    <select name="role_id" class="form-select bg-white" required>
                        <?php foreach ($roles as $role) { ?>
                            <option value="<?= $role['id'] ?>" <?= $role['id'] == $userToForm['role_id'] ? 'selected' : '' ?>><?= htmlspecialchars(ucfirst($role['name'])) ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold text-muted">Statut</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fa-solid fa-toggle-on text-success"></i></span>
                    <select name="status" class="form-select bg-white">
                        <option value="active" <?= $userToForm['status'] == 'active' ? 'selected' : '' ?>>Actif</option>
                        <option value="blocked" <?= $userToForm['status'] == 'blocked' ? 'selected' : '' ?>>Inactif</option>
                        <option value="pending" <?= $userToForm['status'] == 'pending' ? 'selected' : '' ?>>En attente</option>
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold text-muted">Prenom</label>
                <input type="text" class="form-control bg-white" name="first_name" value="<?= htmlspecialchars($userToForm['first_name']) ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold text-muted">Nom</label>
                <input type="text" class="form-control bg-white" name="last_name" value="<?= htmlspecialchars($userToForm['last_name']) ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold text-muted">Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fa-solid fa-envelope text-primary"></i></span>
                    <input type="email" class="form-control bg-white" name="email" value="<?= htmlspecialchars($userToForm['email']) ?>" required>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold text-muted">Telephone</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fa-solid fa-phone text-primary"></i></span>
                    <input type="tel" class="form-control bg-white" name="phone" value="<?= htmlspecialchars($userToForm['phone'] ?? '') ?>">
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold text-muted">Nouveau Mot de passe <span class="text-secondary small fw-normal">(laisser vide pour conserver l'actuel)</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fa-solid fa-key text-primary"></i></span>
                    <input type="password" class="form-control bg-white" name="password">
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold text-muted">Titre professionnel (Headline)</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fa-solid fa-briefcase text-primary"></i></span>
                    <input type="text" class="form-control bg-white" name="headline" value="<?= htmlspecialchars($userToForm['headline']) ?>">
                </div>
            </div>

            <div class="col-12">
                <label class="form-label fw-bold text-muted">Biographie</label>
                <textarea class="form-control bg-white" name="bio" rows="4"><?= htmlspecialchars($userToForm['bio']) ?></textarea>
            </div>

            <div class="col-12 text-end mt-5 border-top pt-4">
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm" style="font-size: 1.1rem;">
                    <i class="fa-solid fa-floppy-disk me-2"></i>Mettre a jour
                </button>
            </div>
        </form>
    </div>
</div>
<?php } else { ?>
    <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i>Utilisateur non trouve.</div>
<?php } ?>

<?php include_once 'footer.php'; ?>
