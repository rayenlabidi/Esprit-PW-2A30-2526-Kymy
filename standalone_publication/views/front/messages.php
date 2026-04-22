<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../controllers/MessageC.php';

$current_user_id = 'current_user';
$current_user_name = 'You';
$current_user_init = 'YO';
$current_user_avatar = 'av-blue';

$controller = new MessageC();
$conversations = $controller->ListeConversations($current_user_id);
$unreadCount = $controller->GetUnreadCount($current_user_id);
$users = $controller->GetAllUsers();
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
    <a href="../back/admin_messages.php" class="wf-nav-icon-btn" style="text-decoration:none;">
      <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
    </a>
    <div class="wf-avatar wf-avatar-36 av-blue" style="cursor:pointer;"><?php echo $current_user_init; ?></div>
  </div>
</nav>

<div class="msg-shell">
  <aside class="msg-panel-left" id="panelLeft">
    <div class="msg-panel-header">
      <div class="msg-panel-header-top">
        <h2>Messages</h2>
        <button class="msg-new-btn" onclick="openNewMessageModal()" title="New message">+</button>
      </div>
      <div class="msg-search-wrap">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input type="text" class="msg-search-input" id="convSearchInput" placeholder="Search conversations…" oninput="filterConversations(this.value)">
      </div>
    </div>
    <div class="msg-conv-list" id="convList"></div>
  </aside>

  <section class="msg-panel-right" id="panelRight">
    <div class="msg-chat-header" id="chatHeader" style="display:none;"></div>
    <div class="msg-chat-body" id="chatBody">
      <div class="msg-empty-state" id="emptyState">
        <div class="msg-empty-icon">
          <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        </div>
        <p>Select a conversation to start messaging</p>
      </div>
    </div>
    <div class="msg-input-bar" id="inputBar" style="display:none;">
      <div class="msg-input-row">
        <div class="msg-input-field-wrap">
          <textarea class="msg-textarea" id="msgTextarea" rows="1" placeholder="Write a message…" onkeydown="handleSendKey(event)" oninput="autoResizeTA(this)"></textarea>
        </div>
        <button class="msg-send-btn" onclick="sendMessage()">
          <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
        </button>
      </div>
    </div>
  </section>
</div>

<!-- Validation Modal -->
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
    <div class="validation-errors" id="validationErrors">
      <ul id="errorList"></ul>
    </div>
    <button onclick="closeValidationModal()">OK</button>
  </div>
</div>

<!-- New Message Modal -->
<div id="newMessageModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeNewMessageModal()">&times;</span>
    <h3>New Conversation</h3>
    <div class="form-group">
      <label>Select User:</label>
      <select id="receiverSelect">
        <option value="">-- Select a user --</option>
        <?php foreach($users as $user): ?>
          <option value="<?php echo $user['user_id']; ?>" data-name="<?php echo $user['name']; ?>" data-init="<?php echo $user['init']; ?>" data-avatar="<?php echo $user['avatar']; ?>">
            <?php echo $user['name']; ?> (<?php echo $user['role']; ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label>Message:</label>
      <textarea id="newMessageContent" rows="4" placeholder="Type your message..."></textarea>
    </div>
    <div class="modal-buttons">
      <button class="btn-save" onclick="sendNewMessage()">Send</button>
      <button class="btn-cancel" onclick="closeNewMessageModal()">Cancel</button>
    </div>
  </div>
</div>

<!-- Edit Message Modal -->
<div id="editMessageModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeEditMessageModal()">&times;</span>
    <h3>Edit Message</h3>
    <div class="form-group">
      <textarea id="editMessageContent" rows="4" placeholder="Edit your message..."></textarea>
    </div>
    <div class="modal-buttons">
      <button class="btn-save" onclick="saveEditMessage()">Save</button>
      <button class="btn-cancel" onclick="closeEditMessageModal()">Cancel</button>
    </div>
  </div>
</div>

<!-- Delete Conversation Modal -->
<div id="deleteConvModal" class="delete-modal">
  <div class="delete-modal-content">
    <div class="delete-icon">
      <svg width="30" height="30" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
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
const CURRENT_USER_ID = '<?php echo $current_user_id; ?>';
const CURRENT_USER_NAME = '<?php echo $current_user_name; ?>';
const CURRENT_USER_INIT = '<?php echo $current_user_init; ?>';
const CURRENT_USER_AVATAR = '<?php echo $current_user_avatar; ?>';
const BASE_URL = window.location.href;

let activeConvId = null;
let activeConvName = '';
let activeConvInit = '';
let activeConvAvatar = '';
let conversations = [];
let currentEditMessageId = null;

function showValidationModal(errors) {
    const errorList = document.getElementById('errorList');
    errorList.innerHTML = '';
    if (Array.isArray(errors)) {
        errors.forEach(error => {
            const li = document.createElement('li');
            li.textContent = error;
            errorList.appendChild(li);
        });
    } else {
        const li = document.createElement('li');
        li.textContent = errors;
        errorList.appendChild(li);
    }
    document.getElementById('validationModal').style.display = 'block';
}

function closeValidationModal() {
    document.getElementById('validationModal').style.display = 'none';
}

function validateMessage(content) {
    const errors = [];
    if (!content || content.trim().length === 0) {
        errors.push('Message cannot be empty');
    }
    if (content && content.trim().length > 5000) {
        errors.push('Message cannot exceed 5000 characters');
    }
    return errors;
}

function loadConversations() {
    const formData = new FormData();
    formData.append('action', 'get_conversations');
    formData.append('user_id', CURRENT_USER_ID);
    
    fetch(BASE_URL, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            conversations = data.conversations;
            renderConversations();
        }
    });
}

function renderConversations(filter = '') {
    const list = document.getElementById('convList');
    const q = filter.toLowerCase();
    
    const filtered = conversations.filter(c => 
        !q || c.other_user_name.toLowerCase().includes(q)
    );
    
    list.innerHTML = filtered.map(c => `
        <div class="msg-conv-item${activeConvId === c.other_user_id ? ' active' : ''}" onclick="openConversation('${c.other_user_id}', '${escapeHtml(c.other_user_name)}', '${escapeHtml(c.other_user_init)}', '${escapeHtml(c.other_user_avatar)}')">
            <div class="msg-conv-avatar-wrap">
                <div class="wf-avatar wf-avatar-40 ${c.other_user_avatar}">${escapeHtml(c.other_user_init)}</div>
            </div>
            <div class="msg-conv-body">
                <div class="msg-conv-row1">
                    <span class="msg-conv-name">${escapeHtml(c.other_user_name)}</span>
                    <span class="msg-conv-time">${formatTime(c.last_time)}</span>
                </div>
                <div class="msg-conv-row2">
                    <span class="msg-conv-preview${c.unread_count > 0 ? ' unread' : ''}">${escapeHtml(c.last_message ? c.last_message.substring(0, 50) : 'No messages')}</span>
                    ${c.unread_count > 0 ? `<span class="msg-unread-badge">${c.unread_count}</span>` : ''}
                </div>
            </div>
        </div>
    `).join('');
    
    updateNavBadge();
}

function openConversation(otherUserId, name, init, avatar) {
    activeConvId = otherUserId;
    activeConvName = name;
    activeConvInit = init;
    activeConvAvatar = avatar;
    
    renderConversations(document.getElementById('convSearchInput').value);
    
    document.getElementById('chatHeader').style.display = 'flex';
    document.getElementById('chatHeader').innerHTML = `
        <div class="msg-chat-header-left">
            <div class="wf-avatar wf-avatar-40 ${avatar}">${escapeHtml(init)}</div>
            <div class="msg-chat-info">
                <div class="msg-chat-name">${escapeHtml(name)}</div>
            </div>
        </div>
        <div class="msg-chat-header-right">
            <button class="msg-header-btn" onclick="openDeleteConvModal()">🗑️ Delete Conversation</button>
        </div>
    `;
    
    document.getElementById('emptyState').style.display = 'none';
    document.getElementById('inputBar').style.display = 'block';
    document.getElementById('msgTextarea').focus();
    
    loadMessages(otherUserId);
}

function loadMessages(otherUserId) {
    const formData = new FormData();
    formData.append('action', 'get_messages');
    formData.append('user_id', CURRENT_USER_ID);
    formData.append('other_user_id', otherUserId);
    
    fetch(BASE_URL, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.messages) {
            renderMessages(data.messages);
            loadConversations();
        }
    });
}

function renderMessages(messages) {
    const body = document.getElementById('chatBody');
    body.innerHTML = '';
    
    messages.forEach(msg => {
        const isMine = msg.sender_id === CURRENT_USER_ID;
        const row = document.createElement('div');
        row.className = 'msg-row ' + (isMine ? 'mine' : 'their');
        row.setAttribute('data-message-id', msg.id);
        
        const bubbleContent = `<div class="msg-bubble" ondblclick="openEditMessageModal(${msg.id}, '${escapeHtml(msg.content)}', ${isMine})">${escapeHtml(msg.content)}</div>`;
        const timeHtml = `<div class="msg-bubble-time">${formatTime(msg.created_at)}</div>`;
        
        if (isMine) {
            row.innerHTML = `
                <div class="msg-bubble-stack">
                    ${bubbleContent}
                    ${timeHtml}
                    <button class="msg-delete-btn" onclick="deleteMessage(${msg.id})">Delete</button>
                </div>`;
        } else {
            row.innerHTML = `
                <div class="wf-avatar wf-avatar-32 ${activeConvAvatar}">${escapeHtml(activeConvInit)}</div>
                <div class="msg-bubble-stack">
                    ${bubbleContent}
                    ${timeHtml}
                </div>`;
        }
        
        body.appendChild(row);
    });
    
    body.scrollTop = body.scrollHeight;
}

function sendMessage() {
    const textarea = document.getElementById('msgTextarea');
    const content = textarea.value.trim();
    
    const errors = validateMessage(content);
    if (errors.length > 0) {
        showValidationModal(errors);
        return;
    }
    if (!activeConvId) return;
    
    const formData = new FormData();
    formData.append('action', 'send_message');
    formData.append('sender_id', CURRENT_USER_ID);
    formData.append('receiver_id', activeConvId);
    formData.append('sender_name', CURRENT_USER_NAME);
    formData.append('receiver_name', activeConvName);
    formData.append('sender_init', CURRENT_USER_INIT);
    formData.append('receiver_init', activeConvInit);
    formData.append('sender_avatar', CURRENT_USER_AVATAR);
    formData.append('receiver_avatar', activeConvAvatar);
    formData.append('content', content);
    
    fetch(BASE_URL, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            textarea.value = '';
            textarea.style.height = 'auto';
            loadMessages(activeConvId);
        } else {
            showValidationModal([data.error || 'Error sending message']);
        }
    });
}

function openEditMessageModal(messageId, currentText, isMine) {
    if (!isMine) {
        showValidationModal(['You can only edit your own messages']);
        return;
    }
    currentEditMessageId = messageId;
    document.getElementById('editMessageContent').value = currentText;
    document.getElementById('editMessageModal').style.display = 'block';
}

function saveEditMessage() {
    const newContent = document.getElementById('editMessageContent').value.trim();
    const errors = validateMessage(newContent);
    
    if (errors.length > 0) {
        showValidationModal(errors);
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'edit_message');
    formData.append('message_id', currentEditMessageId);
    formData.append('content', newContent);
    formData.append('sender_id', CURRENT_USER_ID);
    
    fetch(BASE_URL, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeEditMessageModal();
            loadMessages(activeConvId);
        } else {
            showValidationModal([data.error || 'Error editing message']);
        }
    });
}

function deleteMessage(messageId) {
    const formData = new FormData();
    formData.append('action', 'delete_message');
    formData.append('message_id', messageId);
    formData.append('sender_id', CURRENT_USER_ID);
    
    fetch(BASE_URL, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadMessages(activeConvId);
        } else {
            showValidationModal(['Error deleting message']);
        }
    });
}

function openDeleteConvModal() {
    document.getElementById('deleteConvModal').style.display = 'block';
}

function closeDeleteConvModal() {
    document.getElementById('deleteConvModal').style.display = 'none';
}

function confirmDeleteConversation() {
    const formData = new FormData();
    formData.append('action', 'delete_conversation');
    formData.append('user_id', CURRENT_USER_ID);
    formData.append('other_user_id', activeConvId);
    
    fetch(BASE_URL, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeDeleteConvModal();
            activeConvId = null;
            document.getElementById('chatHeader').style.display = 'none';
            document.getElementById('inputBar').style.display = 'none';
            document.getElementById('emptyState').style.display = 'flex';
            loadConversations();
        } else {
            showValidationModal(['Error deleting conversation']);
        }
    });
}

function openNewMessageModal() {
    document.getElementById('newMessageModal').style.display = 'block';
    document.getElementById('receiverSelect').value = '';
    document.getElementById('newMessageContent').value = '';
}

function closeNewMessageModal() {
    document.getElementById('newMessageModal').style.display = 'none';
}

function sendNewMessage() {
    const receiverSelect = document.getElementById('receiverSelect');
    const receiverId = receiverSelect.value;
    const selectedOption = receiverSelect.options[receiverSelect.selectedIndex];
    const receiverName = selectedOption.getAttribute('data-name');
    const receiverInit = selectedOption.getAttribute('data-init');
    const receiverAvatar = selectedOption.getAttribute('data-avatar');
    const content = document.getElementById('newMessageContent').value.trim();
    
    const errors = [];
    if (!receiverId) {
        errors.push('Please select a user');
    }
    if (!content || content.length === 0) {
        errors.push('Message cannot be empty');
    }
    if (content && content.length > 5000) {
        errors.push('Message cannot exceed 5000 characters');
    }
    
    if (errors.length > 0) {
        showValidationModal(errors);
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'send_message');
    formData.append('sender_id', CURRENT_USER_ID);
    formData.append('receiver_id', receiverId);
    formData.append('sender_name', CURRENT_USER_NAME);
    formData.append('receiver_name', receiverName);
    formData.append('sender_init', CURRENT_USER_INIT);
    formData.append('receiver_init', receiverInit);
    formData.append('sender_avatar', CURRENT_USER_AVATAR);
    formData.append('receiver_avatar', receiverAvatar);
    formData.append('content', content);
    
    fetch(BASE_URL, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeNewMessageModal();
            loadConversations();
            setTimeout(() => {
                openConversation(receiverId, receiverName, receiverInit, receiverAvatar);
            }, 500);
        } else {
            showValidationModal([data.error || 'Error sending message']);
        }
    });
}

function closeEditMessageModal() {
    document.getElementById('editMessageModal').style.display = 'none';
    currentEditMessageId = null;
}

function filterConversations(q) {
    renderConversations(q);
}

function handleSendKey(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
}

function autoResizeTA(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 120) + 'px';
}

function updateNavBadge() {
    const totalUnread = conversations.reduce((sum, c) => sum + (c.unread_count || 0), 0);
    const badge = document.getElementById('navMsgBadge');
    if (badge) {
        if (totalUnread > 0) {
            badge.textContent = totalUnread;
            badge.style.display = '';
        } else {
            badge.style.display = 'none';
        }
    }
}

function formatTime(timestamp) {
    if (!timestamp) return '';
    const date = new Date(timestamp);
    const now = new Date();
    const diff = now - date;
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days = Math.floor(diff / 86400000);
    
    if (minutes < 1) return 'Just now';
    if (minutes < 60) return minutes + 'm';
    if (hours < 24) return hours + 'h';
    if (days < 7) return days + 'd';
    return date.toLocaleDateString();
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

window.onclick = function(event) {
    const modals = ['newMessageModal', 'editMessageModal', 'deleteConvModal', 'validationModal'];
    modals.forEach(id => {
        const modal = document.getElementById(id);
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    loadConversations();
});
</script>
</body>
</html>