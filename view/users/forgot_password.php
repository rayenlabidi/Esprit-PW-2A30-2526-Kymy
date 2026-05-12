<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="auth-panel">
    <div>
        <p class="eyebrow">Recuperation</p>
        <h2>Mot de passe oublie</h2>
        <p class="muted">Entrez l email de votre compte Workify. Si le compte existe, vous recevrez un mot de passe temporaire.</p>
    </div>

    <form class="form-box auth-form" action="../controller/AuthController.php?action=forgot" method="post">
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

        <div class="captcha-box recaptcha-box">
            <div>
                <span class="captcha-label">Verification</span>
                <strong>Protection Google reCAPTCHA</strong>
            </div>
            <div class="g-recaptcha" data-sitekey="<?= htmlspecialchars($recaptchaSiteKey, ENT_QUOTES); ?>"></div>
        </div>

        <div class="actions" style="margin-top: 18px;">
            <button class="btn btn-primary" type="submit">Envoyer</button>
            <a class="btn" href="../controller/AuthController.php?action=login">Retour connexion</a>
        </div>
    </form>
</div>

<script src="https://www.google.com/recaptcha/api.js?hl=fr" async defer></script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
