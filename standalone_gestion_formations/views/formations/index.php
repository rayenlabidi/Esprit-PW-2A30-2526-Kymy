<?php $pageTitle = t('formations.title'); require __DIR__ . '/../layouts/header.php'; ?>

<section class="page-head">
    <div>
        <p class="eyebrow"><?= t('formations.module') ?></p>
        <h1><?= t('formations.title') ?></h1>
        <p class="muted"><?= t('formations.subtitle') ?></p>
    </div>
    <?php if (has_role(['admin', 'freelancer'])): ?>
        <a class="btn btn-primary" href="<?= url(['module' => 'formations', 'action' => 'create']) ?>"><?= t('formations.new') ?></a>
    <?php endif; ?>
</section>

<section class="stats-row">
    <article class="stat-card accent">
        <span class="stat-label"><?= t('formations.catalog') ?></span>
        <strong><?= (int) $stats['total'] ?></strong>
    </article>
    <article class="stat-card">
        <span class="stat-label"><?= t('formations.published') ?></span>
        <strong><?= (int) $stats['published'] ?></strong>
    </article>
    <article class="stat-card">
        <span class="stat-label"><?= t('formations.enrollments') ?></span>
        <strong><?= (int) $stats['enrollments'] ?></strong>
    </article>
    <article class="stat-card">
        <span class="stat-label"><?= t('formations.average_price') ?></span>
        <strong><?= format_currency($stats['average_price']) ?></strong>
    </article>
</section>

<section class="section-card filters-card">
    <form method="GET" action="index.php" class="filters-grid">
        <input type="hidden" name="module" value="formations">
        <input type="hidden" name="action" value="index">
        <input class="form-control" type="text" name="search" placeholder="<?= h(t('formations.search_placeholder')) ?>" value="<?= h($filters['search']) ?>">
        <select class="form-control" name="category_id">
            <option value=""><?= t('formations.all_categories') ?></option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>" <?= ($filters['category_id'] == $category['id']) ? 'selected' : '' ?>><?= h($category['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <select class="form-control" name="level">
            <option value=""><?= t('formations.all_levels') ?></option>
            <?php foreach (['Beginner', 'Intermediate', 'Advanced'] as $level): ?>
                <option value="<?= $level ?>" <?= $filters['level'] === $level ? 'selected' : '' ?>><?= $level ?></option>
            <?php endforeach; ?>
        </select>
        <select class="form-control" name="status">
            <option value=""><?= t('formations.all_statuses') ?></option>
            <?php foreach (['draft', 'published', 'archived'] as $status): ?>
                <option value="<?= $status ?>" <?= $filters['status'] === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
            <?php endforeach; ?>
        </select>
        <input class="form-control" type="number" step="0.01" min="0" name="max_price" placeholder="<?= h(t('formations.max_price')) ?>" value="<?= h($filters['max_price']) ?>">
        <button class="btn btn-primary" type="submit"><?= t('formations.filter') ?></button>
    </form>
</section>

<?php if ($formations): ?>
    <section class="card-grid">
        <?php foreach ($formations as $formation): ?>
            <article class="course-card">
                <div class="course-media" style="background-image:url('<?= h($formation['image_url'] ?: 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=900&q=80') ?>');"></div>
                <div class="course-body">
                    <div class="chip-row">
                        <span class="badge badge-info"><?= h($formation['category_name'] ?? 'General') ?></span>
                        <span class="badge <?= status_badge_class($formation['status']) ?>"><?= h($formation['status']) ?></span>
                    </div>
                    <h2><?= h($formation['title']) ?></h2>
                    <p class="card-copy"><?= h(mb_strimwidth($formation['description'], 0, 140, '...')) ?></p>
                    <div class="detail-grid compact">
                        <div><strong><?= t('formations.level') ?></strong><span><?= h($formation['level']) ?></span></div>
                        <div><strong><?= t('formations.duration') ?></strong><span><?= (int) $formation['duration_hours'] ?> h</span></div>
                        <div><strong><?= t('formations.price') ?></strong><span><?= format_currency($formation['price']) ?></span></div>
                        <div><strong><?= t('formations.learners') ?></strong><span><?= (int) $formation['enrolled_count'] ?></span></div>
                    </div>
                    <p class="muted"><?= t('formations.created_by') ?> <?= h(trim(($formation['first_name'] ?? '') . ' ' . ($formation['last_name'] ?? ''))) ?></p>
                    <div class="card-actions">
                        <a class="btn btn-outline btn-small" href="<?= url(['module' => 'formations', 'action' => 'show', 'id' => $formation['id']]) ?>"><?= t('formations.details') ?></a>
                        <?php if (has_role(['admin']) || (is_logged_in() && (int) auth_user()['id'] === (int) $formation['creator_id'])): ?>
                            <a class="btn btn-outline btn-small" href="<?= url(['module' => 'formations', 'action' => 'edit', 'id' => $formation['id']]) ?>"><?= t('users.edit') ?></a>
                            <a class="btn btn-danger btn-small" href="<?= url(['module' => 'formations', 'action' => 'delete', 'id' => $formation['id']]) ?>" onclick="return confirm('Supprimer cette formation ?');"><?= t('users.delete') ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
<?php else: ?>
    <section class="section-card empty-card">
        <h2><?= t('formations.no_results') ?></h2>
        <p class="muted"><?= t('formations.try_other') ?></p>
    </section>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
