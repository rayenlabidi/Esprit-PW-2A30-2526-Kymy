<?php require 'views/layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1>Modifier la Formation</h1>
        <p>Mettez à jour les informations de cette formation.</p>
    </div>
    <a href="index.php" class="btn btn-secondary">
        Retour à la liste
    </a>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto; padding: 2rem;">
    <form action="index.php?action=edit&id=<?= $this->formation->id ?>" method="POST">
        <div class="form-group">
            <label class="form-label">Titre de la formation</label>
            <input type="text" name="titre" class="form-control" value="<?= htmlspecialchars($this->formation->titre) ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Description (Optionnelle)</label>
            <textarea name="description" class="form-control"><?= htmlspecialchars($this->formation->description) ?></textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label class="form-label">Prix (€)</label>
                <input type="number" step="0.01" name="prix" class="form-control" value="<?= htmlspecialchars($this->formation->prix) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Durée (Heures)</label>
                <input type="number" name="duree" class="form-control" value="<?= htmlspecialchars($this->formation->duree) ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Catégorie</label>
            <select name="id_categorie" class="form-control" required>
                <option value="">-- Sélectionnez une catégorie --</option>
                <?php foreach($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $this->formation->id_categorie == $cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem; text-align: right;">
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </div>
    </form>
</div>

<?php require 'views/layouts/footer.php'; ?>
