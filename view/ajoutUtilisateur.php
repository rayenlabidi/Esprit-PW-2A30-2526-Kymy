<?php
include_once '../controller/UtilisateurController.php';
$pageData = (new UtilisateurController())->prepareAddPage();
$roles = $pageData['roles'];
include_once 'header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 text-dark fw-bold" style="font-family: 'Outfit', sans-serif;"><i class="fa-solid fa-user-plus me-2 text-primary"></i>Ajouter un Utilisateur</h2>
    <a href="listeUtilisateurs.php" class="btn btn-outline-secondary px-4"><i class="fa-solid fa-arrow-left me-2"></i>Retour a la liste</a>
</div>

<div class="card mb-5 animate-fade-in border-0" style="background: rgba(255, 255, 255, 0.7); box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
    <div class="card-body p-4 p-md-5">
        <form action="../controller/UtilisateurRouter.php?action=add" method="POST" class="row g-4">
            <div class="col-md-6">
                <label class="form-label fw-bold text-muted">Role</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fa-solid fa-user-shield text-primary"></i></span>
                    <select name="role_id" class="form-select bg-white" required>
                        <option value="">Selectionner un role...</option>
                        <?php foreach ($roles as $role) { ?>
                            <option value="<?= $role['id'] ?>"><?= htmlspecialchars(ucfirst($role['name'])) ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold text-muted">Statut</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fa-solid fa-toggle-on text-success"></i></span>
                    <select name="status" class="form-select bg-white">
                        <option value="active">Actif</option>
                        <option value="blocked">Inactif</option>
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold text-muted">Prenom</label>
                <input type="text" class="form-control bg-white" name="first_name" placeholder="Ex: Jean" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold text-muted">Nom</label>
                <input type="text" class="form-control bg-white" name="last_name" placeholder="Ex: Dupont" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold text-muted">Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fa-solid fa-envelope text-primary"></i></span>
                    <input type="email" class="form-control bg-white" name="email" placeholder="jean.dupont@workify.com" required>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold text-muted">Telephone</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fa-solid fa-phone text-primary"></i></span>
                    <input type="tel" class="form-control bg-white" name="phone" placeholder="+33 6 12 34 56 78">
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold text-muted">Mot de passe</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fa-solid fa-lock text-primary"></i></span>
                    <input type="password" class="form-control bg-white" name="password" placeholder="Mot de passe securise" required>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold text-muted">Titre professionnel (Headline)</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fa-solid fa-briefcase text-primary"></i></span>
                    <input type="text" class="form-control bg-white" name="headline" placeholder="Ex: Developpeur Full-Stack" required>
                </div>
            </div>

            <div class="col-12">
                <label class="form-label fw-bold text-muted">Biographie</label>
                <textarea class="form-control bg-white" name="bio" rows="4" placeholder="Quelques mots sur l'utilisateur..." required></textarea>
            </div>

            <div class="col-12 text-end mt-5 border-top pt-4">
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm" style="font-size: 1.1rem;">
                    <i class="fa-solid fa-check me-2"></i>Creer l'utilisateur
                </button>
            </div>
        </form>
    </div>
</div>

<?php include_once 'footer.php'; ?>
