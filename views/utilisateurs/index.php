<?php require 'views/layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1>Gestion des Utilisateurs</h1>
        <p>Gérez les membres de la plateforme (Formateurs, Étudiants, Admins).</p>
    </div>
    <a href="index.php?controller=utilisateur&action=create" class="btn btn-primary">
        + Nouvel Utilisateur
    </a>
</div>

<div class="card">
    <?php if(count($utilisateurs) > 0): ?>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nom Complet</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Date d'inscription</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($utilisateurs as $u): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?></strong></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <span class="badge" style="background-color: #fef3c7; color: #b45309;">
                                <?= htmlspecialchars($u['role_nom'] ?? 'Aucun rôle') ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y', strtotime($u['date_inscription'])) ?></td>
                        <td>
                            <div class="actions">
                                <a href="index.php?controller=utilisateur&action=edit&id=<?= $u['id'] ?>" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">Modifier</a>
                                <a href="index.php?controller=utilisateur&action=delete&id=<?= $u['id'] ?>" class="btn btn-danger" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;" onclick="return confirm('Supprimer cet utilisateur ?');">Supprimer</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <h3>Aucun utilisateur trouvé</h3>
            <p>Commencez par ajouter votre premier membre.</p>
        </div>
    <?php endif; ?>
</div>

<?php require 'views/layouts/footer.php'; ?>
