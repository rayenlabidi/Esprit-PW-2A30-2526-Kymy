<?php $pageTitle = $mode === 'create' ? t('formations.new') : t('formations.edit_title'); require __DIR__ . '/../layouts/header.php'; ?>

<section class="page-head">
    <div>
        <p class="eyebrow"><?= t('formations.module') ?></p>
        <h1><?= $mode === 'create' ? t('formations.add') : t('formations.edit_title') ?></h1>
    </div>
    <a class="btn btn-outline" href="<?= url(['module' => 'formations', 'action' => 'backoffice']) ?>"><?= t('formations.back') ?></a>
</section>

<section class="section-card form-card">
    <form method="POST" action="<?= $mode === 'create' ? url(['module' => 'formations', 'action' => 'create']) : url(['module' => 'formations', 'action' => 'edit', 'id' => $formation['id']]) ?>" class="stack-form js-validate" novalidate>
        <div class="form-group">
            <label for="title"><?= t('formations.label_title') ?></label>
            <input id="title" name="title" class="form-control" type="text" value="<?= h($formation['title'] ?? '') ?>" data-label="<?= h(t('formations.label_title')) ?>" data-required="1" data-minlength="4">
            <small class="field-error"></small>
        </div>
        <div class="form-group">
            <label for="description"><?= t('formations.label_description') ?></label>
            <textarea id="description" name="description" class="form-control" data-label="<?= h(t('formations.label_description')) ?>" data-required="1" data-minlength="20"><?= h($formation['description'] ?? '') ?></textarea>
            <small class="field-error"></small>
        </div>
        <div class="form-grid">
            <div class="form-group">
                <label for="category_id"><?= t('formations.label_category') ?></label>
                <select id="category_id" name="category_id" class="form-control" data-label="<?= h(t('formations.label_category')) ?>" data-required="1">
                    <option value=""><?= t('formations.choose') ?></option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= (($formation['category_id'] ?? '') == $category['id']) ? 'selected' : '' ?>><?= h($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="field-error"></small>
            </div>
            <div class="form-group">
                <label for="level"><?= t('formations.level') ?></label>
                <select id="level" name="level" class="form-control" data-label="<?= h(t('formations.level')) ?>" data-required="1">
                    <?php foreach (['Beginner', 'Intermediate', 'Advanced'] as $level): ?>
                        <option value="<?= $level ?>" <?= (($formation['level'] ?? 'Beginner') === $level) ? 'selected' : '' ?>><?= $level ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="field-error"></small>
            </div>
        </div>
        <div class="form-grid">
            <div class="form-group">
                <label for="price"><?= t('formations.label_price') ?></label>
                <input id="price" name="price" class="form-control" type="number" min="0" step="0.01" value="<?= h($formation['price'] ?? '') ?>" data-label="<?= h(t('formations.label_price')) ?>" data-required="1" data-positive="1">
                <small class="field-error"></small>
            </div>
            <div class="form-group">
                <label for="duration_hours"><?= t('formations.label_duration') ?></label>
                <input id="duration_hours" name="duration_hours" class="form-control" type="number" min="1" step="1" value="<?= h($formation['duration_hours'] ?? '') ?>" data-label="<?= h(t('formations.label_duration')) ?>" data-required="1" data-positive="1">
                <small class="field-error"></small>
            </div>
        </div>
        <div class="form-grid">
            <div class="form-group">
                <label for="status"><?= t('formations.label_status') ?></label>
                <select id="status" name="status" class="form-control" data-label="<?= h(t('formations.label_status')) ?>" data-required="1">
                    <?php foreach (['draft', 'published', 'archived'] as $status): ?>
                        <option value="<?= $status ?>" <?= (($formation['status'] ?? 'draft') === $status) ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="field-error"></small>
            </div>
            <div class="form-group">
                <label for="creator_id"><?= t('formations.label_creator') ?></label>
                <select id="creator_id" name="creator_id" class="form-control" data-label="<?= h(t('formations.label_creator')) ?>" data-required="<?= has_role(['admin']) ? '1' : '0' ?>">
                    <?php foreach ($creators as $creator): ?>
                        <?php $selectedCreator = $formation['creator_id'] ?? auth_user()['id']; ?>
                        <option value="<?= $creator['id'] ?>" <?= ((int) $selectedCreator === (int) $creator['id']) ? 'selected' : '' ?>><?= h($creator['first_name'] . ' ' . $creator['last_name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="field-error"></small>
            </div>
        </div>
        <div class="form-grid">
            <div class="form-group">
                <label for="image_url"><?= t('formations.label_image') ?></label>
                <input id="image_url" name="image_url" class="form-control" type="url" value="<?= h($formation['image_url'] ?? '') ?>" data-label="<?= h(t('formations.label_image')) ?>">
                <small class="field-error"></small>
            </div>
            <div class="form-group">
                <label for="tags"><?= t('formations.label_tags') ?></label>
                <input id="tags" name="tags" class="form-control" type="text" value="<?= h($formation['tags'] ?? '') ?>" data-label="<?= h(t('formations.label_tags')) ?>" data-required="1" data-minlength="3">
                <small class="field-error"></small>
            </div>
        </div>
        <button class="btn btn-primary" type="submit"><?= $mode === 'create' ? t('formations.publish') : t('formations.save') ?></button>
    </form>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
