<?php require 'views/layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1>Ajouter un Utilisateur</h1>
        <p>Enregistrez un nouveau membre sur la plateforme.</p>
    </div>
    <a href="index.php?controller=utilisateur" class="btn btn-secondary">
        Retour à la liste
    </a>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto; padding: 2rem;">
    <form action="index.php?controller=utilisateur&action=create" method="POST">
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label class="form-label">Prénom</label>
                <input type="text" name="prenom" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Nom</label>
                <input type="text" name="nom" class="form-control" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Adresse Email</label>
            <input type="email" name="email" class="form-control" placeholder="exemple@workify.com" required>
        </div>

        <div class="form-group">
            <label class="form-label">Mot de passe</label>
            <input type="password" name="mot_de_passe" class="form-control" required>
        </div>

        <div class="form-group">
            <label class="form-label">Rôle assigné</label>
            <select name="id_role" class="form-control" required>
                <option value="">-- Sélectionnez un rôle --</option>
                <?php foreach($roles as $r): ?>
                    <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['nom_role']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem; text-align: right;">
            <button type="submit" class="btn btn-primary">Créer l'utilisateur</button>
        </div>
    </form>
</div>

<?php require 'views/layouts/footer.php'; ?>
