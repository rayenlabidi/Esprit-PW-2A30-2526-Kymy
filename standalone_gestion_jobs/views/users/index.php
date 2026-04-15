<?php $pageTitle = 'Gestion des utilisateurs'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="page-head">
    <div>
        <p class="eyebrow">Admin only</p>
        <h1>Gestion des utilisateurs</h1>
        <p class="muted">CRUD complet des comptes avec roles relies aux formations et aux jobs.</p>
    </div>
    <a class="btn btn-primary" href="<?= url(['module' => 'users', 'action' => 'create']) ?>">Ajouter un utilisateur</a>
</section>

<section class="stats-row">
    <?php foreach ($roleStats as $role): ?>
        <article class="stat-card">
            <span class="stat-label"><?= h($role['name']) ?></span>
            <strong><?= (int) $role['total'] ?></strong>
        </article>
    <?php endforeach; ?>
</section>

<section class="section-card">
    <?php if ($users): ?>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Headline</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $item): ?>
                        <tr>
                            <td><?= h($item['first_name'] . ' ' . $item['last_name']) ?></td>
                            <td><?= h($item['email']) ?></td>
                            <td><span class="badge badge-info"><?= h($item['role_name']) ?></span></td>
                            <td><?= h($item['headline']) ?></td>
                            <td><span class="badge <?= status_badge_class($item['status']) ?>"><?= h($item['status']) ?></span></td>
                            <td class="table-actions">
                                <a class="btn btn-small btn-outline" href="<?= url(['module' => 'users', 'action' => 'show', 'id' => $item['id']]) ?>"><?= t('users.profile') ?></a>
                                <a class="btn btn-small btn-outline" href="<?= url(['module' => 'users', 'action' => 'edit', 'id' => $item['id']]) ?>"><?= t('users.edit') ?></a>
                                <a class="btn btn-small btn-danger" href="<?= url(['module' => 'users', 'action' => 'delete', 'id' => $item['id']]) ?>" onclick="return confirm('Supprimer cet utilisateur ?');"><?= t('users.delete') ?></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="empty-copy">Aucun utilisateur trouve.</p>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
