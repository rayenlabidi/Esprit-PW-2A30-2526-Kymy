<?php
// Vue principale: liste des utilisateurs.
// L'admin voit le tableau complet; un utilisateur normal voit seulement son profil.
include_once '../controller/UtilisateurController.php';

$pageData = (new UtilisateurController())->prepareListPage();

// Récupération des paramètres de recherche et tri
$search = $pageData['search'];
$sort = $pageData['sort'];

$liste = $pageData['liste'];

include_once 'header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 text-dark fw-bold" style="font-family: 'Outfit', sans-serif;"><i class="fa-solid fa-users me-2 text-primary"></i>Gestion des utilisateurs</h2>
    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
        <div class="d-flex align-items-center gap-2">
            <a href="../controller/UtilisateurRouter.php?action=export-pdf&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort) ?>" class="btn btn-outline-secondary px-4 py-2">
                <i class="fa-solid fa-file-pdf me-2"></i> Exporter PDF
            </a>
            <a href="stats.php" class="btn btn-outline-primary px-4 py-2"><i class="fa-solid fa-chart-simple me-2"></i> Statistiques</a>
            <a href="ajoutUtilisateur.php" class="btn btn-primary px-4 py-2"><i class="fa-solid fa-plus me-2"></i> Nouvel utilisateur</a>
        </div>
    <?php endif; ?>
</div>

<?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
<!-- Barre de recherche et filtres pour l'admin -->
<form method="GET" action="listeUtilisateurs.php" class="mb-4 p-3 rounded" style="background: rgba(255,255,255,0.4); border: 1px solid rgba(255,255,255,0.5);">
    <div class="row g-3">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" name="search" class="form-control search-input" placeholder="Rechercher par nom, email ou ID..." value="<?= htmlspecialchars($search) ?>">
            </div>
        </div>
        <div class="col-md-4">
            <select name="sort" class="form-select">
                <option value="date_desc" <?= $sort === 'date_desc' ? 'selected' : '' ?>>Plus récents en premier</option>
                <option value="date_asc" <?= $sort === 'date_asc' ? 'selected' : '' ?>>Plus anciens en premier</option>
                <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?>>Ordre alphabétique (A-Z)</option>
                <option value="name_desc" <?= $sort === 'name_desc' ? 'selected' : '' ?>>Ordre alphabétique (Z-A)</option>
                <option value="role" <?= $sort === 'role' ? 'selected' : '' ?>>Par rôle</option>
            </select>
        </div>
        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter me-2"></i>Filtrer</button>
        </div>
    </div>
</form>
<?php endif; ?>

<?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
<!-- Vue Administrateur : Tableau Complet -->
<div class="table-responsive bg-white rounded-4 shadow-sm p-3 mb-5">
    <table class="table table-hover align-middle mb-0">
        <thead>
            <tr>
                <th class="border-0">ID</th>
                <th class="border-0">Rôle</th>
                <th class="border-0">Utilisateur</th>
                <th class="border-0">Contact</th>
                <th class="border-0">Statut</th>
                <th class="border-0 text-center">QR</th>
                <th class="text-end border-0">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($liste)): ?>
            <tr>
                <td colspan="7" class="text-center py-5 text-muted">
                    <i class="fa-solid fa-folder-open mb-3" style="font-size: 3rem; color: #93c5fd;"></i>
                    <h5 class="fw-bold">Aucun utilisateur trouvé</h5>
                    <p>Essayez de modifier vos critères de recherche.</p>
                </td>
            </tr>
            <?php else: ?>
                <?php foreach ($liste as $u): 
                    $roleName = !empty($u['role_name']) ? $u['role_name'] : 'USER';
                    $profileUrl = $u['profile_url'] ?? ('profile.php?id=' . urlencode((string)$u['id']));
                    $fullName = $u['full_name'] ?? trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
                    $qrPayload = $u['qr_payload'] ?? $profileUrl;
                ?>
                    <tr>
                        <td><span class="badge bg-secondary">#<?= htmlspecialchars($u['id']); ?></span></td>
                        <td>
                            <span class="badge <?= strtolower($roleName) === 'admin' ? 'bg-danger' : 'bg-info text-dark' ?>">
                                <?= htmlspecialchars(strtoupper($roleName)); ?>
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold" style="color: #111827;"><?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']); ?></div>
                            <div class="small" style="color: #6b7280;"><?= htmlspecialchars(!empty($u['headline']) ? $u['headline'] : 'Sans titre'); ?></div>
                        </td>
                        <td>
                            <div style="color: #4b5563; font-weight: 500;"><i class="fa-solid fa-envelope me-2 text-primary"></i><?= htmlspecialchars($u['email']); ?></div>
                            <div class="small text-muted mt-1"><i class="fa-solid fa-phone me-2 text-primary"></i><?= htmlspecialchars(!empty($u['phone']) ? $u['phone'] : 'Non renseigné'); ?></div>
                        </td>
                        <td>
                            <?php if($u['status'] === 'active'): ?>
                                <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i>Actif</span>
                            <?php else: ?>
                                <span class="badge bg-warning"><i class="fa-solid fa-circle-xmark me-1"></i>Inactif</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <button
                                type="button"
                                class="btn btn-sm btn-light text-primary border shadow-sm js-open-qr"
                                data-qr-payload="<?= htmlspecialchars($qrPayload, ENT_QUOTES, 'UTF-8') ?>"
                                data-qr-url="<?= htmlspecialchars($profileUrl, ENT_QUOTES, 'UTF-8') ?>"
                                data-user-name="<?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') ?>"
                                title="Afficher le QR code personnel"
                                aria-label="Afficher le QR code personnel"
                            >
                                <i class="fa-solid fa-qrcode"></i>
                            </button>
                        </td>
                        <td class="text-end">
                            <a href="profile.php?id=<?= $u['id']; ?>" class="btn btn-sm btn-light text-success border me-1 shadow-sm" title="Voir le profil"><i class="fa-solid fa-id-card"></i></a>
                            <a href="update.php?id=<?= $u['id']; ?>" class="btn btn-sm btn-light text-primary border me-1 shadow-sm"><i class="fa-solid fa-pen"></i></a> 
                            <a href="../controller/UtilisateurRouter.php?action=delete&id=<?= $u['id']; ?>" class="btn btn-sm btn-light text-danger border shadow-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px;">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-primary" id="qrModalLabel">
                    <i class="fa-solid fa-qrcode me-2"></i>QR code personnel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body text-center px-4 pb-4">
                <div id="qrUserName" class="fw-bold mb-3"></div>
                <div id="qrCodeBox" class="d-inline-block p-3 rounded-4 bg-white border"></div>
                <div class="mt-3">
                    <a id="qrProfileLink" href="#" class="fw-bold text-primary" target="_blank" rel="noopener">
                        Ouvrir le profil
                    </a>
                </div>
                <div class="small text-muted mt-2">Scannez ce code pour lire les informations personnelles et le lien profil.</div>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/qrcode.min.js"></script>
<script>
    // Au clic, on genere le QR code a partir du contenu vCard prepare par le service QR.
    document.querySelectorAll('.js-open-qr').forEach((button) => {
        button.addEventListener('click', () => {
            const qrPayload = button.dataset.qrPayload || '';
            const qrUrl = button.dataset.qrUrl || '';
            const userName = button.dataset.userName || 'Utilisateur';
            const qrBox = document.getElementById('qrCodeBox');
            const qrUserName = document.getElementById('qrUserName');
            const qrProfileLink = document.getElementById('qrProfileLink');

            if (!qrBox || !qrUserName || !qrProfileLink || !qrPayload) {
                return;
            }

            qrBox.innerHTML = '';
            qrUserName.textContent = userName;
            qrProfileLink.href = qrUrl;

            if (window.QRCode) {
                new QRCode(qrBox, {
                    text: qrPayload,
                    width: 210,
                    height: 210,
                    colorDark: '#111827',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.Q
                });
            } else {
                qrBox.innerHTML = '<div class="text-muted small">Bibliotheque QR indisponible.</div>';
            }

            bootstrap.Modal.getOrCreateInstance(document.getElementById('qrModal')).show();
        });
    });
</script>

<?php else: ?>

<!-- Vue Utilisateur Normal : Carte de Profil Premium -->
<div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">
        <?php foreach ($liste as $u) { 
            // On trouve le profil de l'utilisateur connecté
            if ($u['id'] == $_SESSION['user_id']) { 
                $roleName = !empty($u['role_name']) ? $u['role_name'] : 'USER';
        ?>
        <div class="card border-0 mb-5 overflow-hidden shadow-lg animate-fade-in" style="border-radius: 20px; background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(20px);">
            <!-- En-tête du profil -->
            <div class="p-5 text-center" style="background: var(--primary-gradient); position: relative;">
                <div class="d-inline-block bg-white rounded-circle p-1 mb-3 shadow" style="width: 120px; height: 120px;">
                    <?php if (!empty($u['avatar_url'])): ?>
                        <img
                            src="../assets/uploads/avatars/<?= htmlspecialchars($u['avatar_url']) ?>"
                            alt="Avatar"
                            class="w-100 h-100 rounded-circle"
                            style="object-fit: cover;"
                        >
                    <?php else: ?>
                        <div class="w-100 h-100 rounded-circle d-flex align-items-center justify-content-center bg-light text-primary display-4 fw-bold">
                            <?= strtoupper(substr($u['first_name'], 0, 1) . substr($u['last_name'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'user' && (int)($_SESSION['user_id'] ?? 0) === (int)$u['id']): ?>
                    <form action="../controller/UtilisateurRouter.php?action=upload-avatar" method="POST" enctype="multipart/form-data" class="d-inline-flex align-items-center justify-content-center mt-2" id="avatarForm">
                        <input
                            type="file"
                            name="avatar"
                            accept="image/jpeg,image/png,image/webp"
                            required
                            style="position:absolute; left:-9999px; width:1px; height:1px; overflow:hidden;"
                            id="avatarInput"
                        >
                        <button
                            type="button"
                            class="btn btn-sm btn-light fw-bold"
                            onclick="document.getElementById('avatarInput').click();"
                        >
                            <i class="fa-solid fa-camera me-2"></i>Changer
                        </button>
                    </form>
                    <script>
                        (function () {
                            var input = document.getElementById('avatarInput');
                            var form = document.getElementById('avatarForm');
                            if (!input || !form) return;
                            input.addEventListener('change', function () {
                                if (input.files && input.files.length > 0) form.submit();
                            });
                        })();
                    </script>
                <?php endif; ?>
                <h2 class="text-white fw-bold mb-1" style="font-family: 'Outfit', sans-serif;"><?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']); ?></h2>
                <p class="text-white-50 fs-5 mb-0"><?= htmlspecialchars(!empty($u['headline']) ? $u['headline'] : 'Utilisateur Workify'); ?></p>
                <span class="badge bg-white text-primary mt-3 px-3 py-2" style="font-size: 0.9rem;">
                    <i class="fa-solid fa-user-tag me-2"></i><?= htmlspecialchars(strtoupper($roleName)); ?>
                </span>
            </div>

            <!-- Informations Personnelles -->
            <div class="card-body p-5">
                <h5 class="fw-bold mb-4 text-primary border-bottom pb-2"><i class="fa-solid fa-id-card me-2"></i>Informations Personnelles</h5>
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary me-3">
                                <i class="fa-solid fa-envelope fs-5"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-0 small fw-bold">Adresse Email</p>
                                <p class="mb-0 fw-semibold text-dark"><?= htmlspecialchars($u['email']); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary me-3">
                                <i class="fa-solid fa-phone fs-5"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-0 small fw-bold">Téléphone</p>
                                <p class="mb-0 fw-semibold text-dark"><?= htmlspecialchars(!empty($u['phone']) ? $u['phone'] : 'Non renseigné'); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary me-3">
                                <i class="fa-solid fa-shield-halved fs-5"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-0 small fw-bold">Statut du Compte</p>
                                <p class="mb-0 fw-semibold text-dark">
                                    <?php if($u['status'] === 'active'): ?>
                                        <span class="text-success"><i class="fa-solid fa-circle-check me-1"></i>Actif</span>
                                    <?php else: ?>
                                        <span class="text-warning"><i class="fa-solid fa-circle-xmark me-1"></i>Inactif</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5">
                    <h5 class="fw-bold mb-3 text-primary"><i class="fa-solid fa-book-open me-2"></i>À propos de moi</h5>
                    <div class="p-4 rounded-3" style="background-color: #f8fafc; border-left: 4px solid #3b82f6;">
                        <p class="mb-0 text-secondary" style="line-height: 1.7;">
                            <?= nl2br(htmlspecialchars(!empty($u['bio']) ? $u['bio'] : "Aucune biographie n'a été renseignée pour le moment.")); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <?php 
                break; // On a trouvé l'utilisateur, on arrête la boucle
            } 
        } 
        ?>
    </div>
</div>

<?php endif; ?>

<?php include_once 'footer.php'; ?>
