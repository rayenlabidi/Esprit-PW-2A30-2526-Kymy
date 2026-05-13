<?php
$isEdit = isset($category) && !empty($category['id']);
$pageTitle = $isEdit ? 'Modifier Categorie' : 'Ajouter Categorie';
$activeModule = 'events';
$office = 'back';
$formData = isset($formData) ? $formData : [];
$errors = isset($errors) ? $errors : [];
include __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <div>
        <p class="eyebrow">Workify events</p>
        <h2><?= $isEdit ? 'Modifier une categorie' : 'Nouvelle categorie'; ?></h2>
        <p class="muted">Gardez des intitules simples pour faciliter la recherche des evenements.</p>
    </div>
    <a class="btn" href="../controller/EventC.php?office=back&action=categories">Retour</a>
</div>

<form class="form-card" method="post" action="../controller/EventC.php?office=back&action=<?= $isEdit ? 'edit_category&id=' . (int) $category['id'] : 'add_category'; ?>">
    <?php if (!empty($errors)) { ?>
        <div class="error-box">
            <?php foreach ($errors as $error) { ?>
                <p><?= htmlspecialchars($error, ENT_QUOTES); ?></p>
            <?php } ?>
        </div>
    <?php } ?>

    <label>
        Nom
        <input name="name" required minlength="3" value="<?= htmlspecialchars($formData['name'] ?? '', ENT_QUOTES); ?>">
    </label>

    <label>
        Description
        <textarea name="description" required minlength="8"><?= htmlspecialchars($formData['description'] ?? '', ENT_QUOTES); ?></textarea>
    </label>

    <div class="form-actions">
        <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Enregistrer' : 'Ajouter'; ?></button>
        <a class="btn" href="../controller/EventC.php?office=back&action=categories">Annuler</a>
    </div>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
