<?php
// Vue: fiche detaillee d'un utilisateur.
include_once '../controller/UtilisateurController.php';
$pageData = (new UtilisateurController())->prepareProfilePage();
$profile = $pageData['profile'];
include_once 'header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 text-dark fw-bold" style="font-family: 'Outfit', sans-serif;">
        <i class="fa-solid fa-id-card me-2 text-primary"></i>Profil utilisateur
    </h2>
    <a href="listeUtilisateurs.php" class="btn btn-outline-secondary px-4">
        <i class="fa-solid fa-arrow-left me-2"></i>Retour
    </a>
</div>

<?php if (!$profile): ?>
    <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i>Profil introuvable.</div>
<?php else:
    $roleName = !empty($profile['role_name']) ? $profile['role_name'] : 'USER';
?>
    <div class="card border-0 mb-5 overflow-hidden shadow-lg animate-fade-in" style="border-radius: 20px; background: rgba(255, 255, 255, 0.88); backdrop-filter: blur(20px);">
        <div class="p-5 text-center" style="background: var(--primary-gradient); position: relative;">
            <div class="d-inline-block bg-white rounded-circle p-1 mb-3 shadow" style="width: 120px; height: 120px;">
                <?php if (!empty($profile['avatar_url'])): ?>
                    <img
                        src="../assets/uploads/avatars/<?= htmlspecialchars($profile['avatar_url']) ?>"
                        alt="Avatar"
                        class="w-100 h-100 rounded-circle"
                        style="object-fit: cover;"
                    >
                <?php else: ?>
                    <div class="w-100 h-100 rounded-circle d-flex align-items-center justify-content-center bg-light text-primary display-4 fw-bold">
                        <?= htmlspecialchars(strtoupper(substr($profile['first_name'], 0, 1) . substr($profile['last_name'], 0, 1))) ?>
                    </div>
                <?php endif; ?>
            </div>
            <h2 class="text-white fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">
                <?= htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']) ?>
            </h2>
            <p class="text-white-50 fs-5 mb-0"><?= htmlspecialchars(!empty($profile['headline']) ? $profile['headline'] : 'Utilisateur Workify') ?></p>
            <span class="badge bg-white text-primary mt-3 px-3 py-2" style="font-size: 0.9rem;">
                <i class="fa-solid fa-user-tag me-2"></i><?= htmlspecialchars(strtoupper($roleName)) ?>
            </span>
        </div>

        <div class="card-body p-5">
            <h5 class="fw-bold mb-4 text-primary border-bottom pb-2"><i class="fa-solid fa-address-card me-2"></i>Details du profil</h5>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="p-3 rounded-3 h-100" style="background-color: #f8fafc;">
                        <div class="text-muted small fw-bold mb-1">ID</div>
                        <div class="fw-semibold text-dark">#<?= htmlspecialchars($profile['id']) ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 rounded-3 h-100" style="background-color: #f8fafc;">
                        <div class="text-muted small fw-bold mb-1">Statut</div>
                        <div class="fw-semibold text-dark"><?= htmlspecialchars(ucfirst($profile['status'])) ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 rounded-3 h-100" style="background-color: #f8fafc;">
                        <div class="text-muted small fw-bold mb-1">Email</div>
                        <div class="fw-semibold text-dark"><?= htmlspecialchars($profile['email']) ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 rounded-3 h-100" style="background-color: #f8fafc;">
                        <div class="text-muted small fw-bold mb-1">Telephone</div>
                        <div class="fw-semibold text-dark"><?= htmlspecialchars(!empty($profile['phone']) ? $profile['phone'] : 'Non renseigne') ?></div>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <h5 class="fw-bold mb-3 text-primary"><i class="fa-solid fa-book-open me-2"></i>A propos</h5>
                <div class="p-4 rounded-3" style="background-color: #f8fafc; border-left: 4px solid #3b82f6;">
                    <p class="mb-0 text-secondary" style="line-height: 1.7;">
                        <?= nl2br(htmlspecialchars(!empty($profile['bio']) ? $profile['bio'] : "Aucune biographie n'a ete renseignee pour le moment.")) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include_once 'footer.php'; ?>
