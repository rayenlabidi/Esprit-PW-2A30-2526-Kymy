<?php
$pageTitle = $office === 'back' ? 'Gestion Publications' : 'Publications';
$activeModule = 'publications';
$publications = isset($publications) ? $publications : [];
$commentaires = isset($commentaires) ? $commentaires : [];
$statistiques = isset($statistiques) ? $statistiques : [];
$search = isset($search) ? $search : '';
$errors = isset($errors) ? $errors : [];
include __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <div>
        <p class="eyebrow"><?= $office === 'back' ? 'Gestion communaute' : 'Communaute Workify'; ?></p>
        <h2><?= $office === 'back' ? 'Centre des publications' : 'Publications et annonces'; ?></h2>
        <p class="muted">Un module integre directement dans le MVC principal de Workify.</p>
    </div>
</div>

<div class="stats-grid">
    <div class="card">Publications <strong><?= isset($statistiques['total']) ? (int) $statistiques['total'] : 0; ?></strong></div>
    <div class="card">Commentaires <strong><?= isset($statistiques['comments']) ? (int) $statistiques['comments'] : 0; ?></strong></div>
    <div class="card">Reactions <strong><?= isset($statistiques['likes']) ? (int) $statistiques['likes'] : 0; ?></strong></div>
    <div class="card">Compte actif <strong><?= htmlspecialchars(AuthC::currentUserName(), ENT_QUOTES); ?></strong></div>
</div>

<?php if (!empty($errors)) { ?>
    <div class="error-box">
        <ul>
            <?php foreach ($errors as $errorItem) { ?>
                <li><?= htmlspecialchars($errorItem, ENT_QUOTES); ?></li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>

<form class="form-box publication-compose" action="../controller/PublicationC.php?office=<?= htmlspecialchars($office, ENT_QUOTES); ?>&action=add" method="post">
    <label for="content">Nouvelle publication</label>
    <textarea id="content" name="content" placeholder="Partagez une annonce, une opportunite ou une actualite utile..." required></textarea>
    <label for="image_url">Image URL optionnelle</label>
    <input id="image_url" name="image_url" type="url" placeholder="https://...">
    <button class="btn btn-primary" type="submit">
        <svg viewBox="0 0 24 24"><path d="M3 12 21 3l-4 18-5-7-7-2z"/></svg>
        Publier
    </button>
</form>

<form class="filters" action="../controller/PublicationC.php" method="get">
    <input type="hidden" name="office" value="<?= htmlspecialchars($office, ENT_QUOTES); ?>">
    <input type="hidden" name="action" value="list">
    <input name="search" placeholder="Rechercher dans les publications" value="<?= htmlspecialchars($search, ENT_QUOTES); ?>">
    <button class="btn btn-green" type="submit">Rechercher</button>
    <a class="btn" href="../controller/PublicationC.php?office=<?= htmlspecialchars($office, ENT_QUOTES); ?>&action=list">Initialiser</a>
</form>

<?php if ($office === 'back') { ?>
    <div class="table-box">
        <table>
            <thead>
                <tr>
                    <th>Auteur</th>
                    <th>Publication</th>
                    <th>Commentaires</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($publications)) { ?>
                    <tr><td colspan="5">Aucune publication trouvee.</td></tr>
                <?php } ?>
                <?php foreach ($publications as $publicationItem) { ?>
                    <tr>
                        <td data-label="Auteur"><strong><?= htmlspecialchars($publicationItem['user_name'], ENT_QUOTES); ?></strong><br><span class="badge"><?= htmlspecialchars($publicationItem['user_role'], ENT_QUOTES); ?></span></td>
                        <td data-label="Publication"><?= nl2br(htmlspecialchars(substr($publicationItem['content'], 0, 180), ENT_QUOTES)); ?></td>
                        <td data-label="Commentaires"><?= (int) $publicationItem['comments_count']; ?></td>
                        <td data-label="Date"><?= htmlspecialchars($publicationItem['created_at'], ENT_QUOTES); ?></td>
                        <td data-label="Actions" class="actions">
                            <a class="btn btn-danger" href="../controller/PublicationC.php?office=back&action=delete&id=<?= (int) $publicationItem['id']; ?>" onclick="return confirm('Supprimer cette publication ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php } else { ?>
    <div class="publication-feed">
        <?php if (empty($publications)) { ?>
            <article class="feed-card">
                <h3>Aucune publication pour le moment</h3>
                <p class="muted">Lancez la premiere annonce Workify.</p>
            </article>
        <?php } ?>

        <?php foreach ($publications as $publicationItem) { ?>
            <article class="feed-card">
                <div class="feed-author">
                    <span class="avatar-pill"><?= htmlspecialchars($publicationItem['user_init'], ENT_QUOTES); ?></span>
                    <div>
                        <strong><?= htmlspecialchars($publicationItem['user_name'], ENT_QUOTES); ?></strong>
                        <p class="muted"><?= htmlspecialchars($publicationItem['user_role'], ENT_QUOTES); ?> - <?= htmlspecialchars($publicationItem['created_at'], ENT_QUOTES); ?></p>
                    </div>
                    <?php if (AuthC::isAdmin() || (string) $publicationItem['user_id'] === (string) AuthC::currentUserId()) { ?>
                        <a class="btn btn-danger" href="../controller/PublicationC.php?office=front&action=delete&id=<?= (int) $publicationItem['id']; ?>" onclick="return confirm('Supprimer cette publication ?');">Supprimer</a>
                    <?php } ?>
                </div>
                <p class="feed-content"><?= nl2br(htmlspecialchars($publicationItem['content'], ENT_QUOTES)); ?></p>
                <?php if (!empty($publicationItem['image_url'])) { ?>
                    <img class="feed-image" src="<?= htmlspecialchars($publicationItem['image_url'], ENT_QUOTES); ?>" alt="Image publication">
                <?php } ?>
                <div class="card-meta">
                    <span class="badge"><?= (int) $publicationItem['likes']; ?> reaction(s)</span>
                    <span class="badge badge-green"><?= (int) $publicationItem['comments_count']; ?> commentaire(s)</span>
                </div>

                <div class="comment-list">
                    <?php foreach (($commentaires[(int) $publicationItem['id']] ?? []) as $commentItem) { ?>
                        <div class="comment-item">
                            <span class="avatar-pill small"><?= htmlspecialchars($commentItem['user_init'], ENT_QUOTES); ?></span>
                            <p><strong><?= htmlspecialchars($commentItem['user_name'], ENT_QUOTES); ?></strong><br><?= htmlspecialchars($commentItem['comment'], ENT_QUOTES); ?></p>
                        </div>
                    <?php } ?>
                </div>

                <form class="inline-comment" action="../controller/PublicationC.php?action=comment" method="post">
                    <input type="hidden" name="publication_id" value="<?= (int) $publicationItem['id']; ?>">
                    <input name="comment" placeholder="Ajouter un commentaire..." required>
                    <button class="btn" type="submit">Commenter</button>
                </form>
            </article>
        <?php } ?>
    </div>
<?php } ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
