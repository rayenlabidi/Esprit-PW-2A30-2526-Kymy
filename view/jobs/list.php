<?php
$pageTitle = $office === 'back' ? 'Gestion Jobs' : 'Browse Jobs';
$activeModule = 'jobs';
$jobs = isset($jobs) ? $jobs : [];
$categories = isset($categories) ? $categories : [];
$statistiques = isset($statistiques) ? $statistiques : [];
$search = isset($search) ? $search : '';
$idCategorie = isset($idCategorie) ? $idCategorie : '';
$type = isset($type) ? $type : '';
$statut = isset($statut) ? $statut : '';
$remoteOnly = isset($remoteOnly) ? $remoteOnly : '';
$sort = isset($sort) ? $sort : 'date_desc';
include __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <div>
        <p class="eyebrow"><?= $office === 'back' ? 'Resource control' : 'Workify opportunities'; ?></p>
        <h2><?= $office === 'back' ? 'Job Control Center' : 'Find your next mission'; ?></h2>
    </div>
    <?php if ($office === 'back') { ?>
        <div class="actions">
            <a class="btn" href="../controller/JobC.php?office=back&action=applications">
                <svg viewBox="0 0 24 24"><path d="M16 11c1.7 0 3-1.3 3-3s-1.3-3-3-3-3 1.3-3 3 1.3 3 3 3zm-8 0c1.7 0 3-1.3 3-3S9.7 5 8 5 5 6.3 5 8s1.3 3 3 3zm0 2c-2.3 0-7 1.2-7 3.5V19h14v-2.5C15 14.2 10.3 13 8 13zm8 0c-.3 0-.7 0-1.1.1 1.2.9 2.1 2 2.1 3.4V19h6v-2.5c0-2.3-4.7-3.5-7-3.5z"/></svg>
                Candidatures
            </a>
            <a class="btn btn-primary" href="../controller/JobC.php?office=back&action=add">
                <svg viewBox="0 0 24 24"><path d="M11 5h2v6h6v2h-6v6h-2v-6H5v-2h6V5z"/></svg>
                Ajouter
            </a>
        </div>
    <?php } ?>
</div>

<div class="stats-grid">
    <div class="card">Total <strong><?= isset($statistiques['total']) ? (int) $statistiques['total'] : 0; ?></strong></div>
    <div class="card">Ouverts <strong><?= isset($statistiques['ouverts']) ? (int) $statistiques['ouverts'] : 0; ?></strong></div>
    <div class="card">Candidatures <strong><?= isset($statistiques['candidatures']) ? (int) $statistiques['candidatures'] : 0; ?></strong></div>
    <div class="card">Budget moyen <strong><?= isset($statistiques['budget_moyen']) ? number_format((float) $statistiques['budget_moyen'], 0, '.', ' ') : 0; ?> DT</strong></div>
</div>

<form class="filters" action="../controller/JobC.php" method="get">
    <input type="hidden" name="office" value="<?= htmlspecialchars($office, ENT_QUOTES); ?>">
    <input type="hidden" name="action" value="list">
    <input name="search" placeholder="Search by title or location" value="<?= htmlspecialchars($search, ENT_QUOTES); ?>">
    <select name="id_categorie">
        <option value="">Toutes les categories</option>
        <?php foreach ($categories as $categorie) { ?>
            <option value="<?= (int) $categorie['id']; ?>" <?= ((string) $idCategorie === (string) $categorie['id']) ? 'selected' : ''; ?>>
                <?= htmlspecialchars($categorie['name'], ENT_QUOTES); ?>
            </option>
        <?php } ?>
    </select>
    <select name="type">
        <option value="">Tous les types</option>
        <?php foreach (['Freelance', 'Full-time', 'Stage', 'Part-time'] as $typeOption) { ?>
            <option value="<?= htmlspecialchars($typeOption, ENT_QUOTES); ?>" <?= $type === $typeOption ? 'selected' : ''; ?>><?= htmlspecialchars($typeOption, ENT_QUOTES); ?></option>
        <?php } ?>
    </select>
    <select name="statut">
        <option value="">Tous les statuts</option>
        <option value="open" <?= $statut === 'open' ? 'selected' : ''; ?>>Open</option>
        <option value="draft" <?= $statut === 'draft' ? 'selected' : ''; ?>>Draft</option>
        <option value="closed" <?= $statut === 'closed' ? 'selected' : ''; ?>>Closed</option>
    </select>
    <select name="sort">
        <option value="date_desc" <?= $sort === 'date_desc' ? 'selected' : ''; ?>>Plus recents</option>
        <option value="date_asc" <?= $sort === 'date_asc' ? 'selected' : ''; ?>>Plus anciens</option>
        <option value="budget_desc" <?= $sort === 'budget_desc' ? 'selected' : ''; ?>>Budget decroissant</option>
        <option value="budget_asc" <?= $sort === 'budget_asc' ? 'selected' : ''; ?>>Budget croissant</option>
    </select>
    <label class="inline-check">
        <input type="checkbox" name="remote" value="1" <?= $remoteOnly === '1' ? 'checked' : ''; ?>>
        Remote
    </label>
    <button class="btn btn-green" type="submit">Rechercher</button>
    <a class="btn" href="../controller/JobC.php?office=<?= $office; ?>&action=list">Initialiser</a>
</form>

<?php if ($office === 'front') { ?>
    <div class="formation-grid">
        <?php if (empty($jobs)) { ?>
            <div class="formation-card">
                <h3>Aucun job trouve</h3>
                <p class="muted">Essayez une autre recherche ou une autre categorie.</p>
            </div>
        <?php } ?>

        <?php foreach ($jobs as $jobItem) { ?>
            <article class="formation-card">
                <div class="course-icon">
                    <svg viewBox="0 0 24 24"><path d="M10 4h4a2 2 0 0 1 2 2v2h4v12H4V8h4V6a2 2 0 0 1 2-2zm4 4V6h-4v2h4z"/></svg>
                </div>
                <h3><?= htmlspecialchars($jobItem['title'], ENT_QUOTES); ?></h3>
                <p class="muted"><?= htmlspecialchars(substr($jobItem['description'], 0, 120), ENT_QUOTES); ?>...</p>
                <div class="card-meta">
                    <span class="badge"><?= htmlspecialchars($jobItem['nom_categorie'], ENT_QUOTES); ?></span>
                    <span class="badge badge-green"><?= htmlspecialchars($jobItem['job_type'], ENT_QUOTES); ?></span>
                    <span class="badge badge-amber"><?= $jobItem['is_remote'] ? 'Remote' : 'Sur site'; ?></span>
                </div>
                <p><strong><?= number_format((float) $jobItem['budget'], 2, '.', ' '); ?> DT</strong> - <?= htmlspecialchars($jobItem['location'], ENT_QUOTES); ?></p>
                <p class="muted">Publie par: <?= htmlspecialchars($jobItem['nom_publisher'], ENT_QUOTES); ?></p>
                <p class="muted"><?= (int) $jobItem['total_candidatures']; ?> candidature(s)</p>
                <a class="btn btn-primary" href="../controller/JobC.php?office=front&action=detail&id=<?= (int) $jobItem['id']; ?>">Voir et postuler</a>
            </article>
        <?php } ?>
    </div>
<?php } else { ?>
    <div class="table-box">
        <table>
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Categorie</th>
                    <th>Publie par</th>
                    <th>Type</th>
                    <th>Budget</th>
                    <th>Statut</th>
                    <th>Candidatures</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($jobs)) { ?>
                    <tr><td colspan="8">Aucun job trouve.</td></tr>
                <?php } ?>
                <?php foreach ($jobs as $jobItem) { ?>
                    <tr>
                        <td data-label="Titre"><strong><?= htmlspecialchars($jobItem['title'], ENT_QUOTES); ?></strong><br><span class="muted"><?= htmlspecialchars($jobItem['location'], ENT_QUOTES); ?> - <?= $jobItem['is_remote'] ? 'Remote' : 'Sur site'; ?></span></td>
                        <td data-label="Categorie"><?= htmlspecialchars($jobItem['nom_categorie'], ENT_QUOTES); ?></td>
                        <td data-label="Publie par"><?= htmlspecialchars($jobItem['nom_publisher'], ENT_QUOTES); ?></td>
                        <td data-label="Type"><?= htmlspecialchars($jobItem['job_type'], ENT_QUOTES); ?></td>
                        <td data-label="Budget"><?= number_format((float) $jobItem['budget'], 2, '.', ' '); ?> DT</td>
                        <td data-label="Statut"><span class="badge"><?= htmlspecialchars($jobItem['status'], ENT_QUOTES); ?></span></td>
                        <td data-label="Candidatures"><?= (int) $jobItem['total_candidatures']; ?></td>
                        <td data-label="Actions" class="actions">
                            <a class="btn" href="../controller/JobC.php?office=back&action=detail&id=<?= (int) $jobItem['id']; ?>">Details</a>
                            <a class="btn" href="../controller/JobC.php?office=back&action=edit&id=<?= (int) $jobItem['id']; ?>">Modifier</a>
                            <a class="btn btn-danger" href="../controller/JobC.php?office=back&action=delete&id=<?= (int) $jobItem['id']; ?>" onclick="return confirm('Supprimer ce job ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
