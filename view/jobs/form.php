<?php
$isEdit = isset($formData['id']);
$pageTitle = $isEdit ? 'Modifier Job' : 'Ajouter Job';
$activeModule = 'jobs';
$formData = isset($formData) ? $formData : [];
$errors = isset($errors) ? $errors : [];
$categories = isset($categories) ? $categories : [];
$publishers = isset($publishers) ? $publishers : [];
$id = $isEdit ? (int) $formData['id'] : 0;
$action = $isEdit ? 'edit&id=' . $id : 'add';
include __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <div>
        <p class="eyebrow">Workify Job Studio</p>
        <h2><?= $isEdit ? 'Edit job opportunity' : 'Create a new job'; ?></h2>
    </div>
    <a class="btn" href="../controller/JobC.php?office=back&action=list">Retour</a>
</div>

<form class="form-box" data-validate="job" action="../controller/JobC.php?office=back&action=<?= $action; ?>" method="post">
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
            <label for="titre">Titre</label>
            <input id="titre" name="titre" value="<?= htmlspecialchars(isset($formData['title']) ? $formData['title'] : (isset($formData['titre']) ? $formData['titre'] : ''), ENT_QUOTES); ?>">
        </div>

        <div>
            <label for="id_categorie">Categorie</label>
            <select id="id_categorie" name="id_categorie">
                <option value="">Choisir</option>
                <?php foreach ($categories as $categorie) { ?>
                    <?php $selectedCategory = isset($formData['category_id']) ? $formData['category_id'] : (isset($formData['id_categorie']) ? $formData['id_categorie'] : ''); ?>
                    <option value="<?= (int) $categorie['id']; ?>" <?= ((string) $selectedCategory === (string) $categorie['id']) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($categorie['name'], ENT_QUOTES); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="field-full">
            <label for="description">Description / Mission</label>
            <textarea id="description" name="description"><?= htmlspecialchars(isset($formData['description']) ? $formData['description'] : '', ENT_QUOTES); ?></textarea>
        </div>

        <div>
            <label for="budget">Budget</label>
            <input id="budget" name="budget" value="<?= htmlspecialchars(isset($formData['budget']) ? $formData['budget'] : '', ENT_QUOTES); ?>">
        </div>

        <div>
            <label for="localisation">Localisation</label>
            <input id="localisation" name="localisation" value="<?= htmlspecialchars(isset($formData['location']) ? $formData['location'] : (isset($formData['localisation']) ? $formData['localisation'] : ''), ENT_QUOTES); ?>">
        </div>

        <div>
            <label for="type">Type</label>
            <select id="type" name="type">
                <?php $selectedType = isset($formData['job_type']) ? $formData['job_type'] : (isset($formData['type']) ? $formData['type'] : 'Freelance'); ?>
                <?php foreach (['Freelance', 'Full-time', 'Stage', 'Part-time'] as $typeOption) { ?>
                    <option value="<?= htmlspecialchars($typeOption, ENT_QUOTES); ?>" <?= $selectedType === $typeOption ? 'selected' : ''; ?>><?= htmlspecialchars($typeOption, ENT_QUOTES); ?></option>
                <?php } ?>
            </select>
        </div>

        <div>
            <label for="statut">Statut</label>
            <select id="statut" name="statut">
                <?php $selectedStatus = isset($formData['status']) ? $formData['status'] : (isset($formData['statut']) ? $formData['statut'] : 'open'); ?>
                <option value="open" <?= $selectedStatus === 'open' ? 'selected' : ''; ?>>Ouvert</option>
                <option value="draft" <?= $selectedStatus === 'draft' ? 'selected' : ''; ?>>Brouillon</option>
                <option value="closed" <?= $selectedStatus === 'closed' ? 'selected' : ''; ?>>Ferme</option>
            </select>
        </div>

        <div>
            <label for="id_publisher">Publie par</label>
            <select id="id_publisher" name="id_publisher">
                <option value="">Choisir</option>
                <?php foreach ($publishers as $publisher) { ?>
                    <?php $selectedPublisher = isset($formData['publisher_id']) ? $formData['publisher_id'] : (isset($formData['id_publisher']) ? $formData['id_publisher'] : ''); ?>
                    <option value="<?= (int) $publisher['id']; ?>" <?= ((string) $selectedPublisher === (string) $publisher['id']) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($publisher['first_name'] . ' ' . $publisher['last_name'], ENT_QUOTES); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <label class="inline-check field-full">
            <?php $isRemote = isset($formData['is_remote']) ? (int) $formData['is_remote'] : (isset($formData['remote']) ? 1 : 0); ?>
            <input type="checkbox" name="remote" value="1" <?= $isRemote ? 'checked' : ''; ?>>
            Job remote
        </label>
    </div>

    <div class="actions" style="margin-top: 18px;">
        <button class="btn btn-primary" type="submit">Enregistrer</button>
        <a class="btn" href="../controller/JobC.php?office=back&action=list">Annuler</a>
    </div>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
