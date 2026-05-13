<?php
$pageTitle = $office === 'back' ? 'Gestion Publications' : 'Publications';
$activeModule = 'publications';
$publications = isset($publications) ? $publications : [];
$commentaires = isset($commentaires) ? $commentaires : [];
$statistiques = isset($statistiques) ? $statistiques : [];
$search = isset($search) ? $search : '';
$sort = isset($sort) ? $sort : 'recent';
$errors = isset($errors) ? $errors : [];
$likedPublications = isset($likedPublications) ? $likedPublications : [];
$likedComments = isset($likedComments) ? $likedComments : [];
$currentDisplayName = AuthC::currentUserName() !== '' ? AuthC::currentUserName() : 'Workify';
$nameParts = preg_split('/\s+/', trim($currentDisplayName));
$currentInitials = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
if ($currentInitials === '') {
    $currentInitials = 'WK';
}

function workifyAvatar($avatar, $initials, $class = '')
{
    $safeClass = trim('avatar-pill ' . $class);
    if (!empty($avatar) && strpos($avatar, 'uploads/') === 0) {
        return '<span class="' . htmlspecialchars($safeClass, ENT_QUOTES) . ' avatar-image"><img src="../' . htmlspecialchars($avatar, ENT_QUOTES) . '" alt=""></span>';
    }
    return '<span class="' . htmlspecialchars($safeClass, ENT_QUOTES) . '">' . htmlspecialchars($initials, ENT_QUOTES) . '</span>';
}
include __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <div>
        <p class="eyebrow"><?= $office === 'back' ? 'Gestion communaute' : 'Communaute Workify'; ?></p>
        <h2><?= $office === 'back' ? 'Centre des publications' : 'Publications et annonces'; ?></h2>
        <p class="muted"><?= $office === 'back' ? 'Suivez les posts, reactions et commentaires.' : 'Partagez des opportunites, posez une question ou suivez les annonces de la communaute.'; ?></p>
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

<?php if ($office === 'back') { ?>
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
<?php } ?>

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
    <div class="social-feed-shell">
        <aside class="feed-profile-card">
            <div class="feed-profile-cover"></div>
            <span class="avatar-pill feed-profile-avatar"><?= htmlspecialchars($currentInitials, ENT_QUOTES); ?></span>
            <h3><?= htmlspecialchars($currentDisplayName, ENT_QUOTES); ?></h3>
            <p class="muted"><?= htmlspecialchars(AuthC::currentUserEmail(), ENT_QUOTES); ?></p>
            <div class="feed-profile-stats">
                <span><strong><?= isset($statistiques['total']) ? (int) $statistiques['total'] : 0; ?></strong>Posts</span>
                <span><strong><?= isset($statistiques['likes']) ? (int) $statistiques['likes'] : 0; ?></strong>Likes</span>
                <span><strong><?= isset($statistiques['comments']) ? (int) $statistiques['comments'] : 0; ?></strong>Comments</span>
            </div>
            <a class="btn btn-primary" href="#compose-publication">Publier maintenant</a>
        </aside>

        <section class="feed-main-column">
            <form id="compose-publication" class="publication-compose social-composer" action="../controller/PublicationC.php?office=<?= htmlspecialchars($office, ENT_QUOTES); ?>&action=add" method="post">
                <div class="feed-author">
                    <span class="avatar-pill small"><?= htmlspecialchars($currentInitials, ENT_QUOTES); ?></span>
                    <div>
                        <strong><?= htmlspecialchars($currentDisplayName, ENT_QUOTES); ?></strong>
                        <p class="muted">Partagez une annonce, une opportunite ou une question.</p>
                    </div>
                </div>
                <textarea id="content" name="content" placeholder="Quoi de neuf dans votre projet ?" required></textarea>
                <div class="composer-actions">
                    <input id="image_url" name="image_url" type="url" placeholder="Image URL optionnelle">
                    <button class="btn btn-primary" type="submit">
                        <svg viewBox="0 0 24 24"><path d="M3 12 21 3l-4 18-5-7-7-2z"/></svg>
                        Post
                    </button>
                </div>
            </form>

            <form class="filters feed-search" action="../controller/PublicationC.php" method="get">
                <input type="hidden" name="office" value="<?= htmlspecialchars($office, ENT_QUOTES); ?>">
                <input type="hidden" name="action" value="list">
                <input name="search" placeholder="Search publications..." value="<?= htmlspecialchars($search, ENT_QUOTES); ?>">
                <select name="sort">
                    <option value="recent" <?= $sort === 'recent' ? 'selected' : ''; ?>>Plus recents</option>
                    <option value="liked" <?= $sort === 'liked' ? 'selected' : ''; ?>>Plus aimes</option>
                    <option value="commented" <?= $sort === 'commented' ? 'selected' : ''; ?>>Plus commentes</option>
                </select>
                <button class="btn" type="submit">Search</button>
            </form>

            <div class="publication-feed">
                <?php if (empty($publications)) { ?>
                    <article class="feed-card">
                        <h3>Aucune publication pour le moment</h3>
                        <p class="muted">Lancez la premiere annonce Workify.</p>
                    </article>
                <?php } ?>

                <?php foreach ($publications as $publicationItem) { ?>
                    <?php
                    $publicationId = (int) $publicationItem['id'];
                    $likesCount = isset($publicationItem['likes_count']) ? (int) $publicationItem['likes_count'] : (int) $publicationItem['likes'];
                    $isLiked = !empty($likedPublications[$publicationId]);
                    ?>
                    <article id="publication-<?= $publicationId; ?>" class="feed-card social-post-card">
                        <div class="feed-author">
                            <?= workifyAvatar($publicationItem['user_avatar'], $publicationItem['user_init']); ?>
                            <div>
                                <strong><?= htmlspecialchars($publicationItem['user_name'], ENT_QUOTES); ?></strong>
                                <p class="muted"><?= htmlspecialchars($publicationItem['user_role'], ENT_QUOTES); ?> - <?= htmlspecialchars($publicationItem['created_at'], ENT_QUOTES); ?></p>
                            </div>
                            <?php if (AuthC::isAdmin() || (string) $publicationItem['user_id'] === (string) AuthC::currentUserId()) { ?>
                                <a class="btn btn-danger" href="../controller/PublicationC.php?office=front&action=delete&id=<?= $publicationId; ?>" onclick="return confirm('Supprimer cette publication ?');">Supprimer</a>
                            <?php } ?>
                        </div>
                        <p class="feed-content"><?= nl2br(htmlspecialchars($publicationItem['content'], ENT_QUOTES)); ?></p>
                        <?php if (!empty($publicationItem['image_url'])) { ?>
                            <img class="feed-image" src="<?= htmlspecialchars($publicationItem['image_url'], ENT_QUOTES); ?>" alt="Image publication">
                        <?php } ?>

                        <div class="feed-actions-bar">
                            <form class="feed-action-form" action="../controller/PublicationC.php?office=front&action=like&id=<?= $publicationId; ?>" method="post">
                                <button class="feed-action <?= $isLiked ? 'is-liked' : ''; ?>" type="submit">
                                    <svg viewBox="0 0 24 24"><path d="M12 21s-7-4.4-9.5-8A5.7 5.7 0 0 1 12 6.2 5.7 5.7 0 0 1 21.5 13C19 16.6 12 21 12 21z"/></svg>
                                    <?= $likesCount; ?> Reaction<?= $likesCount > 1 ? 's' : ''; ?>
                                </button>
                            </form>
                            <span class="feed-action passive">
                                <svg viewBox="0 0 24 24"><path d="M4 4h16v12H7l-3 4V4z"/></svg>
                                <?= (int) $publicationItem['comments_count']; ?> Comment<?= (int) $publicationItem['comments_count'] > 1 ? 's' : ''; ?>
                            </span>
                        </div>

                        <div class="comment-list">
                            <?php
                            $topComments = [];
                            $replies = [];
                            foreach (($commentaires[$publicationId] ?? []) as $commentItem) {
                                if (!empty($commentItem['parent_id'])) {
                                    $replies[(int) $commentItem['parent_id']][] = $commentItem;
                                } else {
                                    $topComments[] = $commentItem;
                                }
                            }
                            ?>
                            <?php foreach ($topComments as $commentItem) { ?>
                                <?php $commentId = (int) $commentItem['id']; ?>
                                <div class="comment-thread">
                                    <div class="comment-item">
                                        <?= workifyAvatar($commentItem['user_avatar'], $commentItem['user_init'], 'small'); ?>
                                        <div class="comment-body">
                                            <p><strong><?= htmlspecialchars($commentItem['user_name'], ENT_QUOTES); ?></strong><br><?= htmlspecialchars($commentItem['comment'], ENT_QUOTES); ?></p>
                                            <div class="comment-actions">
                                                <form action="../controller/PublicationC.php?office=front&action=comment_like&id=<?= $commentId; ?>&publication_id=<?= $publicationId; ?>" method="post">
                                                    <button class="comment-action <?= !empty($likedComments[$commentId]) ? 'is-liked' : ''; ?>" type="submit"><?= (int) $commentItem['likes_count']; ?> reaction</button>
                                                </form>
                                                <button class="comment-action" type="button" data-reply-toggle="#reply-<?= $commentId; ?>">Repondre</button>
                                            </div>
                                            <form id="reply-<?= $commentId; ?>" class="inline-comment reply-form" data-validate="comment" action="../controller/PublicationC.php?action=comment" method="post">
                                                <input type="hidden" name="publication_id" value="<?= $publicationId; ?>">
                                                <input type="hidden" name="parent_id" value="<?= $commentId; ?>">
                                                <input name="comment" placeholder="Repondre a ce commentaire..." required>
                                                <button class="btn" type="submit">Repondre</button>
                                            </form>
                                        </div>
                                    </div>
                                    <?php foreach (($replies[$commentId] ?? []) as $replyItem) { ?>
                                        <?php $replyId = (int) $replyItem['id']; ?>
                                        <div class="comment-item reply-item">
                                            <?= workifyAvatar($replyItem['user_avatar'], $replyItem['user_init'], 'small'); ?>
                                            <div class="comment-body">
                                                <p><strong><?= htmlspecialchars($replyItem['user_name'], ENT_QUOTES); ?></strong><br><?= htmlspecialchars($replyItem['comment'], ENT_QUOTES); ?></p>
                                                <form action="../controller/PublicationC.php?office=front&action=comment_like&id=<?= $replyId; ?>&publication_id=<?= $publicationId; ?>" method="post">
                                                    <button class="comment-action <?= !empty($likedComments[$replyId]) ? 'is-liked' : ''; ?>" type="submit"><?= (int) $replyItem['likes_count']; ?> reaction</button>
                                                </form>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>

                        <form class="inline-comment" data-validate="comment" action="../controller/PublicationC.php?action=comment" method="post">
                            <input type="hidden" name="publication_id" value="<?= $publicationId; ?>">
                            <input name="comment" placeholder="Write a comment..." required>
                            <button class="btn" type="submit">Commenter</button>
                        </form>
                    </article>
                <?php } ?>
            </div>
        </section>

        <aside class="feed-side-panel">
            <h3>Trending skills</h3>
            <div class="trend-list">
                <span>PHP MVC</span>
                <span>UI Design</span>
                <span>Remote jobs</span>
                <span>Formation</span>
            </div>
            <div class="side-suggestion">
                <strong>Assistant Publication</strong>
                <p class="muted">Utilisez le bouton assistant pour reformuler un post, trouver un angle ou preparer une annonce.</p>
            </div>
            <a class="btn" href="../controller/MessageC.php?office=front&action=list">Ouvrir messages</a>
        </aside>
    </div>
<?php } ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
