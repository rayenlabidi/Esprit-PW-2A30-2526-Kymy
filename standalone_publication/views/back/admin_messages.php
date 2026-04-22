<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../controllers/MessageC.php';

$controller = new MessageC();
$messages = $controller->GetAllMessagesAdmin();
$users = $controller->GetAllUsers();
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
</head>
<body>

<nav class="wf-nav">
  <a href="../front/publications.php" class="wf-nav-logo">
    <div class="wf-nav-logo-box">
      <svg viewBox="0 0 16 16"><rect x="2" y="4" width="5" height="8" rx="1"/><rect x="9" y="4" width="5" height="8" rx="1"/><rect x="2" y="1" width="12" height="2" rx="1"/></svg>
    </div>
    Workify Admin
  </a>
  <div class="wf-nav-right">
    <div class="wf-avatar wf-avatar-36 av-blue">AD</div>
  </div>
</nav>

<div class="admin-container">
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
    <div class="admin-table-wrapper">
      <table class="admin-table" id="messagesTable">
        <thead>
          <tr><th>ID</th><th>From</th><th>To</th><th>Message</th><th>Read</th><th>Created</th><th>Actions</th></tr>
        </thead>
        <tbody id="messagesTableBody">
          <?php foreach($messages as $msg): ?>
          <tr>
            <td><?php echo $msg['id']; ?></td>
            <td>
              <div class="author-cell">
                <div class="wf-avatar wf-avatar-32 <?php echo $msg['sender_avatar']; ?>"><?php echo htmlspecialchars($msg['sender_init']); ?></div>
                <span><?php echo htmlspecialchars($msg['sender_name']); ?></span>
              </div>
             </div>
            <td>
              <div class="author-cell">
                <div class="wf-avatar wf-avatar-32 <?php echo $msg['receiver_avatar']; ?>"><?php echo htmlspecialchars($msg['receiver_init']); ?></div>
                <span><?php echo htmlspecialchars($msg['receiver_name']); ?></span>
              </div>
             </div>
            <td class="content-cell"><?php echo htmlspecialchars(substr($msg['content'], 0, 80)) . (strlen($msg['content']) > 80 ? '...' : ''); ?> </div>
            <td><?php echo $msg['is_read'] ? '✅ Yes' : '❌ No'; ?> </div>
            <td><?php echo date('M d, Y H:i', strtotime($msg['created_at'])); ?> </div>
            <td>
              <div class="action-buttons">
                <button class="btn-edit" onclick="adminEditMessage(<?php echo $msg['id']; ?>, '<?php echo htmlspecialchars(addslashes($msg['content'])); ?>')">✏️ Edit</button>
                <button class="btn-delete" onclick="adminDeleteMessage(<?php echo $msg['id']; ?>)">🗑️ Delete</button>
              </div>
             </div>
           </div>
          <?php endforeach; ?>
        </tbody>
       </div>
    </div>
  </div>
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
    
    fetch(window.location.href, {
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
    
    fetch(window.location.href, {
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
    
    fetch(window.location.href, {
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