<?php
$isEdit = isset($formData['id_formation']);
$pageTitle = $isEdit ? 'Modifier Formation' : 'Ajouter Formation';
$activeModule = 'formations';
$formData = isset($formData) ? $formData : [];
$errors = isset($errors) ? $errors : [];
$categories = isset($categories) ? $categories : [];
$formateurs = isset($formateurs) ? $formateurs : [];
$id = $isEdit ? (int) $formData['id_formation'] : 0;
$action = $isEdit ? 'edit&id=' . $id : 'add';
include __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <div>
        <p class="eyebrow">Workify Training Studio</p>
        <h2><?= $isEdit ? 'Edit training resource' : 'Create a new formation'; ?></h2>
    </div>
    <button class="btn" type="button" id="generatePlanBtn">
        <svg viewBox="0 0 24 24"><path d="M12 2l1.8 5.6L19 6l-3.2 4.6L20 14l-5.4.3L12 20l-2.6-5.7L4 14l4.2-3.4L5 6l5.2 1.6L12 2z"/></svg>
        Generer un plan
    </button>
</div>

<form class="form-box" data-validate="formation" action="../controller/FormationC.php?office=<?= $office; ?>&action=<?= $action; ?>" method="post">
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
            <input id="titre" name="titre" value="<?= htmlspecialchars(isset($formData['titre']) ? $formData['titre'] : '', ENT_QUOTES); ?>">
        </div>

        <div>
            <label for="id_categorie">Categorie</label>
            <select id="id_categorie" name="id_categorie">
                <option value="">Choisir</option>
                <?php foreach ($categories as $categorie) { ?>
                    <option value="<?= (int) $categorie['id_categorie']; ?>" <?= (isset($formData['id_categorie']) && (string) $formData['id_categorie'] === (string) $categorie['id_categorie']) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($categorie['nom_categorie'], ENT_QUOTES); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="field-full">
            <label for="description">Description / Objectifs</label>
            <textarea id="description" name="description"><?= htmlspecialchars(isset($formData['description']) ? $formData['description'] : '', ENT_QUOTES); ?></textarea>
        </div>

        <div>
            <label for="id_formateur">Formateur</label>
            <select id="id_formateur" name="id_formateur">
                <option value="">Choisir</option>
                <?php foreach ($formateurs as $formateur) { ?>
                    <option value="<?= (int) $formateur['id_formateur']; ?>" <?= (isset($formData['id_formateur']) && (string) $formData['id_formateur'] === (string) $formateur['id_formateur']) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($formateur['nom'], ENT_QUOTES); ?> - <?= htmlspecialchars($formateur['specialite'], ENT_QUOTES); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div>
            <label for="mode">Mode</label>
            <select id="mode" name="mode">
                <option value="">Choisir</option>
                <option value="Presentiel" <?= (isset($formData['mode']) && $formData['mode'] === 'Presentiel') ? 'selected' : ''; ?>>Presentiel</option>
                <option value="En ligne" <?= (isset($formData['mode']) && $formData['mode'] === 'En ligne') ? 'selected' : ''; ?>>En ligne</option>
                <option value="Hybride" <?= (isset($formData['mode']) && $formData['mode'] === 'Hybride') ? 'selected' : ''; ?>>Hybride</option>
            </select>
        </div>

        <div>
            <label for="date_debut">Date debut (YYYY-MM-DD)</label>
            <input id="date_debut" name="date_debut" value="<?= htmlspecialchars(isset($formData['date_debut']) ? $formData['date_debut'] : '', ENT_QUOTES); ?>">
        </div>

        <div>
            <label for="date_fin">Date fin (YYYY-MM-DD)</label>
            <input id="date_fin" name="date_fin" value="<?= htmlspecialchars(isset($formData['date_fin']) ? $formData['date_fin'] : '', ENT_QUOTES); ?>">
        </div>

        <div>
            <label for="duree">Duree en heures</label>
            <input id="duree" name="duree" value="<?= htmlspecialchars(isset($formData['duree']) ? $formData['duree'] : '', ENT_QUOTES); ?>">
        </div>

        <div>
            <label for="places">Places disponibles</label>
            <input id="places" name="places" value="<?= htmlspecialchars(isset($formData['places']) ? $formData['places'] : '', ENT_QUOTES); ?>">
        </div>

        <div>
            <label for="prix">Prix</label>
            <input id="prix" name="prix" value="<?= htmlspecialchars(isset($formData['prix']) ? $formData['prix'] : '', ENT_QUOTES); ?>">
        </div>

        <div>
            <label for="niveau">Niveau</label>
            <select id="niveau" name="niveau">
                <option value="">Choisir</option>
                <option value="Debutant" <?= (isset($formData['niveau']) && $formData['niveau'] === 'Debutant') ? 'selected' : ''; ?>>Debutant</option>
                <option value="Intermediaire" <?= (isset($formData['niveau']) && $formData['niveau'] === 'Intermediaire') ? 'selected' : ''; ?>>Intermediaire</option>
                <option value="Avance" <?= (isset($formData['niveau']) && $formData['niveau'] === 'Avance') ? 'selected' : ''; ?>>Avance</option>
            </select>
        </div>

        <div>
            <label for="statut">Statut</label>
            <select id="statut" name="statut">
                <option value="">Choisir</option>
                <option value="planifiee" <?= (isset($formData['statut']) && $formData['statut'] === 'planifiee') ? 'selected' : ''; ?>>Planifiee</option>
                <option value="en_cours" <?= (isset($formData['statut']) && $formData['statut'] === 'en_cours') ? 'selected' : ''; ?>>En cours</option>
                <option value="terminee" <?= (isset($formData['statut']) && $formData['statut'] === 'terminee') ? 'selected' : ''; ?>>Terminee</option>
                <option value="annulee" <?= (isset($formData['statut']) && $formData['statut'] === 'annulee') ? 'selected' : ''; ?>>Annulee</option>
            </select>
        </div>
    </div>

    <div class="actions" style="margin-top: 18px;">
        <button class="btn btn-primary" type="submit">Enregistrer</button>
        <a class="btn" href="../controller/FormationC.php?office=<?= $office; ?>&action=list">Annuler</a>
    </div>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
