/* ============================================================
   messages.js  –  Workify Messaging with Read System
   ============================================================ */

'use strict';

let activeConvId          = null;
let activeConvName        = '';
let activeConvInit        = '';
let activeConvAvatar      = '';
let conversations         = [];
let currentEditMessageId  = null;
let pollTimer             = null;
let pendingPublicationId  = POST_ID_FROM_URL;

const BAD_WORDS = ['badword1', 'badword2', 'spam', 'scam', 'abuse'];

function handleInputBadWords(ta) {
  const content = ta.value.toLowerCase();
  let foundWord = null;
  for (let word of BAD_WORDS) {
    if (content.includes(word)) {
      foundWord = word;
      break;
    }
  }
  
  let warningDiv = document.getElementById('badWordWarning');
  if (foundWord) {
    if (!warningDiv) {
      warningDiv = document.createElement('div');
      warningDiv.id = 'badWordWarning';
      warningDiv.style.color = 'var(--red)';
      warningDiv.style.fontSize = '12px';
      warningDiv.style.marginBottom = '4px';
      ta.parentNode.insertBefore(warningDiv, ta);
    }
    warningDiv.innerHTML = `Warning: The word "<span style="color:red; font-weight:bold;">${foundWord}</span>" is not allowed. Your message will be flagged.`;
    ta.style.borderColor = 'var(--red)';
    ta.style.backgroundColor = '#fff0f0';
  } else {
    if (warningDiv) {
      warningDiv.remove();
    }
    ta.style.borderColor = '';
    ta.style.backgroundColor = '';
  }
}

function showValidationModal(errors) {
  const modal = document.getElementById('validationModal');
  const list  = document.getElementById('validationErrorList');
  if (!modal || !list) return;
  list.innerHTML = '';
  (Array.isArray(errors) ? errors : [errors]).forEach(msg => {
    const li = document.createElement('li');
    li.textContent = msg;
    list.appendChild(li);
  });
  modal.style.display = 'block';
}

function closeValidationModal() {
  const m = document.getElementById('validationModal');
  if (m) m.style.display = 'none';
}

function showConfirmModal(message, onConfirm) {
  const modal = document.getElementById('confirmModal');
  const msg   = document.getElementById('confirmMessage');
  const btn   = document.getElementById('confirmOkBtn');
  if (!modal) { onConfirm(); return; }
  msg.textContent = message;
  modal.style.display = 'block';
  btn.onclick = function () { closeConfirmModal(); onConfirm(); };
}

function closeConfirmModal() {
  const m = document.getElementById('confirmModal');
  if (m) m.style.display = 'none';
}

function validateMessage(content) {
  const errors = [];
  if (!content || content.trim().length === 0) errors.push('Message cannot be empty');
  if (content && content.trim().length > 5000)  errors.push('Message cannot exceed 5000 characters');
  return errors;
}

function loadConversations(callback) {
  const fd = new FormData();
  fd.append('action',  'get_conversations');
  fd.append('user_id', CURRENT_USER_ID);
  fetch(MSG_URL, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        conversations = data.conversations || [];
        renderConversations();
        updateNavBadge();
        if (callback) callback();
      }
    })
    .catch(console.error);
}

function renderConversations(filter) {
  const list = document.getElementById('convList');
  if (!list) return;
  const q = (filter !== undefined ? filter : (document.getElementById('convSearchInput') ? document.getElementById('convSearchInput').value : '')).toLowerCase();
  const filtered = conversations.filter(c => !q || (c.other_user_name && c.other_user_name.toLowerCase().includes(q)));
  if (filtered.length === 0) {
    list.innerHTML = '<div style="padding:20px;text-align:center;color:var(--text-4);font-size:13px;">No conversations yet</div>';
    return;
  }
  list.innerHTML = filtered.map(c => `
    <div class="msg-conv-item${activeConvId === c.other_user_id ? ' active' : ''}"
         onclick="openConversation('${c.other_user_id}','${escHtml(c.other_user_name || '')}','${escHtml(c.other_user_init || '')}','${escHtml(c.other_user_avatar || 'av-blue')}')">
      <div class="msg-conv-avatar-wrap">
        <div class="wf-avatar wf-avatar-40 ${c.other_user_avatar || 'av-blue'}">${escHtml(c.other_user_init || '?')}</div>
        ${parseInt(c.unread_count) > 0 ? '<span class="badge">' + c.unread_count + '</span>' : ''}
      </div>
      <div class="msg-conv-body">
        <div class="msg-conv-row1">
          <span class="msg-conv-name">${escHtml(c.other_user_name || 'Unknown')}</span>
          <span class="msg-conv-time">${formatTime(c.last_time)}</span>
        </div>
        <div class="msg-conv-row2">
          <span class="msg-conv-preview${parseInt(c.unread_count) > 0 ? ' unread' : ''}">${escHtml((c.last_message || 'No messages').substring(0, 50))}</span>
          ${parseInt(c.unread_count) > 0 ? '<span class="msg-unread-badge">' + c.unread_count + '</span>' : ''}
        </div>
      </div>
    </div>
  `).join('');
}

function filterConversations(q) {
  renderConversations(q);
}

function openConversation(otherUserId, name, init, avatar) {
  if (activeConvId === otherUserId) return;
  activeConvId     = otherUserId;
  activeConvName   = name;
  activeConvInit   = init;
  activeConvAvatar = avatar;
  renderConversations();
  const header = document.getElementById('chatHeader');
  header.style.display = 'flex';
  header.innerHTML = `
    <div class="msg-chat-header-left">
      <div class="wf-avatar wf-avatar-40 ${avatar}">${escHtml(init)}</div>
      <div>
        <div class="msg-chat-name">${escHtml(name)}</div>
        <div style="font-size:11px; color:var(--text-4);" id="readStatus">Online</div>
      </div>
    </div>
    <div>
      <button class="msg-header-action-btn" onclick="openDeleteConvModal()" title="Delete conversation">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <polyline points="3 6 5 6 21 6"/>
          <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
          <path d="M10 11v6"/><path d="M14 11v6"/>
          <path d="M9 6V4h6v2"/>
        </svg>
      </button>
    </div>`;
  document.getElementById('emptyState').style.display = 'none';
  document.getElementById('inputBar').style.display   = 'block';
  document.getElementById('msgTextarea').focus();
  loadMessages(otherUserId);
  if (pollTimer) clearInterval(pollTimer);
  pollTimer = setInterval(() => {
    if (activeConvId) loadMessages(activeConvId, true);
  }, 5000);
}

function loadMessages(otherUserId, silent) {
  if (!otherUserId) return;
  const fd = new FormData();
  fd.append('action',        'get_messages');
  fd.append('user_id',       CURRENT_USER_ID);
  fd.append('other_user_id', otherUserId);
  fetch(MSG_URL, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd })
    .then(r => r.json())
    .then(data => {
      if (activeConvId === otherUserId) {
        if (data.success && data.messages) {
          renderMessages(data.messages);
          if (!silent) {
            loadConversations();
            updateReadStatus(data.messages);
          }
        } else {
          renderMessages([]);
        }
      }
    })
    .catch(error => {
      console.error('Load messages error:', error);
      if (activeConvId === otherUserId) renderMessages([]);
    });
}

// Update read status indicator
function updateReadStatus(messages) {
  const statusEl = document.getElementById('readStatus');
  if (!statusEl) return;
  
  // Find last message sent by current user that hasn't been read yet
  const lastSentMessage = [...messages].reverse().find(msg => msg.sender_id === CURRENT_USER_ID);
  if (lastSentMessage) {
    if (lastSentMessage.is_read == 1) {
      statusEl.innerHTML = '✓✓ Read';
      statusEl.style.color = 'var(--green)';
    } else {
      statusEl.innerHTML = '✓ Sent';
      statusEl.style.color = 'var(--text-4)';
    }
  } else {
    statusEl.innerHTML = 'Online';
    statusEl.style.color = 'var(--text-4)';
  }
}

function renderMessages(messages) {
  const body = document.getElementById('chatBody');
  if (!body) return;
  const wasAtBottom = body.scrollHeight - body.scrollTop - body.clientHeight < 100;
  body.innerHTML = '';
  if (!messages || messages.length === 0) {
    body.innerHTML = '<div style="flex:1;display:flex;align-items:center;justify-content:center;color:var(--text-4);font-size:13px;">No messages yet – say hello!</div>';
    return;
  }
  messages.forEach(msg => {
    const isMine = msg.sender_id === CURRENT_USER_ID;
    const isRead = msg.is_read == 1;
    const readIndicator = (isMine && isRead) ? '<span style="font-size:10px; margin-left:6px; color:var(--green);">✓✓</span>' : 
                          (isMine && !isRead) ? '<span style="font-size:10px; margin-left:6px; color:var(--text-4);">✓</span>' : '';
    const row    = document.createElement('div');
    row.className = 'msg-row ' + (isMine ? 'mine' : 'their');
    row.dataset.messageId = msg.id;
    const editAttr = isMine ? `ondblclick="openEditMessageModal(${msg.id}, '${escAttr(msg.content)}')"` : '';
    const bubble   = `<div class="msg-bubble" ${editAttr} title="${isMine ? 'Double-click to edit' : ''}">${escHtml(msg.content)}${readIndicator}</div>`;
    const time     = `<div class="msg-bubble-time">${formatTime(msg.created_at)}</div>`;
    if (isMine) {
      row.innerHTML = `<div class="msg-bubble-stack">${bubble}${time}<button class="msg-delete-btn" onclick="confirmDeleteMessage(${msg.id})">Delete</button></div>`;
    } else {
      row.innerHTML = `<div class="wf-avatar wf-avatar-32 ${activeConvAvatar}">${escHtml(activeConvInit)}</div><div class="msg-bubble-stack">${bubble}${time}</div>`;
    }
    body.appendChild(row);
  });
  if (wasAtBottom) {
    setTimeout(() => { body.scrollTop = body.scrollHeight; }, 50);
  }
}

function sendMessage() {
  const ta      = document.getElementById('msgTextarea');
  const content = ta.value.trim();
  const errors  = validateMessage(content);
  if (errors.length) { showValidationModal(errors); return; }
  if (!activeConvId) { showValidationModal(['Please select a conversation first']); return; }
  _doSendMessage(CURRENT_USER_ID, activeConvId, CURRENT_USER_NAME, activeConvName,
    CURRENT_USER_INIT, activeConvInit, CURRENT_USER_AVATAR, activeConvAvatar, content,
    function () { 
      ta.value = ''; 
      ta.style.height = 'auto'; 
      handleInputBadWords(ta);
      loadMessages(activeConvId);
      loadConversations();
    });
}

function _doSendMessage(senderId, receiverId, senderName, receiverName,
                        senderInit, receiverInit, senderAvatar, receiverAvatar, content, onSuccess) {
  const fd = new FormData();
  fd.append('action',          'send_message');
  fd.append('sender_id',       senderId);
  fd.append('receiver_id',     receiverId);
  fd.append('sender_name',     senderName);
  fd.append('receiver_name',   receiverName);
  fd.append('sender_init',     senderInit);
  fd.append('receiver_init',   receiverInit);
  fd.append('sender_avatar',   senderAvatar);
  fd.append('receiver_avatar', receiverAvatar);
  fd.append('content',         content);
  if (pendingPublicationId) {
    fd.append('publication_id', pendingPublicationId);
  }
  fetch(MSG_URL, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd })
    .then(r => r.json())
    .then(data => {
      if (data.success) { 
        if (onSuccess) onSuccess();
        pendingPublicationId = null;
      }
      else showValidationModal([data.error || 'Error sending message']);
    })
    .catch(function () { showValidationModal(['Network error while sending message']); });
}

function handleSendKey(e) {
  if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
}

function autoResizeTA(el) {
  el.style.height = 'auto';
  el.style.height = Math.min(el.scrollHeight, 120) + 'px';
  handleInputBadWords(el);
}

function openEditMessageModal(messageId, currentText) {
  currentEditMessageId = messageId;
  const ta = document.getElementById('editMessageContent');
  if (ta) ta.value = currentText;
  document.getElementById('editMessageModal').style.display = 'block';
}

function saveEditMessage() {
  const ta      = document.getElementById('editMessageContent');
  const content = ta ? ta.value.trim() : '';
  const errors  = validateMessage(content);
  if (errors.length) { showValidationModal(errors); return; }
  const fd = new FormData();
  fd.append('action',     'edit_message');
  fd.append('message_id', currentEditMessageId);
  fd.append('content',    content);
  fd.append('sender_id',  CURRENT_USER_ID);
  fetch(MSG_URL, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd })
    .then(r => r.json())
    .then(data => {
      if (data.success) { closeEditMessageModal(); loadMessages(activeConvId); loadConversations(); }
      else showValidationModal([data.error || 'Error editing message']);
    })
    .catch(function () { showValidationModal(['Network error']); });
}

function closeEditMessageModal() {
  document.getElementById('editMessageModal').style.display = 'none';
  currentEditMessageId = null;
}

function confirmDeleteMessage(messageId) {
  showConfirmModal('Delete this message?', function () { deleteMessage(messageId); });
}

function deleteMessage(messageId) {
  const fd = new FormData();
  fd.append('action',     'delete_message');
  fd.append('message_id', messageId);
  fd.append('sender_id',  CURRENT_USER_ID);
  fetch(MSG_URL, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd })
    .then(r => r.json())
    .then(data => {
      if (data.success) { loadMessages(activeConvId); loadConversations(); }
      else showValidationModal(['Error deleting message']);
    })
    .catch(function () { showValidationModal(['Network error']); });
}

function openDeleteConvModal() {
  document.getElementById('deleteConvModal').style.display = 'block';
}

function closeDeleteConvModal() {
  document.getElementById('deleteConvModal').style.display = 'none';
}

function confirmDeleteConversation() {
  const fd = new FormData();
  fd.append('action',        'delete_conversation');
  fd.append('user_id',       CURRENT_USER_ID);
  fd.append('other_user_id', activeConvId);
  fetch(MSG_URL, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        closeDeleteConvModal();
        activeConvId = null;
        if (pollTimer) clearInterval(pollTimer);
        document.getElementById('chatHeader').style.display  = 'none';
        document.getElementById('inputBar').style.display    = 'none';
        document.getElementById('chatBody').innerHTML        = '';
        document.getElementById('emptyState').style.display  = 'flex';
        loadConversations();
      } else {
        showValidationModal(['Error deleting conversation']);
      }
    })
    .catch(function () { showValidationModal(['Network error']); });
}

function openNewMessageModal() {
  document.getElementById('newMessageModal').style.display = 'block';
  document.getElementById('receiverSelect').value          = '';
  document.getElementById('newMessageContent').value       = '';
}

function closeNewMessageModal() {
  document.getElementById('newMessageModal').style.display = 'none';
}

function sendNewMessage() {
  const sel     = document.getElementById('receiverSelect');
  const rid     = sel.value;
  const opt     = sel.options[sel.selectedIndex];
  const rname   = opt.dataset.name   || '';
  const rinit   = opt.dataset.init   || '';
  const ravatar = opt.dataset.avatar || 'av-blue';
  const content = document.getElementById('newMessageContent').value.trim();
  const errors = [];
  if (!rid) errors.push('Please select a user');
  if (!content || content.length === 0) errors.push('Message cannot be empty');
  if (content && content.length > 5000) errors.push('Message cannot exceed 5000 characters');
  if (errors.length) { showValidationModal(errors); return; }
  _doSendMessage(CURRENT_USER_ID, rid, CURRENT_USER_NAME, rname,
    CURRENT_USER_INIT, rinit, CURRENT_USER_AVATAR, ravatar, content,
    function () {
      closeNewMessageModal();
      loadConversations(function () { openConversation(rid, rname, rinit, ravatar); });
    });
}

function updateNavBadge() {
  const total = conversations.reduce(function (s, c) { return s + (parseInt(c.unread_count) || 0); }, 0);
  const badge = document.getElementById('navMsgBadge');
  if (!badge) return;
  badge.textContent   = total;
  badge.style.display = total > 0 ? 'inline-flex' : 'none';
}

function checkUrlParams() {
  const params     = new URLSearchParams(window.location.search);
  const openUser   = params.get('open_user');
  const openName   = params.get('open_name');
  const openInit   = params.get('open_init');
  const openAvatar = params.get('open_avatar') || 'av-blue';
  if (!openUser || !openName) return;
  const existing = conversations.find(function (c) { return c.other_user_id === openUser; });
  if (existing) {
    openConversation(openUser, existing.other_user_name, existing.other_user_init, existing.other_user_avatar);
  } else {
    openConversation(openUser, openName, openInit, openAvatar);
  }
  window.history.replaceState({}, '', window.location.pathname);
}

function formatTime(ts) {
  if (!ts) return '';
  const d   = new Date(ts);
  const now = new Date();
  const diff = now - d;
  const min = Math.floor(diff / 60000);
  if (min < 1)  return 'Just now';
  if (min < 60) return min + 'm';
  const h = Math.floor(min / 60);
  if (h < 24)   return h + 'h';
  const day = Math.floor(h / 24);
  if (day < 7)  return day + 'd';
  return d.toLocaleDateString();
}

function escHtml(text) {
  if (text == null) return '';
  const d = document.createElement('div');
  d.textContent = String(text);
  return d.innerHTML;
}

function escAttr(text) {
  if (text == null) return '';
  return String(text).replace(/&/g, '&amp;').replace(/'/g, '&#39;').replace(/"/g, '&quot;');
}

document.addEventListener('DOMContentLoaded', function () {
  loadConversations(function () {
    checkUrlParams();
  });
  window.addEventListener('click', function (e) {
    ['newMessageModal','editMessageModal','deleteConvModal','validationModal','confirmModal'].forEach(function (id) {
      const m = document.getElementById(id);
      if (m && e.target === m) m.style.display = 'none';
    });
  });
});