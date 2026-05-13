<?php
$pageTitle = 'Categories Evenements';
$activeModule = 'events';
$office = 'back';
$categories = isset($categories) ? $categories : [];
include __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <div>
        <p class="eyebrow">Workify events</p>
        <h2>Categories des evenements</h2>
        <p class="muted">Organisez les evenements par themes pour garder le filtrage clair.</p>
    </div>
    <div class="toolbar-actions">
        <a class="btn" href="../controller/EventC.php?office=back&action=list">Evenements</a>
        <a class="btn btn-primary" href="../controller/EventC.php?office=back&action=add_category">Ajouter categorie</a>
    </div>
</div>

<div class="table-box">
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($categories)) { ?>
                <tr><td colspan="3">Aucune categorie trouvee.</td></tr>
            <?php } ?>
            <?php foreach ($categories as $category) { ?>
                <tr>
                    <td data-label="Nom"><strong><?= htmlspecialchars($category['name'], ENT_QUOTES); ?></strong></td>
                    <td data-label="Description"><?= htmlspecialchars($category['description'], ENT_QUOTES); ?></td>
                    <td data-label="Actions" class="actions">
                        <a class="btn" href="../controller/EventC.php?office=back&action=edit_category&id=<?= (int) $category['id']; ?>">Modifier</a>
                        <a class="btn btn-danger" href="../controller/EventC.php?office=back&action=delete_category&id=<?= (int) $category['id']; ?>" onclick="return confirm('Supprimer cette categorie ? Les evenements resteront sans categorie.');">Supprimer</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
