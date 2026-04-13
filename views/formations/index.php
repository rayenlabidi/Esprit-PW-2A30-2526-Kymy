<?php require 'views/layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1>Gestion des Formations</h1>
        <p>Gérez le catalogue des formations disponibles, ajoutez, modifiez ou supprimez des éléments.</p>
    </div>
    <a href="index.php?action=create" class="btn btn-primary">
        + Nouvelle Formation
    </a>
</div>

<div class="card">
    <?php if(count($formations) > 0): ?>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Catégorie</th>
                        <th>Prix</th>
                        <th>Durée</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($formations as $f): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($f['titre']) ?></strong></td>
                        <td>
                            <span class="badge">
                                <?= htmlspecialchars($f['categorie_nom'] ?? 'Non assignée') ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars(number_format($f['prix'], 2)) ?> €</td>
                        <td><?= htmlspecialchars($f['duree']) ?>H</td>
                        <td>
                            <div class="actions">
                                <a href="index.php?action=edit&id=<?= $f['id'] ?>" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">Modifier</a>
                                <a href="index.php?action=delete&id=<?= $f['id'] ?>" class="btn btn-danger" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette formation ?');">Supprimer</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-folder-open"><path d="m6 14 1.45-2.9A2 2 0 0 1 9.24 10H20a2 2 0 0 1 1.94 2.5l-1.55 6a2 2 0 0 1-1.94 1.5H4a2 2 0 0 1-2-2V5c0-1.1.9-2 2-2h3.93a2 2 0 0 1 1.66.9l.82 1.2a2 2 0 0 0 1.66.9H18a2 2 0 0 1 2 2v2"/></svg>
            <h3>Aucune formation trouvée</h3>
            <p>Commencez par ajouter votre première formation.</p>
        </div>
    <?php endif; ?>
</div>

<?php require 'views/layouts/footer.php'; ?>
