<?php
$pageTitle = $office === 'back' ? 'Gestion Formations' : 'Browse Formations';
$activeModule = 'formations';
$search = isset($search) ? $search : '';
$idCategorie = isset($idCategorie) ? $idCategorie : '';
$statut = isset($statut) ? $statut : '';
$niveau = isset($niveau) ? $niveau : '';
$categories = isset($categories) ? $categories : [];
$liste = isset($liste) ? $liste : [];
$statistiques = isset($statistiques) ? $statistiques : [];
include __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <div>
        <p class="eyebrow"><?= $office === 'back' ? 'Resource control' : 'Workify learning'; ?></p>
        <h2><?= $office === 'back' ? 'Training Control Center' : 'Find your next skill'; ?></h2>
    </div>
    <?php if ($office === 'back') { ?>
        <div class="actions">
            <a class="btn" href="../controller/FormationC.php?office=back&action=inscriptions">
                <svg viewBox="0 0 24 24"><path d="M16 11c1.7 0 3-1.3 3-3s-1.3-3-3-3-3 1.3-3 3 1.3 3 3 3zm-8 0c1.7 0 3-1.3 3-3S9.7 5 8 5 5 6.3 5 8s1.3 3 3 3zm0 2c-2.3 0-7 1.2-7 3.5V19h14v-2.5C15 14.2 10.3 13 8 13zm8 0c-.3 0-.7 0-1.1.1 1.2.9 2.1 2 2.1 3.4V19h6v-2.5c0-2.3-4.7-3.5-7-3.5z"/></svg>
                Inscriptions
            </a>
            <a class="btn btn-primary" href="../controller/FormationC.php?office=back&action=add">
                <svg viewBox="0 0 24 24"><path d="M11 5h2v6h6v2h-6v6h-2v-6H5v-2h6V5z"/></svg>
                Ajouter
            </a>
        </div>
    <?php } ?>
</div>

<div class="stats-grid">
    <div class="card">Total <strong><?= isset($statistiques['total']) ? (int) $statistiques['total'] : 0; ?></strong></div>
    <div class="card">Planifiees <strong><?= isset($statistiques['planifiees']) ? (int) $statistiques['planifiees'] : 0; ?></strong></div>
    <div class="card">En cours <strong><?= isset($statistiques['en_cours']) ? (int) $statistiques['en_cours'] : 0; ?></strong></div>
    <div class="card">Inscriptions <strong><?= isset($statistiques['inscriptions']) ? (int) $statistiques['inscriptions'] : 0; ?></strong></div>
</div>

<form class="filters" action="../controller/FormationC.php" method="get">
    <input type="hidden" name="office" value="<?= htmlspecialchars($office, ENT_QUOTES); ?>">
    <input type="hidden" name="action" value="list">
    <input name="search" placeholder="Search by title or trainer" value="<?= htmlspecialchars($search, ENT_QUOTES); ?>">
    <select name="id_categorie">
        <option value="">Toutes les categories</option>
        <?php foreach ($categories as $categorie) { ?>
            <option value="<?= (int) $categorie['id_categorie']; ?>" <?= ((string) $idCategorie === (string) $categorie['id_categorie']) ? 'selected' : ''; ?>>
                <?= htmlspecialchars($categorie['nom_categorie'], ENT_QUOTES); ?>
            </option>
        <?php } ?>
    </select>
    <select name="niveau">
        <option value="">Tous les niveaux</option>
        <option value="Debutant" <?= $niveau === 'Debutant' ? 'selected' : ''; ?>>Debutant</option>
        <option value="Intermediaire" <?= $niveau === 'Intermediaire' ? 'selected' : ''; ?>>Intermediaire</option>
        <option value="Avance" <?= $niveau === 'Avance' ? 'selected' : ''; ?>>Avance</option>
    </select>
    <select name="statut">
        <option value="">Tous les statuts</option>
        <option value="planifiee" <?= $statut === 'planifiee' ? 'selected' : ''; ?>>Planifiee</option>
        <option value="en_cours" <?= $statut === 'en_cours' ? 'selected' : ''; ?>>En cours</option>
        <option value="terminee" <?= $statut === 'terminee' ? 'selected' : ''; ?>>Terminee</option>
        <option value="annulee" <?= $statut === 'annulee' ? 'selected' : ''; ?>>Annulee</option>
    </select>
    <button class="btn btn-green" type="submit">Rechercher</button>
    <a class="btn" href="../controller/FormationC.php?office=<?= $office; ?>&action=list">Initialiser</a>
</form>

<?php if ($office === 'front') { ?>
    <div class="formation-grid">
        <?php if (empty($liste)) { ?>
            <div class="formation-card">
                <h3>Aucune formation trouvee</h3>
                <p class="muted">Essayez une autre recherche ou une autre categorie.</p>
            </div>
        <?php } ?>

        <?php foreach ($liste as $formation) { ?>
            <article class="formation-card">
                <div class="course-icon">
                    <svg viewBox="0 0 24 24"><path d="M4 5h16v12H7l-3 3V5zm4 4v2h8V9H8zm0 4v2h6v-2H8z"/></svg>
                </div>
                <h3><?= htmlspecialchars($formation['titre'], ENT_QUOTES); ?></h3>
                <p class="muted"><?= htmlspecialchars(substr($formation['description'], 0, 120), ENT_QUOTES); ?>...</p>
                <div class="card-meta">
                    <span class="badge"><?= htmlspecialchars($formation['nom_categorie'], ENT_QUOTES); ?></span>
                    <span class="badge badge-green"><?= htmlspecialchars($formation['niveau'], ENT_QUOTES); ?></span>
                    <span class="badge badge-amber"><?= htmlspecialchars($formation['mode'], ENT_QUOTES); ?></span>
                </div>
                <p><strong><?= number_format((float) $formation['prix'], 2, '.', ' '); ?> DT</strong> - <?= (int) $formation['duree']; ?> h</p>
                <p class="muted">Formateur: <?= htmlspecialchars($formation['nom_formateur'], ENT_QUOTES); ?></p>
                <p class="muted"><?= (int) $formation['total_inscrits']; ?> / <?= (int) $formation['places']; ?> places reservees</p>
                <a class="btn btn-primary" href="../controller/FormationC.php?office=front&action=detail&id=<?= (int) $formation['id_formation']; ?>">Voir et s inscrire</a>
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
                    <th>Formateur</th>
                    <th>Dates</th>
                    <th>Places</th>
                    <th>Prix</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($liste)) { ?>
                    <tr><td colspan="8">Aucune formation trouvee.</td></tr>
                <?php } ?>
                <?php foreach ($liste as $formation) { ?>
                    <tr>
                        <td data-label="Titre"><strong><?= htmlspecialchars($formation['titre'], ENT_QUOTES); ?></strong><br><span class="muted"><?= htmlspecialchars($formation['niveau'], ENT_QUOTES); ?> - <?= htmlspecialchars($formation['mode'], ENT_QUOTES); ?></span></td>
                        <td data-label="Categorie"><?= htmlspecialchars($formation['nom_categorie'], ENT_QUOTES); ?></td>
                        <td data-label="Formateur"><?= htmlspecialchars($formation['nom_formateur'], ENT_QUOTES); ?></td>
                        <td data-label="Dates"><?= htmlspecialchars($formation['date_debut'], ENT_QUOTES); ?> au <?= htmlspecialchars($formation['date_fin'], ENT_QUOTES); ?></td>
                        <td data-label="Places"><?= (int) $formation['total_inscrits']; ?> / <?= (int) $formation['places']; ?></td>
                        <td data-label="Prix"><?= number_format((float) $formation['prix'], 2, '.', ' '); ?> DT</td>
                        <td data-label="Statut"><span class="badge"><?= htmlspecialchars($formation['statut'], ENT_QUOTES); ?></span></td>
                        <td data-label="Actions" class="actions">
                            <a class="btn" href="../controller/FormationC.php?office=back&action=detail&id=<?= (int) $formation['id_formation']; ?>">Details</a>
                            <a class="btn" href="../controller/FormationC.php?office=back&action=edit&id=<?= (int) $formation['id_formation']; ?>">Modifier</a>
                            <a class="btn btn-danger" href="../controller/FormationC.php?office=back&action=delete&id=<?= (int) $formation['id_formation']; ?>" onclick="return confirm('Supprimer cette formation ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
