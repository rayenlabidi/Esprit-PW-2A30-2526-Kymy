<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="auth-panel signup-panel">
    <div>
        <p class="eyebrow">Nouveau compte</p>
        <h2>Creer un compte Workify</h2>
        <p class="muted">Inscrivez-vous pour postuler aux jobs, suivre les formations et garder vos demandes dans un espace unique.</p>
    </div>

    <form class="form-box auth-form" data-validate="signup" action="../controller/AuthController.php?action=signup" method="post">
        <?php if ($error !== '') { ?>
            <div class="error-box"><?= htmlspecialchars($error, ENT_QUOTES); ?></div>
        <?php } ?>

        <?php if ($successMessage !== '') { ?>
            <div class="success-box"><?= htmlspecialchars($successMessage, ENT_QUOTES); ?></div>
        <?php } ?>

        <div class="form-grid">
            <div>
                <label for="first_name">Prenom</label>
                <input id="first_name" name="first_name" value="<?= htmlspecialchars($formData['first_name'], ENT_QUOTES); ?>" required>
            </div>
            <div>
                <label for="last_name">Nom</label>
                <input id="last_name" name="last_name" value="<?= htmlspecialchars($formData['last_name'], ENT_QUOTES); ?>" required>
            </div>
            <div>
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="<?= htmlspecialchars($formData['email'], ENT_QUOTES); ?>" required>
            </div>
            <div>
                <label for="phone">Telephone</label>
                <input id="phone" name="phone" value="<?= htmlspecialchars($formData['phone'], ENT_QUOTES); ?>">
            </div>
            <div>
                <label for="role">Type de compte</label>
                <select id="role" name="role" required>
                    <option value="freelancer" <?= $formData['role'] === 'freelancer' ? 'selected' : ''; ?>>Freelancer</option>
                    <option value="boss" <?= $formData['role'] === 'boss' ? 'selected' : ''; ?>>Entreprise</option>
                </select>
            </div>
            <div>
                <label for="headline">Titre professionnel</label>
                <input id="headline" name="headline" value="<?= htmlspecialchars($formData['headline'], ENT_QUOTES); ?>" placeholder="Ex: Developpeur PHP, recruteur, designer...">
            </div>
            <div>
                <label for="password">Mot de passe</label>
                <input id="password" name="password" type="password" minlength="8" required>
            </div>
            <div>
                <label for="password_confirm">Confirmer</label>
                <input id="password_confirm" name="password_confirm" type="password" minlength="8" required>
            </div>
        </div>

        <div class="captcha-box recaptcha-box">
            <div>
                <span class="captcha-label">Verification</span>
                <strong>Protection Google reCAPTCHA</strong>
            </div>
            <div class="g-recaptcha" data-sitekey="<?= htmlspecialchars($recaptchaSiteKey, ENT_QUOTES); ?>"></div>
        </div>

        <div class="actions" style="margin-top: 18px;">
            <button class="btn btn-primary" type="submit">Creer mon compte</button>
            <a class="btn" href="../controller/AuthController.php?action=login">Deja inscrit</a>
        </div>
    </form>
</div>

<script src="https://www.google.com/recaptcha/api.js?hl=fr" async defer></script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
