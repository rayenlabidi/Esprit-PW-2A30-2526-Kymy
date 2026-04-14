<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Workify</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body {
            background-color: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .login-card {
            background: white;
            padding: 3rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-logo {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 2rem;
            color: var(--text-main);
        }
        .login-logo svg {
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

<div class="login-card">
    <div class="login-logo">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
        Workify
    </div>

    <h2>Bon retour !</h2>
    <p style="color: var(--text-muted); margin-bottom: 2rem; font-size: 0.9rem;">Veuillez vous identifier pour accéder à la plateforme.</p>

    <?php if(!empty($error)): ?>
        <div class="error-message">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="index.php?controller=auth&action=login" method="POST" style="text-align: left;">
        <div class="form-group">
            <label class="form-label">Email Professionnel</label>
            <input type="email" name="email" class="form-control" placeholder="admin@workify.com" required>
        </div>

        <div class="form-group">
            <label class="form-label">Mot de passe</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem; padding: 0.75rem;">
            Se connecter
        </button>
    </form>
    
    <div style="margin-top: 2rem; font-size: 0.9rem; color: var(--text-muted);">
        Vous n'avez pas de compte ? <a href="index.php?controller=auth&action=register" style="color: var(--primary); font-weight: 600;">Créer un compte</a>
    </div>
</div>

</body>
</html>
