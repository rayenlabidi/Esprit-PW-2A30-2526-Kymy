<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="auth-panel">
    <div>
        <p class="eyebrow">Workify access</p>
        <h2>Connexion Workify</h2>
        <p class="muted">Connectez-vous pour retrouver votre espace. Les comptes autorises peuvent ouvrir l'espace prive depuis leur session.</p>
    </div>

    <form class="form-box auth-form" action="../controller/AuthController.php?action=login" method="post">
        <?php if ($error !== '') { ?>
            <div class="error-box"><?= htmlspecialchars($error, ENT_QUOTES); ?></div>
        <?php } ?>

        <div>
            <label for="email">Email</label>
            <input id="email" name="email" type="email" placeholder="Votre email" required>
        </div>

        <div>
            <label for="password">Mot de passe</label>
            <input id="password" name="password" type="password" placeholder="Votre mot de passe" required>
        </div>

        <div class="captcha-box">
            <div>
                <span class="captcha-label">Captcha</span>
                <strong><?= htmlspecialchars($captcha, ENT_QUOTES); ?> = ?</strong>
            </div>
            <input id="captcha_answer" name="captcha_answer" inputmode="numeric" placeholder="Reponse" required>
        </div>

        <div class="actions" style="margin-top: 18px;">
            <button class="btn btn-primary" type="submit">Se connecter</button>
            <a class="btn" href="../controller/HomeC.php">Retour accueil</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
