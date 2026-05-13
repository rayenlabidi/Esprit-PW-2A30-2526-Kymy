<?php
// Vue admin: statistiques des utilisateurs.
include_once '../controller/UtilisateurController.php';
$pageData = (new UtilisateurController())->prepareStatsPage();
$stats = $pageData['stats'];
include_once 'header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 text-dark fw-bold" style="font-family: 'Outfit', sans-serif;">
        <i class="fa-solid fa-chart-line me-2 text-primary"></i>Statistiques
    </h2>
    <a href="listeUtilisateurs.php" class="btn btn-outline-secondary px-4">
        <i class="fa-solid fa-arrow-left me-2"></i>Retour
    </a>
</div>

<?php if (isset($stats['error'])): ?>
    <div class="alert alert-danger">
        <i class="fa-solid fa-triangle-exclamation me-2"></i>
        Erreur: <?= htmlspecialchars($stats['error']) ?>
    </div>
<?php else: ?>
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 h-100" style="background: rgba(255, 255, 255, 0.75); box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="text-muted small fw-bold mb-2">Total utilisateurs</div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="display-6 fw-bold text-dark mb-0"><?= (int)$stats['totalUsers'] ?></div>
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fa-solid fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 h-100" style="background: rgba(255, 255, 255, 0.75); box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="text-muted small fw-bold mb-2">Creations recentes</div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="fw-bold text-dark">7 jours</div>
                        <div class="badge bg-info text-dark"><?= (int)$stats['createdLast7'] ?></div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="fw-bold text-dark">30 jours</div>
                        <div class="badge bg-secondary"><?= (int)$stats['createdLast30'] ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 h-100" style="background: rgba(255, 255, 255, 0.75); box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="text-muted small fw-bold mb-2">Statut comptes</div>
                    <?php if (empty($stats['byStatus'])): ?>
                        <div class="text-muted">Aucune donnee</div>
                    <?php else: ?>
                        <?php foreach ($stats['byStatus'] as $row): ?>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="fw-bold text-dark"><?= htmlspecialchars(ucfirst((string)$row['status'])) ?></div>
                                <div class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25"><?= (int)$row['c'] ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0" style="background: rgba(255, 255, 255, 0.75); box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-radius: 16px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 text-primary"><i class="fa-solid fa-user-tag me-2"></i>Repartition par role</h5>
                    <?php if (empty($stats['byRole'])): ?>
                        <div class="text-muted">Aucune donnee</div>
                    <?php else: ?>
                        <?php foreach ($stats['byRole'] as $row): ?>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="fw-semibold text-dark"><?= htmlspecialchars((string)$row['role_name']) ?></div>
                                <div class="badge bg-primary"><?= (int)$row['c'] ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0" style="background: rgba(255, 255, 255, 0.75); box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-radius: 16px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 text-primary"><i class="fa-solid fa-clock-rotate-left me-2"></i>Derniers utilisateurs</h5>
                    <?php if (empty($stats['latestUsers'])): ?>
                        <div class="text-muted">Aucune donnee</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="border-0 text-muted">Utilisateur</th>
                                        <th class="border-0 text-muted">Role</th>
                                        <th class="border-0 text-muted">Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($stats['latestUsers'] as $u): ?>
                                        <tr>
                                            <td class="border-0">
                                                <div class="fw-bold text-dark"><?= htmlspecialchars(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')) ?></div>
                                                <div class="small text-muted"><?= htmlspecialchars((string)($u['email'] ?? '')) ?></div>
                                            </td>
                                            <td class="border-0">
                                                <span class="badge bg-info text-dark"><?= htmlspecialchars((string)($u['role_name'] ?? '')) ?></span>
                                            </td>
                                            <td class="border-0">
                                                <span class="badge bg-secondary"><?= htmlspecialchars((string)($u['status'] ?? '')) ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include_once 'footer.php'; ?>
