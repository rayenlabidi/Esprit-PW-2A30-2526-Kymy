<?php
// Vue publique: demande de reinitialisation du mot de passe.
include_once '../controller/AuthController.php';
$pageData = (new AuthController())->prepareForgotPasswordPage();
$success = $pageData['success'];
$error = $pageData['error'];

include_once 'header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <h2 class="text-center mb-4 text-primary fw-bold">Mot de passe oublie</h2>

        <?php if ($success): ?>
            <div class="alert alert-success"><i class="fa-solid fa-circle-check me-2"></i><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation me-2"></i><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-4">
                <label class="form-label">Adresse Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                    <input type="email" class="form-control" name="reset_email" placeholder="Entrez votre email" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Envoyer le lien</button>
            <div class="text-center mt-3">
                <a href="login.php" class="fw-bold text-primary">Retour a la connexion</a>
            </div>
        </form>
    </div>
</div>

<?php include_once 'footer.php'; ?>
