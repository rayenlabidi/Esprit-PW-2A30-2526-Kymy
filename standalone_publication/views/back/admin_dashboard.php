<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Prevent controller AJAX handlers from running
define('ADMIN_AJAX_HANDLER', true);

require_once __DIR__ . '/../../controllers/PublicationC.php';
require_once __DIR__ . '/../../controllers/MessageC.php';

$pubController = new PublicationC();
$msgController = new MessageC();

// Get statistics
$posts = $pubController->ListePublications();
$comments = $pubController->ListeAllComments();
$messages = $msgController->GetAllMessagesAdmin();
$users = $msgController->GetAllUsers();

// Calculate stats
$total_posts = count($posts);
$total_comments = count($comments);
$total_messages = count($messages);
$total_users = count($users);

// Get recent activity
$recent_posts = array_slice($posts, 0, 5);
$recent_messages = array_slice($messages, 0, 5);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard | Workify</title>
<link rel="stylesheet" href="../../public/css/workify-tokens.css">
<link rel="stylesheet" href="../../public/css/admin.css">
<style>
.admin-layout {
    display: flex;
    margin-top: var(--nav-h);
    min-height: calc(100vh - var(--nav-h));
}

.admin-sidebar {
    width: 280px;
    background: var(--bg-card);
    border-right: 1px solid var(--border);
    padding: 24px 0;
    position: fixed;
    height: calc(100vh - var(--nav-h));
    overflow-y: auto;
}

.admin-sidebar-header {
    padding: 0 20px 20px 20px;
    border-bottom: 1px solid var(--border);
    margin-bottom: 20px;
}

.admin-sidebar-header h3 {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-1);
    margin-bottom: 4px;
}

.admin-sidebar-header p {
    font-size: 12px;
    color: var(--text-3);
}

.admin-nav {
    list-style: none;
    padding: 0;
    margin: 0;
}

.admin-nav-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 20px;
    color: var(--text-2);
    text-decoration: none;
    transition: all 0.2s;
    border-left: 3px solid transparent;
    font-size: 14px;
    font-weight: 500;
}

.admin-nav-item:hover {
    background: var(--bg-hover);
    color: var(--text-1);
}

.admin-nav-item.active {
    background: var(--blue-light);
    color: var(--blue);
    border-left-color: var(--blue);
}

.admin-nav-item .nav-icon {
    width: 20px;
    text-align: center;
    font-size: 18px;
}

.admin-nav-item .nav-badge {
    margin-left: auto;
    background: var(--red-light);
    color: var(--red);
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.admin-main {
    flex: 1;
    margin-left: 280px;
    padding: 30px;
    background: var(--bg);
    min-height: calc(100vh - var(--nav-h));
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 24px;
    margin-bottom: 40px;
}

.stat-card {
    background: var(--bg-card);
    border-radius: var(--radius-lg);
    padding: 24px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border);
    transition: transform 0.2s, box-shadow 0.2s;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-md);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 16px;
    font-size: 28px;
}

.stat-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--text-1);
    margin-bottom: 8px;
}

.stat-label {
    font-size: 14px;
    color: var(--text-3);
    font-weight: 500;
}

.recent-section {
    background: var(--bg-card);
    border-radius: var(--radius-lg);
    padding: 24px;
    margin-bottom: 24px;
    border: 1px solid var(--border);
}

.recent-section h2 {
    font-size: 18px;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid var(--border);
}

.recent-item {
    padding: 12px 0;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 12px;
}

.recent-item:last-child {
    border-bottom: none;
}

.recent-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    flex-shrink: 0;
}

.recent-content {
    flex: 1;
}

.recent-title {
    font-weight: 600;
    color: var(--text-1);
    margin-bottom: 4px;
}

.recent-meta {
    font-size: 12px;
    color: var(--text-3);
}

.view-all {
    margin-top: 16px;
    text-align: right;
}

.view-all a {
    color: var(--blue);
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
}

.view-all a:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .admin-sidebar {
        width: 80px;
    }
    .admin-sidebar-header h3,
    .admin-sidebar-header p,
    .admin-nav-item span:not(.nav-icon) {
        display: none;
    }
    .admin-main {
        margin-left: 80px;
        padding: 15px;
    }
    .admin-nav-item {
        justify-content: center;
        padding: 12px;
    }
    .admin-nav-item .nav-icon {
        font-size: 22px;
    }
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>
</head>
<body>

<nav class="wf-nav">
  <a href="admin_dashboard.php" class="wf-nav-logo">
    <div class="wf-nav-logo-box">
      <svg viewBox="0 0 16 16"><rect x="2" y="4" width="5" height="8" rx="1"/><rect x="9" y="4" width="5" height="8" rx="1"/><rect x="2" y="1" width="12" height="2" rx="1"/></svg>
    </div>
    Workify Admin
  </a>
  <div class="wf-nav-right">
    <div class="wf-avatar wf-avatar-36 av-blue">AD</div>
  </div>
</nav>

<div class="admin-layout">
  <!-- Left Sidebar Dashboard -->
  <aside class="admin-sidebar">
    <div class="admin-sidebar-header">
      <h3>Admin Panel</h3>
      <p>Manage your platform</p>
    </div>
    <ul class="admin-nav">
      <li><a href="admin_dashboard.php" class="admin-nav-item active">
        <span class="nav-icon">📊</span>
        <span>Dashboard</span>
      </a></li>
      <li><a href="admin.php" class="admin-nav-item">
        <span class="nav-icon">📝</span>
        <span>Publications</span>
        <span class="nav-badge"><?php echo $total_posts; ?></span>
      </a></li>
      <li><a href="admin_messages.php" class="admin-nav-item">
        <span class="nav-icon">💬</span>
        <span>Messages</span>
        <span class="nav-badge"><?php echo $total_messages; ?></span>
      </a></li>
    </ul>
  </aside>

  <!-- Main Content -->
  <main class="admin-main">
    <div class="admin-header">
      <h1>📊 Dashboard Overview</h1>
      <p>Welcome back! Here's what's happening on Workify today.</p>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">📝</div>
        <div class="stat-value"><?php echo $total_posts; ?></div>
        <div class="stat-label">Total Publications</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">💬</div>
        <div class="stat-value"><?php echo $total_comments; ?></div>
        <div class="stat-label">Total Comments</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">✉️</div>
        <div class="stat-value"><?php echo $total_messages; ?></div>
        <div class="stat-label">Total Messages</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-value"><?php echo $total_users; ?></div>
        <div class="stat-label">Total Users</div>
      </div>
    </div>

    <!-- Recent Publications -->
    <div class="recent-section">
      <h2>📰 Recent Publications</h2>
      <?php if(empty($recent_posts)): ?>
        <p style="color: var(--text-3); text-align: center; padding: 20px;">No publications yet</p>
      <?php else: ?>
        <?php foreach($recent_posts as $post): ?>
        <div class="recent-item">
          <div class="recent-avatar <?php echo $post['user_avatar']; ?>"><?php echo htmlspecialchars($post['user_init']); ?></div>
          <div class="recent-content">
            <div class="recent-title"><?php echo htmlspecialchars($post['user_name']); ?></div>
            <div class="recent-meta"><?php echo htmlspecialchars(substr($post['content'], 0, 80)) . (strlen($post['content']) > 80 ? '...' : ''); ?></div>
          </div>
          <div class="recent-meta"><?php echo date('M d, H:i', strtotime($post['created_at'])); ?></div>
        </div>
        <?php endforeach; ?>
        <div class="view-all">
          <a href="admin.php">View all publications →</a>
        </div>
      <?php endif; ?>
    </div>

    <!-- Recent Messages -->
    <div class="recent-section">
      <h2>✉️ Recent Messages</h2>
      <?php if(empty($recent_messages)): ?>
        <p style="color: var(--text-3); text-align: center; padding: 20px;">No messages yet</p>
      <?php else: ?>
        <?php foreach($recent_messages as $msg): ?>
        <div class="recent-item">
          <div class="recent-avatar <?php echo $msg['sender_avatar']; ?>"><?php echo htmlspecialchars($msg['sender_init']); ?></div>
          <div class="recent-content">
            <div class="recent-title"><?php echo htmlspecialchars($msg['sender_name']); ?> → <?php echo htmlspecialchars($msg['receiver_name']); ?></div>
            <div class="recent-meta"><?php echo htmlspecialchars(substr($msg['content'], 0, 80)) . (strlen($msg['content']) > 80 ? '...' : ''); ?></div>
          </div>
          <div class="recent-meta"><?php echo date('M d, H:i', strtotime($msg['created_at'])); ?></div>
        </div>
        <?php endforeach; ?>
        <div class="view-all">
          <a href="admin_messages.php">View all messages →</a>
        </div>
      <?php endif; ?>
    </div>
  </main>
</div>

</body>
</html>