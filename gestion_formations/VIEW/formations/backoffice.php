<?php $pageTitle = t('formations.backoffice_title'); require __DIR__ . '/../layouts/header.php'; ?>

<section class="page-head">
    <div>
        <p class="eyebrow"><?= t('formations.module') ?></p>
        <h1><?= t('formations.backoffice_title') ?></h1>
        <p class="muted"><?= t('formations.backoffice_copy') ?></p>
    </div>
    <div class="card-actions">
        <a class="btn btn-outline" href="<?= url(['module' => 'formations', 'action' => 'index']) ?>"><?= t('formations.front') ?></a>
        <a class="btn btn-primary" href="<?= url(['module' => 'formations', 'action' => 'create']) ?>"><?= t('formations.new') ?></a>
    </div>
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
    <form method="GET" action="index.php" class="filters-grid js-validate" novalidate>
        <input type="hidden" name="module" value="formations">
        <input type="hidden" name="action" value="backoffice">
        <input class="form-control" type="text" name="search" placeholder="<?= h(t('formations.search_placeholder')) ?>" value="<?= h($filters['search']) ?>" data-label="Recherche">
        <select class="form-control" name="category_id" data-label="Categorie">
            <option value=""><?= t('formations.all_categories') ?></option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>" <?= ($filters['category_id'] == $category['id']) ? 'selected' : '' ?>><?= h($category['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <select class="form-control" name="level" data-label="Niveau">
            <option value=""><?= t('formations.all_levels') ?></option>
            <?php foreach (['Beginner', 'Intermediate', 'Advanced'] as $level): ?>
                <option value="<?= $level ?>" <?= $filters['level'] === $level ? 'selected' : '' ?>><?= $level ?></option>
            <?php endforeach; ?>
        </select>
        <select class="form-control" name="status" data-label="Statut">
            <option value=""><?= t('formations.all_statuses') ?></option>
            <?php foreach (['draft', 'published', 'archived'] as $status): ?>
                <option value="<?= $status ?>" <?= $filters['status'] === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
            <?php endforeach; ?>
        </select>
        <input class="form-control" type="number" step="0.01" min="0" name="max_price" placeholder="<?= h(t('formations.max_price')) ?>" value="<?= h($filters['max_price']) ?>" data-label="Prix max" data-positive="1">
        <button class="btn btn-primary" type="submit"><?= t('formations.filter') ?></button>
    </form>
</section>

<section class="two-column-grid">
    <article class="section-card">
        <h2><?= t('formations.latest_enrollments') ?></h2>
        <?php if ($latestEnrollments): ?>
            <table class="table-clean">
                <thead>
                    <tr>
                        <th><?= t('auth.first_name') ?></th>
                        <th><?= t('formations.label_title') ?></th>
                        <th><?= t('formations.created_at') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($latestEnrollments as $enrollment): ?>
                        <tr>
                            <td><?= h(trim($enrollment['first_name'] . ' ' . $enrollment['last_name'])) ?></td>
                            <td><?= h($enrollment['title']) ?></td>
                            <td><?= format_date($enrollment['enrolled_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="muted"><?= t('formations.no_enrollments') ?></p>
        <?php endif; ?>
    </article>

    <article class="section-card formations-table-card">
        <h2>Tableau des formations</h2>
        <?php if ($formations): ?>
            <div class="formations-table-wrap">
            <table class="table-clean formations-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th><?= t('formations.label_title') ?></th>
                        <th><?= t('formations.label_category') ?></th>
                        <th><?= t('formations.level') ?></th>
                        <th><?= t('formations.price') ?></th>
                        <th><?= t('formations.learners') ?></th>
                        <th><?= t('formations.label_status') ?></th>
                        <th>CRUD</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($formations as $formation): ?>
                        <tr>
                            <td><?= (int) $formation['id'] ?></td>
                            <td><?= h($formation['title']) ?></td>
                            <td><?= h($formation['category_name'] ?? '-') ?></td>
                            <td><?= h($formation['level']) ?></td>
                            <td><?= format_currency($formation['price']) ?></td>
                            <td><?= (int) $formation['enrolled_count'] ?></td>
                            <td><span class="badge <?= status_badge_class($formation['status']) ?>"><?= h($formation['status']) ?></span></td>
                            <td>
                                <div class="card-actions">
                                    <a class="btn btn-outline btn-small" href="<?= url(['module' => 'formations', 'action' => 'show', 'id' => $formation['id']]) ?>"><?= t('formations.details') ?></a>
                                    <a class="btn btn-outline btn-small" href="<?= url(['module' => 'formations', 'action' => 'edit', 'id' => $formation['id']]) ?>"><?= t('users.edit') ?></a>
                                    <a class="btn btn-danger btn-small" href="<?= url(['module' => 'formations', 'action' => 'delete', 'id' => $formation['id']]) ?>" onclick="return confirm('Supprimer cette formation ?');"><?= t('users.delete') ?></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php else: ?>
            <p class="muted"><?= t('formations.no_results') ?></p>
        <?php endif; ?>
    </article>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
