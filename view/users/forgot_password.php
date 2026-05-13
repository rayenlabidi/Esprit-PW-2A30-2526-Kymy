<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="auth-panel">
    <div>
        <p class="eyebrow">Recuperation</p>
        <h2>Mot de passe oublie</h2>
        <p class="muted">Entrez l email de votre compte Workify. Si le compte existe, vous recevrez un mot de passe temporaire.</p>
    </div>

    <form class="form-box auth-form" data-validate="forgot" action="../controller/AuthController.php?action=forgot" method="post">
        <?php if ($error !== '') { ?>
            <div class="error-box"><?= htmlspecialchars($error, ENT_QUOTES); ?></div>
        <?php } ?>

        <?php if ($successMessage !== '') { ?>
            <div class="success-box"><?= htmlspecialchars($successMessage, ENT_QUOTES); ?></div>
        <?php } ?>

        <div>
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="<?= htmlspecialchars($email, ENT_QUOTES); ?>" placeholder="Votre email" required>
        </div>

        <?php $captchaScope = 'forgot'; include __DIR__ . '/../includes/captcha.php'; ?>

        <div class="actions" style="margin-top: 18px;">
            <button class="btn btn-primary" type="submit">Envoyer</button>
            <a class="btn" href="../controller/AuthController.php?action=login">Retour connexion</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
