<?php require 'views/layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1>Modifier un Utilisateur</h1>
        <p>Mettez à jour les informations du membre.</p>
    </div>
    <a href="index.php?controller=utilisateur" class="btn btn-secondary">
        Retour à la liste
    </a>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto; padding: 2rem;">
    <form action="index.php?controller=utilisateur&action=edit&id=<?= $this->utilisateur->id ?>" method="POST">
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label class="form-label">Prénom</label>
                <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($this->utilisateur->prenom) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Nom</label>
                <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($this->utilisateur->nom) ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Adresse Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($this->utilisateur->email) ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Rôle assigné</label>
            <select name="id_role" class="form-control" required>
                <option value="">-- Sélectionnez un rôle --</option>
                <?php foreach($roles as $r): ?>
                    <option value="<?= $r['id'] ?>" <?= $this->utilisateur->id_role == $r['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($r['nom_role']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem; text-align: right;">
            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
        </div>
    </form>
</div>

<?php require 'views/layouts/footer.php'; ?>
