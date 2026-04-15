<?php $pageTitle = $mode === 'create' ? 'Offrir un job' : 'Modifier le job'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="page-head">
    <div>
        <p class="eyebrow">Gestion des jobs</p>
        <h1><?= $mode === 'create' ? 'Offrir un job' : 'Modifier le job' ?></h1>
    </div>
    <a class="btn btn-outline" href="<?= url(['module' => 'jobs', 'action' => 'index']) ?>">Retour</a>
</section>

<section class="section-card form-card">
    <form method="POST" action="<?= $mode === 'create' ? url(['module' => 'jobs', 'action' => 'create']) : url(['module' => 'jobs', 'action' => 'edit', 'id' => $job['id']]) ?>" class="stack-form js-validate" novalidate>
        <div class="form-group">
            <label for="title">Titre du job</label>
            <input id="title" name="title" class="form-control" type="text" value="<?= h($job['title'] ?? '') ?>" data-label="Titre du job" data-required="1" data-minlength="4">
            <small class="field-error"></small>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-control" data-label="Description" data-required="1" data-minlength="20"><?= h($job['description'] ?? '') ?></textarea>
            <small class="field-error"></small>
        </div>
        <div class="form-grid">
            <div class="form-group">
                <label for="budget">Budget / salaire</label>
                <input id="budget" name="budget" class="form-control" type="number" min="0" step="0.01" value="<?= h($job['budget'] ?? '') ?>" data-label="Budget" data-required="1" data-positive="1">
                <small class="field-error"></small>
            </div>
            <div class="form-group">
                <label for="location">Localisation</label>
                <input id="location" name="location" class="form-control" type="text" value="<?= h($job['location'] ?? '') ?>" data-label="Localisation" data-required="1" data-minlength="2">
                <small class="field-error"></small>
            </div>
        </div>
        <div class="form-grid">
            <div class="form-group">
                <label for="category_id">Categorie</label>
                <select id="category_id" name="category_id" class="form-control" data-label="Categorie" data-required="1">
                    <option value="">Choisir</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= (($job['category_id'] ?? '') == $category['id']) ? 'selected' : '' ?>><?= h($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="field-error"></small>
            </div>
            <div class="form-group">
                <label for="job_type">Type</label>
                <select id="job_type" name="job_type" class="form-control" data-label="Type" data-required="1">
                    <?php foreach (['Freelance', 'Full-time', 'Stage', 'Part-time'] as $type): ?>
                        <option value="<?= $type ?>" <?= (($job['job_type'] ?? 'Freelance') === $type) ? 'selected' : '' ?>><?= $type ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="field-error"></small>
            </div>
        </div>
        <div class="form-grid">
            <div class="form-group">
                <label for="status">Statut</label>
                <select id="status" name="status" class="form-control" data-label="Statut" data-required="1">
                    <?php foreach (['open', 'draft', 'closed'] as $status): ?>
                        <option value="<?= $status ?>" <?= (($job['status'] ?? 'open') === $status) ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="field-error"></small>
            </div>
            <div class="form-group">
                <label for="publisher_id">Publie par</label>
                <select id="publisher_id" name="publisher_id" class="form-control" data-label="Publie par" data-required="<?= has_role(['admin']) ? '1' : '0' ?>">
                    <?php foreach ($publishers as $publisher): ?>
                        <?php $selectedPublisher = $job['publisher_id'] ?? auth_user()['id']; ?>
                        <option value="<?= $publisher['id'] ?>" <?= ((int) $selectedPublisher === (int) $publisher['id']) ? 'selected' : '' ?>><?= h($publisher['first_name'] . ' ' . $publisher['last_name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="field-error"></small>
            </div>
        </div>
        <label class="checkbox-row">
            <input type="checkbox" name="is_remote" value="1" <?= !empty($job['is_remote']) ? 'checked' : '' ?>>
            Job remote
        </label>
        <button class="btn btn-primary" type="submit"><?= $mode === 'create' ? 'Publier le job' : 'Enregistrer les changements' ?></button>
    </form>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
