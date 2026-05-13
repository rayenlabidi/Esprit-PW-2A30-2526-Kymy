<?php
$pageTitle = $office === 'back' ? 'Gestion Evenements' : 'Evenements';
$activeModule = 'events';
$events = isset($events) ? $events : [];
$categories = isset($categories) ? $categories : [];
$statistiques = isset($statistiques) ? $statistiques : [];
$search = isset($search) ? $search : '';
$category = isset($category) ? $category : '';
$status = isset($status) ? $status : '';
$sort = isset($sort) ? $sort : 'date_desc';
include __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <div>
        <p class="eyebrow"><?= $office === 'back' ? 'Workify events' : 'Agenda Workify'; ?></p>
        <h2><?= $office === 'back' ? 'Centre des evenements' : 'Evenements pour freelancers'; ?></h2>
        <p class="muted"><?= $office === 'back' ? 'Creez et pilotez les evenements depuis le meme backend.' : 'Retrouvez les sessions utiles pour apprendre, rencontrer et collaborer.'; ?></p>
    </div>
    <?php if ($office === 'back') { ?>
        <div class="toolbar-actions">
            <a class="btn" href="../controller/EventC.php?office=back&action=categories">Categories</a>
            <a class="btn" href="../controller/EventC.php?office=back&action=calendar">Calendrier</a>
            <a class="btn btn-primary" href="../controller/EventC.php?office=back&action=add">
                <svg viewBox="0 0 24 24"><path d="M11 5h2v6h6v2h-6v6h-2v-6H5v-2h6V5z"/></svg>
                Ajouter
            </a>
        </div>
    <?php } else { ?>
        <a class="btn" href="../controller/EventC.php?office=front&action=calendar">Calendrier</a>
    <?php } ?>
</div>

<div class="stats-grid">
    <div class="card">Total <strong><?= (int) ($statistiques['total'] ?? 0); ?></strong></div>
    <div class="card">A venir <strong><?= (int) ($statistiques['upcoming'] ?? 0); ?></strong></div>
    <div class="card">En cours <strong><?= (int) ($statistiques['ongoing'] ?? 0); ?></strong></div>
    <div class="card">En ligne <strong><?= (int) ($statistiques['online'] ?? 0); ?></strong></div>
</div>

<form class="filters" action="../controller/EventC.php" method="get">
    <input type="hidden" name="office" value="<?= htmlspecialchars($office, ENT_QUOTES); ?>">
    <input type="hidden" name="action" value="list">
    <input name="search" placeholder="Rechercher un evenement" value="<?= htmlspecialchars($search, ENT_QUOTES); ?>">
    <select name="category">
        <option value="">Toutes les categories</option>
        <?php foreach ($categories as $cat) { ?>
            <option value="<?= (int) $cat['id']; ?>" <?= (string) $category === (string) $cat['id'] ? 'selected' : ''; ?>><?= htmlspecialchars($cat['name'], ENT_QUOTES); ?></option>
        <?php } ?>
    </select>
    <select name="status">
        <option value="">Tous les statuts</option>
        <?php foreach (['upcoming' => 'A venir', 'ongoing' => 'En cours', 'completed' => 'Termine', 'cancelled' => 'Annule'] as $key => $label) { ?>
            <option value="<?= $key; ?>" <?= $status === $key ? 'selected' : ''; ?>><?= $label; ?></option>
        <?php } ?>
    </select>
    <select name="sort">
        <option value="date_desc" <?= $sort === 'date_desc' ? 'selected' : ''; ?>>Dates recentes</option>
        <option value="date_asc" <?= $sort === 'date_asc' ? 'selected' : ''; ?>>Dates proches</option>
        <option value="title" <?= $sort === 'title' ? 'selected' : ''; ?>>Titre</option>
        <option value="capacity" <?= $sort === 'capacity' ? 'selected' : ''; ?>>Capacite</option>
    </select>
    <button class="btn btn-green" type="submit">Filtrer</button>
    <a class="btn" href="../controller/EventC.php?office=<?= htmlspecialchars($office, ENT_QUOTES); ?>&action=list">Initialiser</a>
</form>

<?php if ($office === 'back') { ?>
    <div class="table-box">
        <table>
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Categorie</th>
                    <th>Date</th>
                    <th>Lieu</th>
                    <th>Capacite</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($events)) { ?>
                    <tr><td colspan="7">Aucun evenement trouve.</td></tr>
                <?php } ?>
                <?php foreach ($events as $event) { ?>
                    <tr>
                        <td data-label="Titre"><strong><?= htmlspecialchars($event['title'], ENT_QUOTES); ?></strong><br><span class="muted"><?= htmlspecialchars($event['organizer_name'], ENT_QUOTES); ?></span></td>
                        <td data-label="Categorie"><?= htmlspecialchars($event['category_name'], ENT_QUOTES); ?></td>
                        <td data-label="Date"><?= htmlspecialchars($event['event_date'], ENT_QUOTES); ?></td>
                        <td data-label="Lieu"><?= $event['is_online'] ? 'En ligne' : htmlspecialchars($event['location'], ENT_QUOTES); ?></td>
                        <td data-label="Capacite"><?= (int) $event['max_participants']; ?></td>
                        <td data-label="Statut"><span class="badge"><?= htmlspecialchars($event['status'], ENT_QUOTES); ?></span></td>
                        <td data-label="Actions" class="actions">
                            <a class="btn" href="../controller/EventC.php?office=back&action=detail&id=<?= (int) $event['id']; ?>">Details</a>
                            <a class="btn" href="../controller/EventC.php?office=back&action=edit&id=<?= (int) $event['id']; ?>">Modifier</a>
                            <a class="btn btn-danger" href="../controller/EventC.php?office=back&action=delete&id=<?= (int) $event['id']; ?>" onclick="return confirm('Supprimer cet evenement ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php } else { ?>
    <div class="formation-grid event-grid">
        <?php if (empty($events)) { ?>
            <article class="formation-card">
                <h3>Aucun evenement disponible</h3>
                <p class="muted">Revenez bientot pour les prochaines sessions Workify.</p>
            </article>
        <?php } ?>
        <?php foreach ($events as $event) { ?>
            <?php $eventImage = !empty($event['image_url']) ? $event['image_url'] : 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=1200&q=80'; ?>
            <article class="formation-card event-card">
                <div class="formation-card-media">
                    <img src="<?= strpos($eventImage, 'uploads/') === 0 ? '../' . htmlspecialchars($eventImage, ENT_QUOTES) : htmlspecialchars($eventImage, ENT_QUOTES); ?>" alt="<?= htmlspecialchars($event['title'], ENT_QUOTES); ?>">
                    <span class="course-icon"><svg viewBox="0 0 24 24"><path d="M7 2h2v3h6V2h2v3h3v17H4V5h3V2zm11 8H6v10h12V10z"/></svg></span>
                </div>
                <h3><?= htmlspecialchars($event['title'], ENT_QUOTES); ?></h3>
                <p class="muted"><?= htmlspecialchars(substr($event['description'], 0, 120), ENT_QUOTES); ?>...</p>
                <div class="card-meta">
                    <span class="badge"><?= htmlspecialchars($event['category_name'], ENT_QUOTES); ?></span>
                    <span class="badge badge-green"><?= htmlspecialchars($event['status'], ENT_QUOTES); ?></span>
                    <span class="badge badge-amber"><?= $event['is_online'] ? 'En ligne' : 'Presentiel'; ?></span>
                </div>
                <p><strong><?= htmlspecialchars(date('d/m/Y H:i', strtotime($event['event_date'])), ENT_QUOTES); ?></strong></p>
                <p class="muted"><?= htmlspecialchars($event['location'], ENT_QUOTES); ?> - <?= (int) $event['max_participants']; ?> places</p>
                <a class="btn btn-primary" href="../controller/EventC.php?office=front&action=detail&id=<?= (int) $event['id']; ?>">Voir details</a>
            </article>
        <?php } ?>
    </div>
<?php } ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
