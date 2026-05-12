<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="auth-panel">
    <div>
        <p class="eyebrow">BackOffice securise</p>
        <h2>Connexion administrateur</h2>
        <p class="muted">Le BackOffice Workify est reserve aux comptes admin. Les visiteurs peuvent consulter les modules publics sans connexion.</p>
    </div>

    <form class="form-box auth-form" action="../controller/AuthController.php?action=login" method="post">
        <?php if ($error !== '') { ?>
            <div class="error-box"><?= htmlspecialchars($error, ENT_QUOTES); ?></div>
        <?php } ?>

        <div>
            <label for="email">Email admin</label>
            <input id="email" name="email" type="email" placeholder="admin@workify.com" required>
        </div>

        <div>
            <label for="password">Mot de passe</label>
            <input id="password" name="password" type="password" placeholder="admin123" required>
        </div>

        <div class="actions" style="margin-top: 18px;">
            <button class="btn btn-primary" type="submit">Se connecter</button>
            <a class="btn" href="../controller/HomeC.php">Retour FrontOffice</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
