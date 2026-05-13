<?php
$isEdit = isset($formData['id']);
$pageTitle = $isEdit ? 'Modifier Evenement' : 'Ajouter Evenement';
$activeModule = 'events';
$formData = isset($formData) ? $formData : [];
$errors = isset($errors) ? $errors : [];
$categories = isset($categories) ? $categories : [];
$id = $isEdit ? (int) $formData['id'] : 0;
$action = $isEdit ? 'edit&id=' . $id : 'add';
include __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <div>
        <p class="eyebrow">Workify events</p>
        <h2><?= $isEdit ? 'Modifier l evenement' : 'Creer un evenement'; ?></h2>
    </div>
    <a class="btn" href="../controller/EventC.php?office=back&action=list">Retour</a>
</div>

<form class="form-box" data-validate="event" action="../controller/EventC.php?office=back&action=<?= $action; ?>" method="post" enctype="multipart/form-data">
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
            <label for="title">Titre</label>
            <input id="title" name="title" value="<?= htmlspecialchars($formData['title'] ?? '', ENT_QUOTES); ?>">
        </div>

        <div>
            <label for="category_id">Categorie</label>
            <select id="category_id" name="category_id">
                <option value="">Choisir</option>
                <?php foreach ($categories as $category) { ?>
                    <?php $selected = $formData['event_category_id'] ?? ($formData['category_id'] ?? ''); ?>
                    <option value="<?= (int) $category['id']; ?>" <?= (string) $selected === (string) $category['id'] ? 'selected' : ''; ?>><?= htmlspecialchars($category['name'], ENT_QUOTES); ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="field-full">
            <label for="description">Description</label>
            <textarea id="description" name="description"><?= htmlspecialchars($formData['description'] ?? '', ENT_QUOTES); ?></textarea>
        </div>

        <div>
            <label for="event_date">Date</label>
            <?php $dateValue = !empty($formData['event_date']) ? date('Y-m-d\TH:i', strtotime($formData['event_date'])) : ''; ?>
            <input id="event_date" name="event_date" type="datetime-local" value="<?= htmlspecialchars($dateValue, ENT_QUOTES); ?>">
        </div>

        <div>
            <label for="location">Lieu</label>
            <input id="location" name="location" value="<?= htmlspecialchars($formData['location'] ?? '', ENT_QUOTES); ?>">
        </div>

        <div>
            <label for="max_participants">Participants max</label>
            <input id="max_participants" name="max_participants" value="<?= htmlspecialchars($formData['max_participants'] ?? '50', ENT_QUOTES); ?>">
        </div>

        <div>
            <label for="status">Statut</label>
            <?php $selectedStatus = $formData['status'] ?? 'upcoming'; ?>
            <select id="status" name="status">
                <option value="upcoming" <?= $selectedStatus === 'upcoming' ? 'selected' : ''; ?>>A venir</option>
                <option value="ongoing" <?= $selectedStatus === 'ongoing' ? 'selected' : ''; ?>>En cours</option>
                <option value="completed" <?= $selectedStatus === 'completed' ? 'selected' : ''; ?>>Termine</option>
                <option value="cancelled" <?= $selectedStatus === 'cancelled' ? 'selected' : ''; ?>>Annule</option>
            </select>
        </div>

        <div>
            <label for="image_file">Image depuis PC</label>
            <input id="image_file" name="image_file" type="file" accept="image/jpeg,image/png,image/webp">
        </div>

        <div>
            <label for="image_url">Image URL</label>
            <input id="image_url" name="image_url" type="url" value="<?= htmlspecialchars($formData['image_url'] ?? '', ENT_QUOTES); ?>">
        </div>

        <div>
            <label for="latitude">Latitude</label>
            <input id="latitude" name="latitude" value="<?= htmlspecialchars($formData['latitude'] ?? '', ENT_QUOTES); ?>">
        </div>

        <div>
            <label for="longitude">Longitude</label>
            <input id="longitude" name="longitude" value="<?= htmlspecialchars($formData['longitude'] ?? '', ENT_QUOTES); ?>">
        </div>

        <label class="inline-check field-full">
            <input type="checkbox" name="is_online" value="1" <?= !empty($formData['is_online']) ? 'checked' : ''; ?>>
            Evenement en ligne
        </label>
    </div>

    <div class="actions" style="margin-top: 18px;">
        <button class="btn btn-primary" type="submit">Enregistrer</button>
        <a class="btn" href="../controller/EventC.php?office=back&action=list">Annuler</a>
    </div>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
