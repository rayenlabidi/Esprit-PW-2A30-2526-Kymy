<?php
include_once '../controller/AuthController.php';
$pageData = (new AuthController())->prepareResetPasswordPage();
$error = $pageData['error'];
$token = $pageData['token'];
$resetData = $pageData['resetData'];

include_once 'header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <h2 class="text-center mb-4 text-primary fw-bold">Nouveau mot de passe</h2>

        <?php if (!$resetData): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation me-2"></i>Le lien de reinitialisation est invalide ou expire.
            </div>
            <div class="text-center">
                <a href="forgot_password.php" class="fw-bold text-primary">Demander un nouveau lien</a>
            </div>
        <?php else: ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation me-2"></i><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                <div class="mb-3">
                    <label class="form-label">Nouveau mot de passe</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" class="form-control" name="new_password" placeholder="Au moins 8 caracteres" minlength="8" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Confirmer le mot de passe</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" class="form-control" name="new_password_confirm" placeholder="Confirmez le mot de passe" minlength="8" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Valider le nouveau mot de passe</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include_once 'footer.php'; ?>
