<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Prevent controller AJAX handlers from running - this page has its own
define('ADMIN_AJAX_HANDLER', true);

require_once __DIR__ . '/../../controllers/MessageC.php';
require_once __DIR__ . '/../../controllers/PublicationC.php';

$controller = new MessageC();
$pubController = new PublicationC();

// Handle AJAX requests
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isAjax) {
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';
    $response = ['success' => false];

    switch ($action) {
        case 'add_user':
            if (empty($_POST['user_id']) || empty($_POST['name']) || empty($_POST['init'])) {
                $response = ['success' => false, 'error' => 'All fields are required'];
            } else {
                $result = $controller->AddUser($_POST['user_id'], $_POST['name'], $_POST['init'], $_POST['avatar'], $_POST['role']);
                $response = ['success' => (bool)$result];
            }
            break;
        case 'edit_message_admin':
            $result = $controller->EditMessageAdmin($_POST['message_id'], trim($_POST['content']));
            $response = ['success' => (bool)$result];
            break;
        case 'delete_message_admin':
            $result = $controller->DeleteMessageAdmin($_POST['message_id']);
            $response = ['success' => (bool)$result];
            break;
        case 'unflag_message':
            $result = $controller->UnflagMessage($_POST['message_id']);
            $response = ['success' => (bool)$result];
            break;
        default:
            $response = ['success' => false, 'error' => 'Unknown action: ' . $action];
            break;
    }

    echo json_encode($response);
    exit;
}

$messages = $controller->GetAllMessagesAdmin();
$users = $controller->GetAllUsers();
$posts = $pubController->ListePublications();

// Count unread and flagged
$unreadCount = 0;
$flaggedCount = 0;
foreach ($messages as $m) {
    if (!$m['is_read']) $unreadCount++;
    if ($m['is_flagged']) $flaggedCount++;
}
$message = '';
$error = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel - Messages | Workify</title>
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
    }
    .admin-nav-item {
        justify-content: center;
        padding: 12px;
    }
    .admin-nav-item .nav-icon {
        font-size: 22px;
    }
}

.flagged-row {
    background-color: #fff0f0;
}
.flagged-row:hover {
    background-color: #ffe0e0 !important;
}

/* Filter Bar */
.msg-filter-bar {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.msg-filter-btn {
    padding: 8px 18px;
    border: 1.5px solid var(--border);
    border-radius: 20px;
    background: var(--bg-card);
    color: var(--text-2);
    font-family: var(--font);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.msg-filter-btn:hover {
    border-color: var(--blue);
    color: var(--blue);
    background: var(--blue-light);
}

.msg-filter-btn.active {
    background: var(--blue);
    color: white;
    border-color: var(--blue);
}

.msg-filter-btn.active:hover {
    background: var(--blue-dark);
}

.filter-badge {
    background: rgba(255,255,255,0.25);
    padding: 1px 7px;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 700;
    min-width: 18px;
    text-align: center;
}

.msg-filter-btn:not(.active) .filter-badge {
    background: var(--bg-hover);
    color: var(--text-2);
}

.msg-filter-btn.badge-red:not(.active) .filter-badge {
    background: var(--red-light);
    color: var(--red);
}

.msg-filter-btn.badge-orange:not(.active) .filter-badge {
    background: #fff3e0;
    color: #e65100;
}

.no-results-row td {
    text-align: center;
    padding: 30px !important;
    color: var(--text-3);
    font-style: italic;
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
      <li><a href="admin_dashboard.php" class="admin-nav-item">
        <span class="nav-icon">📊</span>
        <span>Dashboard</span>
      </a></li>
      <li><a href="admin.php" class="admin-nav-item">
        <span class="nav-icon">📝</span>
        <span>Publications</span>
        <span class="nav-badge"><?php echo count($posts); ?></span>
      </a></li>
      <li><a href="admin_messages.php" class="admin-nav-item active">
        <span class="nav-icon">💬</span>
        <span>Messages</span>
        <span class="nav-badge"><?php echo count($messages); ?></span>
      </a></li>
    </ul>
  </aside>

  <!-- Main Content -->
  <main class="admin-main">
    <div class="admin-header">
      <h1>💬 Message Management</h1>
      <p>Total messages: <?php echo count($messages); ?> | Total users: <?php echo count($users); ?></p>
    </div>

    <div id="messageAlert"></div>
    <div id="errorAlert"></div>

    <!-- Add User Section -->
    <div class="crud-section">
      <h2>➕ Add New User</h2>
      <div class="form-row">
        <div class="form-group">
          <label>User ID:</label>
          <input type="text" id="adminUserId" placeholder="e.g., john_doe">
          <small class="error-message" id="adminUserIdError"></small>
        </div>
        <div class="form-group">
          <label>Name:</label>
          <input type="text" id="adminUserName" placeholder="Full name">
          <small class="error-message" id="adminUserNameError"></small>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Initials:</label>
          <input type="text" id="adminUserInit" maxlength="5" placeholder="JD">
          <small class="error-message" id="adminUserInitError"></small>
        </div>
        <div class="form-group">
          <label>Avatar Color:</label>
          <select id="adminUserAvatar">
            <option value="av-blue">Blue</option>
            <option value="av-green">Green</option>
            <option value="av-orange">Orange</option>
            <option value="av-purple">Purple</option>
            <option value="av-pink">Pink</option>
            <option value="av-teal">Teal</option>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Role:</label>
          <select id="adminUserRole">
            <option value="Freelancer">Freelancer</option>
            <option value="Client">Client</option>
          </select>
        </div>
      </div>
      <button class="btn-create" onclick="adminAddUser()">➕ Add User</button>
    </div>

    <!-- All Messages Table -->
    <div class="crud-section">
      <h2>📄 All Messages</h2>

      <!-- Filter Bar -->
      <div class="msg-filter-bar">
        <button class="msg-filter-btn active" id="filterAll" onclick="filterMessages('all')">
          📋 All <span class="filter-badge"><?php echo count($messages); ?></span>
        </button>
        <button class="msg-filter-btn badge-orange" id="filterUnread" onclick="filterMessages('unread')">
          📩 Unread <span class="filter-badge"><?php echo $unreadCount; ?></span>
        </button>
        <button class="msg-filter-btn badge-red" id="filterFlagged" onclick="filterMessages('flagged')">
          🚩 Flagged <span class="filter-badge"><?php echo $flaggedCount; ?></span>
        </button>
        <button class="msg-filter-btn" id="filterRead" onclick="filterMessages('read')">
          ✅ Read <span class="filter-badge"><?php echo count($messages) - $unreadCount; ?></span>
        </button>
      </div>

      <div class="admin-table-wrapper">
        <table class="admin-table" id="messagesTable">
          <thead>
            <tr><th>ID</th><th>From</th><th>To</th><th>Message</th><th>Read</th><th>Status</th><th>Created</th><th>Actions</th></tr>
          </thead>
          <tbody id="messagesTableBody">
            <?php foreach($messages as $msg): ?>
            <tr class="<?php echo $msg['is_flagged'] ? 'flagged-row' : ''; ?>" data-read="<?php echo $msg['is_read'] ? '1' : '0'; ?>" data-flagged="<?php echo $msg['is_flagged'] ? '1' : '0'; ?>">
              <td><?php echo $msg['id']; ?></td>
              <td>
                <div class="author-cell">
                  <div class="wf-avatar wf-avatar-32 <?php echo $msg['sender_avatar']; ?>"><?php echo htmlspecialchars($msg['sender_init']); ?></div>
                  <span><?php echo htmlspecialchars($msg['sender_name']); ?></span>
                </div>
              </td>
              <td>
                <div class="author-cell">
                  <div class="wf-avatar wf-avatar-32 <?php echo $msg['receiver_avatar']; ?>"><?php echo htmlspecialchars($msg['receiver_init']); ?></div>
                  <span><?php echo htmlspecialchars($msg['receiver_name']); ?></span>
                </div>
              </td>
              <td class="content-cell"><?php echo htmlspecialchars(substr($msg['content'], 0, 80)) . (strlen($msg['content']) > 80 ? '...' : ''); ?></td>
              <td><?php echo $msg['is_read'] ? '✅ Yes' : '❌ No'; ?></td>
              <td>
                <?php if ($msg['is_flagged']): ?>
                  <span style="background:var(--red-light); color:var(--red); padding:4px 8px; border-radius:4px; font-size:12px; font-weight:bold;">FLAGGED</span>
                <?php else: ?>
                  <span style="color:var(--text-3); font-size:12px;">Normal</span>
                <?php endif; ?>
              </td>
              <td><?php echo date('M d, Y H:i', strtotime($msg['created_at'])); ?></td>
              <td>
                <div class="action-buttons">

                  <button class="btn-edit" onclick="adminEditMessage(<?php echo $msg['id']; ?>, '<?php echo htmlspecialchars(addslashes($msg['content'])); ?>')">✏️ Edit</button>
                  <button class="btn-delete" onclick="adminDeleteMessage(<?php echo $msg['id']; ?>)">🗑️ Delete</button>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>

<!-- Edit Message Modal -->
<div id="adminEditModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeAdminEditModal()">&times;</span>
    <h3>Edit Message</h3>
    <div class="form-group">
      <textarea id="adminEditContent" rows="4" placeholder="Edit message..."></textarea>
      <small class="error-message" id="adminEditError"></small>
    </div>
    <div class="modal-buttons">
      <button class="btn-save" onclick="adminSaveEditMessage()">Save</button>
      <button class="btn-cancel" onclick="closeAdminEditModal()">Cancel</button>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="adminDeleteModal" class="modal">
  <div class="modal-content">
    <div class="validation-icon" style="margin: 0 auto 20px; width: 60px; height: 60px; border-radius: 50%; background: var(--red-light); display: flex; align-items: center; justify-content: center;">
      <svg width="30" height="30" fill="none" stroke="var(--red)" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
    </div>
    <h3>Delete Message</h3>
    <p>Are you sure you want to delete this message? This action cannot be undone.</p>
    <div class="modal-buttons">
      <button class="btn-delete" id="confirmDeleteBtn" style="background: var(--red);">Delete</button>
      <button class="btn-cancel" onclick="closeAdminDeleteModal()">Cancel</button>
    </div>
  </div>
</div>

<script>
let adminEditMessageId = null;
let adminDeleteMessageId = null;
let currentFilter = 'all';

function filterMessages(filter) {
    currentFilter = filter;
    const rows = document.querySelectorAll('#messagesTableBody tr:not(.no-results-row)');
    const buttons = document.querySelectorAll('.msg-filter-btn');
    let visibleCount = 0;

    // Update active button
    buttons.forEach(btn => btn.classList.remove('active'));
    document.getElementById('filter' + filter.charAt(0).toUpperCase() + filter.slice(1)).classList.add('active');

    // Remove old no-results row
    const oldNoResults = document.querySelector('.no-results-row');
    if (oldNoResults) oldNoResults.remove();

    // Filter rows
    rows.forEach(row => {
        const isRead = row.getAttribute('data-read') === '1';
        const isFlagged = row.getAttribute('data-flagged') === '1';
        let show = false;

        switch (filter) {
            case 'all':
                show = true;
                break;
            case 'unread':
                show = !isRead;
                break;
            case 'read':
                show = isRead;
                break;
            case 'flagged':
                show = isFlagged;
                break;
        }

        row.style.display = show ? '' : 'none';
        if (show) visibleCount++;
    });

    // Show "no results" if needed
    if (visibleCount === 0) {
        const tbody = document.getElementById('messagesTableBody');
        const labels = { all: 'messages', unread: 'unread messages', read: 'read messages', flagged: 'flagged messages' };
        const tr = document.createElement('tr');
        tr.className = 'no-results-row';
        tr.innerHTML = `<td colspan="8" style="text-align:center; padding:30px; color:var(--text-3); font-style:italic;">No ${labels[filter]} found.</td>`;
        tbody.appendChild(tr);
    }
}

function showAdminMessage(msg, isError = false) {
    const alertDiv = document.getElementById(isError ? 'errorAlert' : 'messageAlert');
    alertDiv.innerHTML = `<div class="alert ${isError ? 'alert-error' : 'alert-success'}">${isError ? '❌' : '✅'} ${msg}</div>`;
    setTimeout(() => {
        alertDiv.innerHTML = '';
    }, 3000);
}

function adminAddUser() {
    const userId = document.getElementById('adminUserId').value.trim();
    const name = document.getElementById('adminUserName').value.trim();
    const init = document.getElementById('adminUserInit').value.trim().toUpperCase();
    const avatar = document.getElementById('adminUserAvatar').value;
    const role = document.getElementById('adminUserRole').value;
    
    let isValid = true;
    
    if (!userId) {
        document.getElementById('adminUserIdError').textContent = 'User ID is required';
        isValid = false;
    } else {
        document.getElementById('adminUserIdError').textContent = '';
    }
    
    if (!name || name.length < 2) {
        document.getElementById('adminUserNameError').textContent = 'Name must be at least 2 characters';
        isValid = false;
    } else {
        document.getElementById('adminUserNameError').textContent = '';
    }
    
    if (!init || init.length < 1 || init.length > 5) {
        document.getElementById('adminUserInitError').textContent = 'Initials must be 1-5 characters';
        isValid = false;
    } else {
        document.getElementById('adminUserInitError').textContent = '';
    }
    
    if (!isValid) return;
    
    const formData = new FormData();
    formData.append('action', 'add_user');
    formData.append('user_id', userId);
    formData.append('name', name);
    formData.append('init', init);
    formData.append('avatar', avatar);
    formData.append('role', role);
    
    fetch(window.location.pathname, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAdminMessage('User added successfully!');
            setTimeout(() => location.reload(), 1500);
        } else {
            showAdminMessage(data.error || 'Error adding user', true);
        }
    });
}

function adminEditMessage(id, content) {
    adminEditMessageId = id;
    document.getElementById('adminEditContent').value = content;
    document.getElementById('adminEditModal').style.display = 'block';
    document.getElementById('adminEditError').textContent = '';
}

function adminSaveEditMessage() {
    const newContent = document.getElementById('adminEditContent').value.trim();
    const errorSpan = document.getElementById('adminEditError');
    
    if (!newContent || newContent.length === 0) {
        errorSpan.textContent = 'Message cannot be empty';
        return;
    }
    if (newContent.length > 5000) {
        errorSpan.textContent = 'Message cannot exceed 5000 characters';
        return;
    }
    errorSpan.textContent = '';
    
    const formData = new FormData();
    formData.append('action', 'edit_message_admin');
    formData.append('message_id', adminEditMessageId);
    formData.append('content', newContent);
    
    fetch(window.location.pathname, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeAdminEditModal();
            showAdminMessage('Message updated successfully!');
            setTimeout(() => location.reload(), 1000);
        } else {
            showAdminMessage(data.error || 'Error updating message', true);
        }
    });
}

function adminDeleteMessage(id) {
    adminDeleteMessageId = id;
    document.getElementById('adminDeleteModal').style.display = 'block';
}

function confirmAdminDelete() {
    const formData = new FormData();
    formData.append('action', 'delete_message_admin');
    formData.append('message_id', adminDeleteMessageId);
    
    fetch(window.location.pathname, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeAdminDeleteModal();
            showAdminMessage('Message deleted successfully!');
            setTimeout(() => location.reload(), 1000);
        } else {
            showAdminMessage('Error deleting message', true);
        }
    });
}

function adminUnflagMessage(id) {
    if (confirm('Are you sure you want to mark this message as safe?')) {
        const formData = new FormData();
        formData.append('action', 'unflag_message');
        formData.append('message_id', id);
        
        fetch(window.location.pathname, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(response => {
            if (!response.ok) throw new Error('HTTP ' + response.status);
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showAdminMessage('Message marked as safe!');
                setTimeout(() => location.reload(), 1000);
            } else {
                showAdminMessage(data.error || 'Error updating message', true);
            }
        })
        .catch(err => {
            console.error('Unflag error:', err);
            showAdminMessage('Network error: ' + err.message, true);
        });
    }
}

function closeAdminEditModal() {
    document.getElementById('adminEditModal').style.display = 'none';
    adminEditMessageId = null;
}

function closeAdminDeleteModal() {
    document.getElementById('adminDeleteModal').style.display = 'none';
    adminDeleteMessageId = null;
}

document.getElementById('confirmDeleteBtn').onclick = confirmAdminDelete;

window.onclick = function(event) {
    const editModal = document.getElementById('adminEditModal');
    const deleteModal = document.getElementById('adminDeleteModal');
    if (event.target == editModal) {
        closeAdminEditModal();
    }
    if (event.target == deleteModal) {
        closeAdminDeleteModal();
    }
}
</script>
</body>
</html>