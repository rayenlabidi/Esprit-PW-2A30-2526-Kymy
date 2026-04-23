<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../controllers/MessageC.php';

$current_user_id     = 'current_user';
$current_user_name   = 'You';
$current_user_init   = 'YO';
$current_user_avatar = 'av-blue';

$controller    = new MessageC();
$conversations = $controller->ListeConversations($current_user_id);
$unreadCount   = $controller->GetUnreadCount($current_user_id);
$users         = $controller->GetAllUsers();

// Get post_id from URL if present
$post_id_from_url = isset($_GET['post_id']) ? (int)$_GET['post_id'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Messages – Workify</title>
<link rel="stylesheet" href="../../public/css/workify-tokens.css">
<link rel="stylesheet" href="../../public/css/messages.css">
</head>
<body>

<nav class="wf-nav">
  <a href="#" class="wf-nav-logo">
    <div class="wf-nav-logo-box">
      <svg viewBox="0 0 16 16"><rect x="2" y="4" width="5" height="8" rx="1"/><rect x="9" y="4" width="5" height="8" rx="1"/><rect x="2" y="1" width="12" height="2" rx="1"/></svg>
    </div>
    Workify
  </a>
  <ul class="wf-nav-links">
    <li><a href="#">Home</a></li>
    <li><a href="#">Browse jobs</a></li>
    <li><a href="publications.php">Publications</a></li>
    <li><a href="#" class="active">Messages</a></li>
  </ul>
  <div class="wf-nav-right">
    <a href="../back/admin_messages.php" class="wf-nav-icon-btn" style="text-decoration:none;" title="Admin">
      <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
      </svg>
    </a>
    <div class="wf-avatar wf-avatar-36 av-blue" style="cursor:pointer;"><?php echo $current_user_init; ?></div>
  </div>
</nav>

<div class="msg-shell">
  <!-- LEFT PANEL -->
  <aside class="msg-panel-left" id="panelLeft">
    <div class="msg-panel-header">
      <div class="msg-panel-header-top">
        <h2>Messages
          <?php if($unreadCount > 0): ?>
            <span id="navMsgBadge" class="msg-unread-badge" style="margin-left:6px;"><?php echo $unreadCount; ?></span>
          <?php else: ?>
            <span id="navMsgBadge" class="msg-unread-badge" style="display:none;margin-left:6px;"></span>
          <?php endif; ?>
        </h2>
        <button class="msg-new-btn" onclick="openNewMessageModal()" title="New conversation">+</button>
      </div>
      <div class="msg-search-wrap">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
        </svg>
        <input type="text" class="msg-search-input" id="convSearchInput"
               placeholder="Search conversations…" oninput="filterConversations(this.value)">
      </div>
    </div>
    <div class="msg-conv-list" id="convList">
      <div style="padding:20px;text-align:center;color:var(--text-4);font-size:13px;">Loading…</div>
    </div>
  </aside>

  <!-- RIGHT PANEL -->
  <section class="msg-panel-right" id="panelRight">
    <div class="msg-chat-header" id="chatHeader" style="display:none;"></div>
    <div class="msg-chat-body" id="chatBody">
      <div class="msg-empty-state" id="emptyState">
        <div class="msg-empty-icon">
          <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
          </svg>
        </div>
        <p>Select a conversation to start messaging</p>
      </div>
    </div>
    <div class="msg-input-bar" id="inputBar" style="display:none;">
      <div class="msg-input-row">
        <div class="msg-input-field-wrap">
          <textarea class="msg-textarea" id="msgTextarea" rows="1"
                    placeholder="Write a message…"
                    onkeydown="handleSendKey(event)"
                    oninput="autoResizeTA(this)"></textarea>
        </div>
        <button class="msg-send-btn" onclick="sendMessage()">
          <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
          </svg>
        </button>
      </div>
    </div>
  </section>
</div>

<!-- MODALS -->
<div id="validationModal" class="validation-modal">
  <div class="validation-modal-content">
    <div class="validation-icon">
      <svg width="30" height="30" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
    </div>
    <h3>Validation Error</h3>
    <div class="validation-errors">
      <ul id="validationErrorList"></ul>
    </div>
    <button onclick="closeValidationModal()">OK</button>
  </div>
</div>

<div id="confirmModal" class="validation-modal">
  <div class="validation-modal-content">
    <div class="validation-icon" style="background:var(--red-light);">
      <svg width="30" height="30" fill="none" stroke="var(--red)" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
    </div>
    <h3>Are you sure?</h3>
    <p id="confirmMessage" style="color:var(--text-2);margin-bottom:20px;line-height:1.6;"></p>
    <div class="modal-buttons" style="justify-content:center;">
      <button id="confirmOkBtn" style="padding:10px 24px;background:var(--red);color:white;border:none;border-radius:var(--radius-md);font-weight:600;cursor:pointer;">Confirm</button>
      <button onclick="closeConfirmModal()" style="padding:10px 24px;background:var(--bg);color:var(--text-2);border:1px solid var(--border);border-radius:var(--radius-md);font-weight:600;cursor:pointer;">Cancel</button>
    </div>
  </div>
</div>

<div id="newMessageModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeNewMessageModal()">&times;</span>
    <h3>New Conversation</h3>
    <div class="form-group">
      <label>Select User:</label>
      <select id="receiverSelect">
        <option value="">-- Select a user --</option>
        <?php foreach($users as $user): ?>
          <option value="<?php echo htmlspecialchars($user['user_id']); ?>"
                  data-name="<?php echo htmlspecialchars($user['name']); ?>"
                  data-init="<?php echo htmlspecialchars($user['init']); ?>"
                  data-avatar="<?php echo htmlspecialchars($user['avatar']); ?>">
            <?php echo htmlspecialchars($user['name']); ?> (<?php echo htmlspecialchars($user['role']); ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label>Message:</label>
      <textarea id="newMessageContent" rows="4" placeholder="Type your message…"></textarea>
    </div>
    <div class="modal-buttons">
      <button class="btn-save" onclick="sendNewMessage()">Send</button>
      <button class="btn-cancel" onclick="closeNewMessageModal()">Cancel</button>
    </div>
  </div>
</div>

<div id="editMessageModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeEditMessageModal()">&times;</span>
    <h3>Edit Message</h3>
    <div class="form-group">
      <textarea id="editMessageContent" rows="4" placeholder="Edit your message…"></textarea>
    </div>
    <div class="modal-buttons">
      <button class="btn-save" onclick="saveEditMessage()">Save</button>
      <button class="btn-cancel" onclick="closeEditMessageModal()">Cancel</button>
    </div>
  </div>
</div>

<div id="deleteConvModal" class="delete-modal">
  <div class="delete-modal-content">
    <div class="delete-icon">
      <svg width="30" height="30" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <polyline points="3 6 5 6 21 6"/>
        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
        <path d="M10 11v6"/><path d="M14 11v6"/>
        <path d="M9 6V4h6v2"/>
      </svg>
    </div>
    <h3>Delete Conversation</h3>
    <p>Are you sure you want to delete this entire conversation? This action cannot be undone.</p>
    <div class="modal-buttons">
      <button class="delete-btn" onclick="confirmDeleteConversation()">Delete</button>
      <button class="cancel-btn" onclick="closeDeleteConvModal()">Cancel</button>
    </div>
  </div>
</div>

<script>
const CURRENT_USER_ID     = '<?php echo $current_user_id; ?>';
const CURRENT_USER_NAME   = '<?php echo $current_user_name; ?>';
const CURRENT_USER_INIT   = '<?php echo $current_user_init; ?>';
const CURRENT_USER_AVATAR = '<?php echo $current_user_avatar; ?>';
const MSG_URL             = '<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>';
const POST_ID_FROM_URL    = <?php echo $post_id_from_url ? $post_id_from_url : 'null'; ?>;
</script>
<script src="../../public/js/messages.js"></script>
</body>
</html>