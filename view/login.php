<?php
include_once '../controller/AuthController.php';
$pageData = (new AuthController())->prepareLoginPage();
$error = $pageData['error'];

include_once 'header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <h2 class="text-center mb-4 text-primary fw-bold">Connexion</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation me-2"></i><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label">Adresse Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                    <input type="email" class="form-control" name="email" placeholder="Entrez votre email" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Mot de passe</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" class="form-control" name="password" placeholder="Entrez votre mot de passe" required>
                </div>
            </div>

            <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-3">
                <button type="submit" class="btn btn-primary flex-grow-1 py-2 fw-bold">Se connecter a Workify</button>
                <a href="forgot_password.php" class="fw-bold text-primary text-center text-sm-nowrap">
                    Mot de passe oublie
                </a>
            </div>
        </form>
    </div>
</div>

<?php include_once 'footer.php'; ?>
