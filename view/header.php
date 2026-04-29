<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentPage = basename($_SERVER['PHP_SELF']);
$publicPages = ['login.php', 'forgot_password.php', 'reset_password.php'];
if (!in_array($currentPage, $publicPages, true) && !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$showSidebar = (!in_array($currentPage, $publicPages, true)
    && isset($_SESSION['user_id'])
    && in_array($_SESSION['user_role'] ?? '', ['admin', 'user'], true)
);
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
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
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
            gap: 24px;
            align-items: stretch;
        }
        .sidebar {
            position: sticky;
            top: 92px; /* navbar height-ish */
            align-self: flex-start;
        }
        .sidebar-card {
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.65);
            border-radius: 16px;
            box-shadow: 0 18px 36px rgba(0,0,0,0.07);
            overflow: hidden;
        }
        .sidebar-title {
            font-weight: 800;
            letter-spacing: -0.3px;
            color: #111827;
        }
        .sidebar-meta {
            color: #6b7280;
            font-size: 0.82rem;
            font-weight: 650;
            text-transform: uppercase;
            letter-spacing: 0.7px;
        }
        .sidebar-nav .nav-link {
            color: #1f2937;
            font-weight: 700;
            border-radius: 10px;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid transparent;
        }
        .sidebar-nav .nav-link:hover {
            background: rgba(59, 130, 246, 0.08);
            border-color: rgba(59, 130, 246, 0.18);
            color: #1d4ed8;
        }
        .sidebar-nav .nav-link.disabled {
            opacity: 0.95;
            cursor: not-allowed;
        }
        .sidebar-nav .nav-link i {
            width: 18px;
            text-align: center;
        }
        @media (max-width: 991.98px) {
            .app-shell { flex-direction: column; }
            .sidebar { position: static; top: auto; width: 100% !important; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg mb-5 sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="<?= isset($_SESSION['user_id']) ? 'listeUtilisateurs.php' : 'login.php' ?>">
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
    <?php endif; ?>
  </div>
</nav>

<?php if ($showSidebar): ?>
<div class="container-fluid px-3 px-lg-4">
    <div class="app-shell">
        <aside class="sidebar" style="width: 290px;">
            <div class="sidebar-card p-3">
                <div class="sidebar-meta mb-2">Navigation</div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="sidebar-title"><i class="fa-solid fa-layer-group me-2 text-primary"></i>Espace</div>
                </div>
                <nav class="nav flex-column sidebar-nav">
                    <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true" onclick="return false;"><i class="fa-solid fa-briefcase text-primary"></i>Jobs</a>
                    <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true" onclick="return false;"><i class="fa-solid fa-calendar-days text-primary"></i>Evenement</a>
                    <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true" onclick="return false;"><i class="fa-solid fa-message text-primary"></i>Message</a>
                    <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true" onclick="return false;"><i class="fa-solid fa-graduation-cap text-primary"></i>Formation</a>
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
