<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte - Workify</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body {
            background-color: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .register-card {
            background: white;
            padding: 3rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }
        .register-logo {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 2rem;
            color: var(--text-main);
        }
        .register-logo svg {
            color: var(--primary);
        }
        .error-message {
            background-color: #fee2e2;
            color: #ef4444;
            padding: 0.75rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>

<div class="register-card">
    <div class="register-logo">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
        Workify
    </div>

    <h2>Créer un compte</h2>
    <p style="color: var(--text-muted); margin-bottom: 2rem; font-size: 0.9rem;">Rejoignez-nous en remplissant le formulaire ci-dessous.</p>

    <?php if(!empty($error)): ?>
        <div class="error-message">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="index.php?controller=auth&action=register" method="POST" style="text-align: left;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Prénom</label>
                <input type="text" name="prenom" class="form-control" required>
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Nom</label>
                <input type="text" name="nom" class="form-control" required>
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 1rem;">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label class="form-label">Mot de passe</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem;">
            S'inscrire
        </button>
    </form>
    
    <div style="margin-top: 2rem; font-size: 0.9rem; color: var(--text-muted);">
        Vous avez déjà un compte ? <a href="index.php?controller=auth&action=login" style="color: var(--primary); font-weight: 600;">Se connecter</a>
    </div>
</div>

</body>
</html>
