<?php
/*
 * Header commun de l'application.
 * Il demarre la session, protege les pages privees et affiche la navigation.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentPage = basename($_SERVER['PHP_SELF']);
$publicPages = ['index.php', 'login.php', 'forgot_password.php', 'reset_password.php'];

// Si la page n'est pas publique et que l'utilisateur n'est pas connecte, on le renvoie au login.
if (!in_array($currentPage, $publicPages, true) && !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$showSidebar = (!in_array($currentPage, $publicPages, true)
    && isset($_SESSION['user_id'])
    && in_array($_SESSION['user_role'] ?? '', ['admin', 'user'], true)
);
$isPublicPage = in_array($currentPage, $publicPages, true);
$workifyPhone = '+216 22822870';
$workifyEmail = 'contact@workify.tn';
$sessionUserName = $_SESSION['user_name'] ?? 'Utilisateur';
$sessionUserRole = $_SESSION['user_role'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workify Users</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%);
            --bg-color: #eff6ff; /* light blue bg */
            --card-bg: rgba(255, 255, 255, 0.85);
        }
        body { 
            background:
                radial-gradient(circle at top left, rgba(20, 184, 166, 0.16), transparent 34%),
                radial-gradient(circle at top right, rgba(37, 99, 235, 0.18), transparent 38%),
                linear-gradient(135deg, #f8fbff 0%, #e7f2ff 100%);
            font-family: 'Outfit', sans-serif; 
            min-height: 100vh;
            color: #1f2937;
        }
        /* Navbar Glassmorphism */
        .navbar { 
            background: rgba(255, 255, 255, 0.7) !important;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255,255,255,0.3);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
            padding: 15px 0;
        }
        .navbar-brand { 
            font-size: 1.7rem; 
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px; 
        }
        .navbar-brand i {
            color: #2563eb; /* fallback for icon */
            -webkit-text-fill-color: initial;
        }
        .navbar-text-custom {
            color: #4b5563;
            font-weight: 600;
        }
        .btn-custom-logout {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-custom-logout:hover {
            background: #ef4444;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }
        .public-nav-links {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin: 0 auto;
        }
        .public-nav-links a {
            display: inline-flex;
            align-items: center;
            min-height: 40px;
            padding: 0 12px;
            border-radius: 999px;
            color: #334155;
            font-size: 0.95rem;
            font-weight: 750;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .public-nav-links a:hover {
            color: #1d4ed8;
            background: rgba(37, 99, 235, 0.08);
        }
        .public-nav-links i {
            color: #2563eb;
            font-size: 0.92rem;
        }
        .public-nav-links .nav-login-link {
            color: #1d4ed8;
            background: rgba(37, 99, 235, 0.09);
            font-weight: 850;
        }
        @media (max-width: 991.98px) {
            .navbar > .container {
                flex-wrap: wrap;
                gap: 14px;
            }
            .public-nav-links {
                order: 3;
                width: 100%;
                justify-content: flex-start;
                overflow-x: auto;
                padding-bottom: 2px;
            }
        }

        /* Card Glassmorphism */
        .card { 
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 20px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        
        /* Modern Buttons */
        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            border-radius: 10px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.5);
        }
        
        /* Table Styles */
        .table {
            --bs-table-bg: transparent;
        }
        .table th { 
            background-color: transparent; 
            color: #6b7280; 
            font-weight: 700; 
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e5e7eb;
            padding: 15px 10px;
        }
        .table td {
            padding: 15px 10px;
            vertical-align: middle;
            border-bottom: 1px solid rgba(0,0,0,0.04);
            color: #374151;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(255,255,255,0.6);
            transform: scale(1.01);
            transition: all 0.2s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.02);
            border-radius: 10px;
        }
        
        /* Badges */
        .badge {
            border-radius: 8px;
            padding: 6px 12px;
            font-weight: 600;
            font-size: 0.8rem;
        }
        .badge.bg-success { background: rgba(16, 185, 129, 0.15) !important; color: #059669 !important; border: 1px solid rgba(16, 185, 129, 0.3); }
        .badge.bg-danger { background: rgba(239, 68, 68, 0.15) !important; color: #dc2626 !important; border: 1px solid rgba(239, 68, 68, 0.3); }
        .badge.bg-info { background: rgba(56, 189, 248, 0.15) !important; color: #0284c7 !important; border: 1px solid rgba(56, 189, 248, 0.3); }
        .badge.bg-secondary { background: rgba(107, 114, 128, 0.1) !important; color: #4b5563 !important; border: 1px solid rgba(107, 114, 128, 0.2); }
        .badge.bg-warning { background: rgba(245, 158, 11, 0.15) !important; color: #d97706 !important; border: 1px solid rgba(245, 158, 11, 0.3); }

        /* Search & Select form */
        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            padding: 10px 15px;
            font-family: 'Outfit', sans-serif;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }
        .input-group-text {
            background: transparent;
            border: 1px solid #e5e7eb;
            border-right: none;
            color: #9ca3af;
            border-radius: 10px 0 0 10px;
        }
        .form-control.search-input {
            border-left: none;
            padding-left: 0;
        }

        /* Sidebar */
        .app-shell {
            display: flex;
            gap: 28px;
            align-items: stretch;
        }
        .sidebar {
            position: sticky;
            top: 92px; /* navbar height-ish */
            align-self: flex-start;
        }
        .sidebar-card {
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.65);
            border-radius: 0 0 18px 18px;
            box-shadow: 0 22px 42px rgba(15, 23, 42, 0.08);
            overflow: hidden;
            min-height: 440px;
            padding: 28px 22px !important;
        }
        .sidebar-title {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 1.35rem;
            font-weight: 850;
            letter-spacing: -0.3px;
            color: #111827;
        }
        .sidebar-title i {
            color: #111827;
            font-size: 1.2rem;
        }
        .sidebar-meta {
            color: #6b7280;
            font-size: 0.98rem;
            font-weight: 850;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .sidebar-main-links {
            gap: 26px;
        }
        .sidebar-nav .nav-link {
            color: #1f2937;
            font-size: 1.25rem;
            font-weight: 850;
            border-radius: 12px;
            padding: 4px 0;
            display: flex;
            align-items: center;
            gap: 16px;
            border: 1px solid transparent;
        }
        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            background: transparent;
            border-color: transparent;
            color: #1d4ed8;
        }
        .sidebar-nav .nav-link i {
            width: 26px;
            text-align: center;
            color: #1d74ff;
            font-size: 1.1rem;
        }
        .sidebar-work-links {
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1px solid rgba(148, 163, 184, 0.18);
        }
        .sidebar-work-links .nav-link {
            color: #475569;
            font-size: 0.92rem;
            font-weight: 750;
            padding: 10px 12px;
        }
        .workspace-hero {
            position: relative;
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 20px;
            align-items: center;
            margin-bottom: 26px;
            padding: 24px;
            border-radius: 24px;
            overflow: hidden;
            color: #fff;
            background:
                linear-gradient(135deg, rgba(15, 23, 42, 0.76), rgba(29, 78, 216, 0.82)),
                url('https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=1200&q=80') center/cover;
            box-shadow: 0 24px 50px rgba(15, 23, 42, 0.16);
        }
        .workspace-hero h1 {
            margin: 0 0 6px;
            font-size: clamp(1.7rem, 3vw, 2.55rem);
            font-weight: 850;
            letter-spacing: 0;
        }
        .workspace-hero p {
            margin: 0;
            max-width: 680px;
            color: rgba(255, 255, 255, 0.78);
            line-height: 1.65;
        }
        .workspace-quick-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }
        .workspace-quick-actions .btn {
            border-radius: 14px;
            font-weight: 800;
        }
        .workspace-mini-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 24px;
        }
        .workspace-mini-card {
            padding: 16px;
            border: 1px solid rgba(148, 163, 184, 0.18);
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.72);
        }
        .workspace-mini-card i {
            color: #2563eb;
            margin-bottom: 10px;
        }
        .workspace-mini-card strong {
            display: block;
            color: #0f172a;
        }
        .workspace-mini-card span {
            color: #64748b;
            font-size: 0.9rem;
        }
        @media (max-width: 991.98px) {
            .app-shell { flex-direction: column; }
            .sidebar { position: static; top: auto; width: 100% !important; }
            .workspace-hero {
                grid-template-columns: 1fr;
            }
            .workspace-quick-actions {
                justify-content: flex-start;
            }
            .workspace-mini-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg mb-5 sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="<?= isset($_SESSION['user_id']) ? 'listeUtilisateurs.php' : 'index.php' ?>">
        <i class="fa-solid fa-layer-group me-2" style="font-size: 1.4rem;"></i>Workify
    </a>
    <?php if(isset($_SESSION['user_id'])): ?>
    <div class="d-flex align-items-center">
        <span class="navbar-text-custom me-4 d-none d-md-block">
            <i class="fa-solid fa-circle-user text-primary me-2"></i> <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?>
        </span>
        <a href="logout.php" class="btn btn-custom-logout btn-sm px-3 py-2">
            <i class="fa-solid fa-power-off me-2"></i>Déconnexion
        </a>
    </div>
    <?php else: ?>
    <div class="public-nav-links" aria-label="Navigation publique">
        <a href="index.php#home"><i class="fa-solid fa-house"></i>Accueil</a>
        <a href="index.php#about"><i class="fa-solid fa-circle-info"></i>À propos</a>
        <a href="index.php#workify-features"><i class="fa-solid fa-briefcase"></i>Services</a>
        <a href="index.php#contact"><i class="fa-solid fa-headset"></i>Contact</a>
        <?php if ($currentPage !== 'login.php'): ?>
            <a href="login.php" class="nav-login-link"><i class="fa-solid fa-right-to-bracket"></i>Connexion</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
</nav>

<?php if ($showSidebar): ?>
<div class="container-fluid px-3 px-lg-4">
    <div class="app-shell">
        <aside class="sidebar" style="width: 360px;">
            <div class="sidebar-card p-3">
                <div class="sidebar-meta mb-3">Navigation</div>
                <div class="sidebar-title mb-4">
                    <i class="fa-solid fa-layer-group"></i>Espace
                </div>
                <nav class="nav flex-column sidebar-nav sidebar-main-links">
                    <a class="nav-link" href="listeUtilisateurs.php">
                        <i class="fa-solid fa-briefcase"></i>Jobs
                    </a>
                    <a class="nav-link" href="stats.php">
                        <i class="fa-solid fa-calendar-days"></i>Événement
                    </a>
                    <a class="nav-link" href="profile.php?id=<?= urlencode((string)($_SESSION['user_id'] ?? '')) ?>">
                        <i class="fa-solid fa-message"></i>Message
                    </a>
                    <a class="nav-link" href="index.php" target="_blank" rel="noopener">
                        <i class="fa-solid fa-graduation-cap"></i>Formation
                    </a>
                </nav>
            </div>
        </aside>

        <main style="flex: 1; min-width: 0;">
            <div class="card mb-5 animate-fade-in">
                <div class="card-body p-4 p-md-5">
                    <?php
                    if (isset($_SESSION['flash'])) {
                        $flashes = $_SESSION['flash'];
                        unset($_SESSION['flash']);
                        if (!is_array($flashes) || !isset($flashes[0])) {
                            $flashes = [$flashes];
                        }
                        foreach ($flashes as $f) {
                            if (!is_array($f)) {
                                continue;
                            }
                            $type = $f['type'] ?? 'info';
                            $message = $f['message'] ?? '';
                            $title = $f['title'] ?? '';
                            if ($message === '' && $title === '') {
                                continue;
                            }
                            ?>
                            <div class="alert alert-<?= htmlspecialchars($type) ?> d-flex align-items-start justify-content-between gap-3" role="alert" style="border-radius: 14px;">
                                <div>
                                    <?php if ($title !== ''): ?>
                                        <div class="fw-bold mb-1"><?= htmlspecialchars($title) ?></div>
                                    <?php endif; ?>
                                    <?php if ($message !== ''): ?>
                                        <div><?= htmlspecialchars($message) ?></div>
                                    <?php endif; ?>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php
                        }
                    }
                    ?>
                    <section class="workspace-hero" aria-label="Accueil de l'espace Workify">
                        <div>
                            <div class="small fw-bold text-uppercase mb-2" style="letter-spacing: 0.12em; color: rgba(255,255,255,0.7);">
                                Espace Workify
                            </div>
                            <h1>Bienvenue, <?= htmlspecialchars($sessionUserName) ?></h1>
                            <p>
                                GÃ©rez vos profils, vos accÃ¨s et vos informations avec une interface plus claire,
                                rapide et pensÃ©e pour un travail professionnel.
                            </p>
                        </div>
                        <div class="workspace-quick-actions">
                            <?php if ($sessionUserRole === 'admin'): ?>
                                <a href="ajoutUtilisateur.php" class="btn btn-light px-4 py-2">
                                    <i class="fa-solid fa-user-plus me-2"></i>Ajouter
                                </a>
                                <a href="stats.php" class="btn btn-outline-light px-4 py-2">
                                    <i class="fa-solid fa-chart-simple me-2"></i>Statistiques
                                </a>
                            <?php else: ?>
                                <a href="profile.php?id=<?= urlencode((string)($_SESSION['user_id'] ?? '')) ?>" class="btn btn-light px-4 py-2">
                                    <i class="fa-solid fa-id-card me-2"></i>Voir mon profil
                                </a>
                            <?php endif; ?>
                        </div>
                    </section>

                    <div class="workspace-mini-grid" aria-label="Points forts de l'espace connectÃ©">
                        <div class="workspace-mini-card">
                            <i class="fa-solid fa-shield-halved"></i>
                            <strong>AccÃ¨s sÃ©curisÃ©</strong>
                            <span>Session protÃ©gÃ©e et donnÃ©es utilisateur centralisÃ©es.</span>
                        </div>
                        <div class="workspace-mini-card">
                            <i class="fa-solid fa-bolt"></i>
                            <strong>Actions rapides</strong>
                            <span>Les fonctions importantes restent toujours Ã  portÃ©e.</span>
                        </div>
                        <div class="workspace-mini-card">
                            <i class="fa-solid fa-qrcode"></i>
                            <strong>IdentitÃ© digitale</strong>
                            <span>Profils, QR codes et informations personnelles mieux valorisÃ©s.</span>
                        </div>
                    </div>
<?php else: ?>
<div class="container">
    <div class="card mb-5 animate-fade-in">
        <div class="card-body p-4 p-md-5">
            <?php
            if (isset($_SESSION['flash'])) {
                $flashes = $_SESSION['flash'];
                unset($_SESSION['flash']);
                if (!is_array($flashes) || !isset($flashes[0])) {
                    $flashes = [$flashes];
                }
                foreach ($flashes as $f) {
                    if (!is_array($f)) {
                        continue;
                    }
                    $type = $f['type'] ?? 'info';
                    $message = $f['message'] ?? '';
                    $title = $f['title'] ?? '';
                    if ($message === '' && $title === '') {
                        continue;
                    }
                    ?>
                    <div class="alert alert-<?= htmlspecialchars($type) ?> d-flex align-items-start justify-content-between gap-3" role="alert" style="border-radius: 14px;">
                        <div>
                            <?php if ($title !== ''): ?>
                                <div class="fw-bold mb-1"><?= htmlspecialchars($title) ?></div>
                            <?php endif; ?>
                            <?php if ($message !== ''): ?>
                                <div><?= htmlspecialchars($message) ?></div>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php
                }
            }
            ?>
<?php endif; ?>
