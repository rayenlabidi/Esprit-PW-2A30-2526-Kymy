<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workify - Administration</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<nav class="navbar">
    <div class="container">
        <div class="nav-brand">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-briefcase"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            Workify
        </div>
        
        <div class="nav-links">
            <a href="index.php?controller=formation" class="nav-link <?= (!isset($_GET['controller']) || $_GET['controller'] == 'formation') ? 'active' : '' ?>">Formations</a>
            <a href="index.php?controller=utilisateur" class="nav-link <?= (isset($_GET['controller']) && $_GET['controller'] == 'utilisateur') ? 'active' : '' ?>">Utilisateurs</a>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="index.php?controller=auth&action=logout" class="btn btn-secondary" style="margin-left: 2rem;">Log out</a>
            <?php else: ?>
                <a href="index.php?controller=auth&action=login" class="btn btn-primary" style="margin-left: 2rem;">Log in</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="main-content">
    <div class="container">
