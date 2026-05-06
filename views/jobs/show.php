<?php $pageTitle = 'Details job'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="detail-layout">
    <article class="detail-card">
        <div class="detail-content">
            <div class="chip-row">
                <span class="badge badge-info"><?= h($job['category_name'] ?? 'General') ?></span>
                <span class="badge <?= status_badge_class($job['status']) ?>"><?= h($job['status']) ?></span>
                <span class="badge <?= $job['is_remote'] ? 'badge-success' : 'badge-neutral' ?>"><?= $job['is_remote'] ? 'Remote' : 'Sur site' ?></span>
            </div>
            <h1><?= h($job['title']) ?></h1>
            <p><?= nl2br(h($job['description'])) ?></p>
            <div class="detail-grid">
                <div><strong>Budget</strong><span><?= format_currency($job['budget']) ?></span></div>
                <div><strong>Type</strong><span><?= h($job['job_type']) ?></span></div>
                <div><strong>Lieu</strong><span><?= h($job['location']) ?></span></div>
                <div><strong>Publie par</strong><span><?= h(trim($job['first_name'] . ' ' . $job['last_name'])) ?></span></div>
                <div><strong>Candidatures</strong><span><?= (int) $job['application_count'] ?></span></div>
                <div><strong>Date</strong><span><?= format_date($job['created_at']) ?></span></div>
            </div>

            <?php if (has_role(['freelancer', 'admin']) && !$hasApplied): ?>
                <div class="apply-box">
                    <h2>Postuler a ce job</h2>
                    <form method="POST" action="<?= url(['module' => 'jobs', 'action' => 'apply', 'id' => $job['id']]) ?>" class="stack-form js-validate" enctype="multipart/form-data" novalidate>
                        <div class="form-group">
                            <label for="cover_letter">Message de candidature</label>
                            <textarea id="cover_letter" name="cover_letter" class="form-control" data-label="Message de candidature" data-required="1" data-minlength="20"></textarea>
                            <small class="field-error"></small>
                        </div>
                        <div class="form-group">
                            <label for="cv_file">Mon CV (Optionnel, PDF)</label>
                            <input type="file" id="cv_file" name="cv_file" class="form-control" accept=".pdf">
                        </div>
                        <div class="form-group">
                            <label for="photo_file">Ma Photo (Optionnel, JPG/PNG)</label>
                            <input type="file" id="photo_file" name="photo_file" class="form-control" accept="image/*">
                        </div>
                        <button class="btn btn-primary" type="submit">Envoyer ma candidature</button>
                    </form>
                </div>
            <?php elseif ($hasApplied): ?>
                <p class="badge badge-success">Vous avez deja postule a ce job.</p>
            <?php endif; ?>

            <div class="card-actions">
                <a class="btn btn-outline" href="<?= url(['module' => 'jobs', 'action' => 'index']) ?>">Retour aux jobs</a>
            </div>
        </div>
    </article>

    <?php if (!empty($applications)): ?>
    <article class="section-card" style="margin-top: 2rem;">
        <div class="section-head">
            <h2>Candidatures reçues (<?= count($applications) ?>)</h2>
        </div>
        <div class="listing-stack">
            <?php foreach ($applications as $app): ?>
                <div class="listing-card" style="align-items: flex-start;">
                    <div style="flex: 1;">
                        <div style="display: flex; gap: 1rem; align-items: center; margin-bottom: 1rem;">
                            <?php if ($app['photo_url']): ?>
                                <img src="<?= h($app['photo_url']) ?>" alt="Photo" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                            <?php elseif ($app['avatar_url']): ?>
                                <img src="<?= h($app['avatar_url']) ?>" alt="Avatar" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                            <?php else: ?>
                                <div class="avatar-circle" style="width: 50px; height: 50px; font-size: 1.2rem;"><?= strtoupper(substr($app['first_name'], 0, 1) . substr($app['last_name'], 0, 1)) ?></div>
                            <?php endif; ?>
                            <div>
                                <h3 style="margin: 0;"><?= h($app['first_name'] . ' ' . $app['last_name']) ?></h3>
                                <a href="mailto:<?= h($app['email']) ?>" class="muted"><?= h($app['email']) ?></a>
                            </div>
                        </div>
                        <p class="card-copy" style="background: var(--color-background); padding: 1rem; border-radius: 8px;">
                            <strong>Message :</strong><br>
                            <?= nl2br(h($app['cover_letter'])) ?>
                        </p>
                        <p class="muted"><small>Reçu le <?= format_date($app['applied_at']) ?></small></p>
                    </div>
                    <div class="listing-side">
                        <div style="margin-bottom: 0.5rem;">
                            <span class="badge <?= status_badge_class($app['status']) ?>"><?= h($app['status']) ?></span>
                        </div>
                        <?php if ($app['status'] === 'pending' && (has_role(['admin']) || (int) $job['publisher_id'] === (int) auth_user()['id'])): ?>
                            <div style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem;">
                                <a href="<?= url(['module' => 'jobs', 'action' => 'updateApplicationStatus', 'id' => $app['id'], 'status' => 'accepted']) ?>" class="btn btn-primary btn-small" style="background: var(--color-success); border-color: var(--color-success);">✅ Accepter</a>
                                <a href="<?= url(['module' => 'jobs', 'action' => 'updateApplicationStatus', 'id' => $app['id'], 'status' => 'rejected']) ?>" class="btn btn-outline btn-small" style="color: var(--color-danger); border-color: var(--color-danger);">❌ Refuser</a>
                            </div>
                        <?php endif; ?>
                        <?php if ($app['cv_url']): ?>
                            <a href="<?= h($app['cv_url']) ?>" target="_blank" class="btn btn-outline btn-small">📄 Télécharger CV</a>
                        <?php else: ?>
                            <span class="muted"><small>Aucun CV fourni</small></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </article>
    <?php endif; ?>

    <?php if (!empty($recommendations)): ?>
    <article class="section-card" style="margin-top: 2rem; border-left: 4px solid var(--color-accent); background: linear-gradient(to right, rgba(26, 115, 232, 0.05), transparent);">
        <div class="section-head">
            <h2 style="color: var(--color-accent);">💡 Profils recommandés par notre algorithme (<?= count($recommendations) ?>)</h2>
            <p class="muted">Ces freelances sont inscrits à des formations correspondant à la catégorie de votre offre et n'ont pas encore postulé.</p>
        </div>
        <div class="listing-stack">
            <?php foreach ($recommendations as $rec): ?>
                <div class="listing-card" style="align-items: center;">
                    <div style="display: flex; gap: 1rem; align-items: center; flex: 1;">
                        <?php if ($rec['avatar_url']): ?>
                            <img src="<?= h($rec['avatar_url']) ?>" alt="Avatar" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                        <?php else: ?>
                            <div class="avatar-circle" style="width: 50px; height: 50px; font-size: 1.2rem;"><?= strtoupper(substr($rec['first_name'], 0, 1) . substr($rec['last_name'], 0, 1)) ?></div>
                        <?php endif; ?>
                        <div>
                            <h3 style="margin: 0;"><?= h($rec['first_name'] . ' ' . $rec['last_name']) ?></h3>
                            <p class="muted" style="margin: 0;"><small><?= h($rec['headline']) ?></small></p>
                        </div>
                    </div>
                    <div style="flex: 1; text-align: right;">
                        <?php
                            $score = (int) $rec['match_score'];
                            $color = $score >= 70 ? 'var(--color-success)' : ($score >= 40 ? '#f59e0b' : 'var(--color-danger)');
                        ?>
                        <div style="display: inline-flex; align-items: center; justify-content: center; width: 60px; height: 60px; border-radius: 50%; border: 4px solid <?= $color ?>; margin-bottom: 5px;">
                            <strong style="color: <?= $color ?>; font-size: 1.2rem;"><?= $score ?>%</strong>
                        </div><br>
                        <small class="muted">Score de pertinence</small>
                    </div>
                    <div class="listing-side">
                        <a href="<?= url(['module' => 'users', 'action' => 'show', 'id' => $rec['id']]) ?>" class="btn btn-outline btn-small">Voir le profil complet</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </article>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
