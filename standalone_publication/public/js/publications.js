/* ============================================================
   publications.js  –  Workify Publications
   - Avatar click passes post ID to messages page
   ============================================================ */

'use strict';

/* ---------- shared state ---------- */
let pendingImage        = null;
let postLikeStates      = {};
let isLiking            = false;
let isCommentLiking     = false;
let pendingDeletePostId = null;
let pendingDeleteCommentId = null;
let currentEditPostId   = null;
let currentEditCommentId = null;

/* ============================================================
   VALIDATION MODAL
   ============================================================ */
function showValidationModal(errors) {
  const modal   = document.getElementById('validationModal');
  const list    = document.getElementById('validationErrorList');
  if (!modal || !list) return;
  list.innerHTML = '';
  const items = Array.isArray(errors) ? errors : [errors];
  items.forEach(msg => {
    const li = document.createElement('li');
    li.textContent = msg;
    list.appendChild(li);
  });
  modal.style.display = 'block';
}

function closeValidationModal() {
  const modal = document.getElementById('validationModal');
  if (modal) modal.style.display = 'none';
}

/* ============================================================
   CONFIRM MODAL
   ============================================================ */
function showConfirmModal(message, onConfirm) {
  const modal = document.getElementById('confirmModal');
  const msg   = document.getElementById('confirmMessage');
  const btn   = document.getElementById('confirmOkBtn');
  if (!modal || !msg || !btn) { onConfirm(); return; }
  msg.textContent = message;
  modal.style.display = 'block';
  btn.onclick = function () {
    closeConfirmModal();
    onConfirm();
  };
}

function closeConfirmModal() {
  const modal = document.getElementById('confirmModal');
  if (modal) modal.style.display = 'none';
}

/* ============================================================
   POST-OPTION MODAL
   ============================================================ */
function showPostOptions(postId) {
  currentEditPostId = postId;
  const modal = document.getElementById('postOptionsModal');
  if (!modal) return;
  modal.style.display = 'block';
}

function closePostOptionsModal() {
  const modal = document.getElementById('postOptionsModal');
  if (modal) modal.style.display = 'none';
}

function postOptionsEdit() {
  closePostOptionsModal();
  editPost(currentEditPostId);
}

function postOptionsDelete() {
  closePostOptionsModal();
  showConfirmModal('Are you sure you want to delete this post?', function () {
    deletePost(currentEditPostId);
  });
}

/* ============================================================
   EDIT COMMENT MODAL
   ============================================================ */
function editComment(commentId, currentText) {
  currentEditCommentId = commentId;
  const modal = document.getElementById('editCommentModal');
  const ta    = document.getElementById('editCommentText');
  if (!modal || !ta) return;
  ta.value = currentText;
  modal.style.display = 'block';
}

function closeEditCommentModal() {
  const modal = document.getElementById('editCommentModal');
  if (modal) modal.style.display = 'none';
  currentEditCommentId = null;
}

function saveEditComment() {
  const ta      = document.getElementById('editCommentText');
  const newText = ta ? ta.value.trim() : '';
  if (!newText || newText.length < 2) {
    showValidationModal(['Comment must be at least 2 characters']);
    return;
  }
  if (newText.length > 5000) {
    showValidationModal(['Comment cannot exceed 5000 characters']);
    return;
  }
  const fd = new FormData();
  fd.append('action',     'edit_comment');
  fd.append('comment_id', currentEditCommentId);
  fd.append('comment',    newText);
  fd.append('user_name',  CURRENT_USER_NAME);
  fetch(BASE_URL, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        closeEditCommentModal();
        const wrapper = document.querySelector(`.comment-wrapper[data-comment-id="${currentEditCommentId}"]`)
                     || document.querySelector(`[data-comment-id="${currentEditCommentId}"]`);
        if (wrapper) {
          const card = wrapper.closest('.pub-post-card');
          if (card) loadComments(card.dataset.postId);
        }
      } else {
        showValidationModal([data.error || 'Could not edit comment']);
      }
    })
    .catch(() => showValidationModal(['Network error while editing comment']));
}

/* ============================================================
   AVATAR CLICK → START CHAT WITH POST ID
   ============================================================ */
function startChatFromAvatar(userId, userName, userInit, userAvatar, postId) {
  if (userId === CURRENT_USER_ID) return;
  const params = new URLSearchParams({
    open_user:   userId,
    open_name:   userName,
    open_init:   userInit,
    open_avatar: userAvatar,
    post_id:     postId
  });
  window.location.href = 'messages.php?' + params.toString();
}

/* ============================================================
   IMAGE HELPERS
   ============================================================ */
function autoResize(el) {
  el.style.height = 'auto';
  el.style.height = Math.min(el.scrollHeight, 200) + 'px';
}

function previewImage(input) {
  if (input.files && input.files[0]) {
    const file = input.files[0];
    const allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!allowed.includes(file.type)) {
      showValidationModal(['Only JPG, PNG, GIF, WEBP images are allowed']);
      input.value = '';
      return;
    }
    if (file.size > 5000000) {
      showValidationModal(['Image must be less than 5 MB']);
      input.value = '';
      return;
    }
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById('imgPreview').src = e.target.result;
      document.getElementById('imgPreviewWrap').style.display = 'block';
      pendingImage = file;
    };
    reader.readAsDataURL(file);
  }
}

function removeImage() {
  document.getElementById('imgPreviewWrap').style.display = 'none';
  document.getElementById('imgPreview').src = '';
  document.getElementById('imgUploadInput').value = '';
  pendingImage = null;
}

/* ============================================================
   POST CRUD
   ============================================================ */
function validatePostContent(content) {
  const errors = [];
  if (!content || content.trim().length === 0) errors.push('Please enter some content for your post');
  else if (content.trim().length < 5)          errors.push('Content must be at least 5 characters long');
  else if (content.trim().length > 5000)        errors.push('Content cannot exceed 5000 characters');
  return errors;
}

function submitPost() {
  const textarea = document.getElementById('newPostText');
  const content  = textarea.value.trim();
  const errors   = validatePostContent(content);
  if (errors.length) { showValidationModal(errors); return; }
  if (pendingImage) {
    const fd = new FormData();
    fd.append('action', 'upload_image');
    fd.append('image',  pendingImage);
    fetch(BASE_URL, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd })
      .then(r => r.json())
      .then(imageData => {
        if (imageData.success) sendPostWithImage(content, imageData.filename);
        else showValidationModal(['Image upload failed: ' + (imageData.error || 'Unknown error')]);
      })
      .catch(() => showValidationModal(['Image upload failed – network error']));
  } else {
    sendPostWithImage(content, '');
  }
}

function sendPostWithImage(content, imageUrl) {
  const fd = new FormData();
  fd.append('action',      'create');
  fd.append('user_id',     CURRENT_USER_ID);
  fd.append('user_name',   CURRENT_USER_NAME);
  fd.append('user_init',   'YO');
  fd.append('user_role',   'Freelancer');
  fd.append('user_avatar', 'av-blue');
  fd.append('content',     content);
  fd.append('image_url',   imageUrl);
  fetch(BASE_URL, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd })
    .then(r => r.json())
    .then(data => {
      if (data.success) location.reload();
      else showValidationModal([data.errors ? data.errors.join(', ') : 'Could not create post']);
    })
    .catch(() => showValidationModal(['An error occurred while creating the post']));
}

function editPost(postId) {
  const contentEl = document.getElementById('post-content-' + postId);
  if (!contentEl) return;
  document.getElementById('editId').value      = postId;
  document.getElementById('editContent').value = contentEl.innerText;
  document.getElementById('editModal').style.display = 'block';
}

function saveEdit() {
  const postId     = document.getElementById('editId').value;
  const newContent = document.getElementById('editContent').value.trim();
  const errors     = validatePostContent(newContent);
  if (errors.length) { showValidationModal(errors); return; }
  const fd = new FormData();
  fd.append('action',  'update');
  fd.append('id',      postId);
  fd.append('content', newContent);
  fd.append('user_id', CURRENT_USER_ID);
  fetch(BASE_URL, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd })
    .then(r => r.json())
    .then(data => {
      if (data.success) location.reload();
      else showValidationModal([data.errors ? data.errors.join(', ') : 'Could not update post']);
    })
    .catch(() => showValidationModal(['Network error while saving post']));
}

function deletePost(postId) {
  const fd = new FormData();
  fd.append('action',  'delete');
  fd.append('id',      postId);
  fd.append('user_id', CURRENT_USER_ID);
  fetch(BASE_URL, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd })
    .then(r => r.json())
    .then(data => {
      if (data.success) location.reload();
      else showValidationModal([data.errors ? data.errors.join(', ') : 'Cannot delete post']);
    })
    .catch(() => showValidationModal(['Network error while deleting post']));
}

function closeModal() {
  document.getElementById('editModal').style.display = 'none';
}

/* ============================================================
   LIKES
   ============================================================ */
function toggleLike(btn, postId, currentLikes) {
  if (isLiking) return;
  isLiking = true;
  const wasLiked = postLikeStates[postId] || false;
  const newLikes = Math.max(0, wasLiked ? currentLikes - 1 : currentLikes + 1);
  const fd = new FormData();
  fd.append('action', 'update_likes');
  fd.append('id',     postId);
  fd.append('likes',  newLikes);
  fetch(BASE_URL, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd })
    .then(r => r.json())
    .then(data => {
      isLiking = false;
      if (data.success) {
        postLikeStates[postId] = !wasLiked;
        btn.classList.toggle('liked');
        const span = document.querySelector('.like-count-' + postId);
        if (span) span.textContent = data.likes;
        btn.style.color      = !wasLiked ? 'var(--blue)' : 'var(--text-3)';
        btn.style.background = !wasLiked ? 'var(--blue-light)' : 'none';
      }
    })
    .catch(() => { isLiking = false; });
}

function toggleCommentLike(btn, commentId, currentLikes) {
  if (isCommentLiking) return;
  isCommentLiking = true;
  const wasLiked = btn.classList.contains('liked');
  const newLikes = Math.max(0, wasLiked ? currentLikes - 1 : currentLikes + 1);
  const fd = new FormData();
  fd.append('action',     'update_comment_likes');
  fd.append('comment_id', commentId);
  fd.append('likes',      newLikes);
  fetch(BASE_URL, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd })
    .then(r => r.json())
    .then(data => {
      isCommentLiking = false;
      if (data.success) {
        btn.classList.toggle('liked');
        const span = document.querySelector('.comment-like-count-' + commentId);
        if (span) span.textContent = data.likes;
        btn.style.color = wasLiked ? 'var(--text-3)' : 'var(--blue)';
      }
    })
    .catch(() => { isCommentLiking = false; });
}

/* ============================================================
   COMMENTS
   ============================================================ */
function toggleComments(postId) {
  const section = document.getElementById('comments-' + postId);
  if (!section) return;
  const isHidden = section.style.display === 'none' || section.style.display === '';
  section.style.display = isHidden ? 'block' : 'none';
  if (isHidden) loadComments(postId);
}

function loadComments(postId) {
  const fd = new FormData();
  fd.append('action',         'get_comments');
  fd.append('publication_id', postId);
  fetch(BASE_URL, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd })
    .then(r => r.json())
    .then(data => {
      if (!data.success) return;
      const list  = document.getElementById('comments-list-' + postId);
      const count = document.querySelector('.comment-count-' + postId);
      if (!list) return;
      list.innerHTML = '';
      let total = 0;
      if (!data.comments || data.comments.length === 0) {
        list.innerHTML = '<div class="pub-comment"><div class="pub-comment-content"><em>No comments yet.</em></div></div>';
      } else {
        data.comments.forEach(c => {
          total++;
          let repliesHtml = '';
          if (c.replies && c.replies.length > 0) {
            repliesHtml = '<div class="comment-replies">';
            c.replies.forEach(r => {
              total++;
              repliesHtml += buildCommentHtml(r, postId, true);
            });
            repliesHtml += '</div>';
          }
          list.innerHTML += `<div class="comment-wrapper" data-comment-id="${c.id}">${buildCommentHtml(c, postId, false)}${repliesHtml}</div>`;
        });
      }
      if (count) count.textContent = total;
    });
}

function buildCommentHtml(c, postId, isReply) {
  const isOwner = (c.user_name === CURRENT_USER_NAME);
  const ownerBtns = isOwner
    ? `<button class="comment-edit-btn"   onclick="editComment(${c.id}, ${JSON.stringify(c.comment)})">✏️ Edit</button>
       <button class="comment-delete-btn" onclick="confirmDeleteComment(${c.id})">🗑️ Delete</button>`
    : '';
  const replyBtn = !isReply
    ? `<button class="comment-reply-btn" onclick="showReplyForm(${postId}, ${c.id})">💬 Reply</button>`
    : '';
  const replyForm = !isReply ? `
    <div class="reply-form-container" id="reply-form-${c.id}" style="display:none; margin-top:10px;">
      <div class="pub-add-comment" style="padding-left:40px;">
        <div class="wf-avatar wf-avatar-32 av-blue">YO</div>
        <input type="text" class="pub-comment-input" id="reply-input-${c.id}" placeholder="Write a reply…">
        <button class="pub-comment-send" onclick="addReply(${postId}, ${c.id})">Send</button>
      </div>
    </div>` : '';
  return `
    <div class="pub-comment" data-comment-id="${c.id}">
      <div class="wf-avatar wf-avatar-32 ${c.user_avatar}">${escapeHtml(c.user_init)}</div>
      <div class="pub-comment-content">
        <div class="pub-comment-author">${escapeHtml(c.user_name)}</div>
        <div class="pub-comment-text" id="comment-text-${c.id}">${escapeHtml(c.comment).replace(/\n/g, '<br>')}</div>
        <div class="pub-comment-actions">
          <button class="comment-like-btn" onclick="toggleCommentLike(this, ${c.id}, ${c.likes})">
            👍 <span class="comment-like-count-${c.id}">${c.likes}</span>
          </button>
          ${replyBtn}
          ${ownerBtns}
        </div>
        ${replyForm}
      </div>
    </div>`;
}

function addComment(postId) {
  const input   = document.getElementById('comment-input-' + postId);
  const comment = input ? input.value.trim() : '';
  if (!comment || comment.length < 2) {
    showValidationModal(['Comment must be at least 2 characters']);
    return;
  }
  if (comment.length > 5000) {
    showValidationModal(['Comment cannot exceed 5000 characters']);
    return;
  }
  _postComment(postId, comment, null, input);
}

function addReply(postId, parentId) {
  const input   = document.getElementById('reply-input-' + parentId);
  const comment = input ? input.value.trim() : '';
  if (!comment || comment.length < 2) {
    showValidationModal(['Reply must be at least 2 characters']);
    return;
  }
  _postComment(postId, comment, parentId, input);
}

function _postComment(postId, comment, parentId, input) {
  const fd = new FormData();
  fd.append('action',         'add_comment');
  fd.append('publication_id', postId);
  fd.append('user_name',      CURRENT_USER_NAME);
  fd.append('user_init',      'YO');
  fd.append('user_avatar',    'av-blue');
  fd.append('comment',        comment);
  if (parentId) fd.append('parent_id', parentId);
  fetch(BASE_URL, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        if (input) input.value = '';
        loadComments(postId);
      } else {
        showValidationModal([data.error || 'Could not add comment']);
      }
    })
    .catch(() => showValidationModal(['Network error']));
}

function confirmDeleteComment(commentId) {
  showConfirmModal('Are you sure you want to delete this comment?', function () {
    deleteComment(commentId);
  });
}

function deleteComment(commentId) {
  const fd = new FormData();
  fd.append('action',     'delete_comment');
  fd.append('comment_id', commentId);
  fetch(BASE_URL, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        const wrapper = document.querySelector(`.comment-wrapper[data-comment-id="${commentId}"]`);
        if (wrapper) {
          const card = wrapper.closest('.pub-post-card');
          if (card) loadComments(card.dataset.postId);
        }
      } else {
        showValidationModal(['Error deleting comment']);
      }
    })
    .catch(() => showValidationModal(['Network error']));
}

function showReplyForm(postId, commentId) {
  const form = document.getElementById('reply-form-' + commentId);
  if (!form) return;
  const isHidden = form.style.display === 'none' || form.style.display === '';
  form.style.display = isHidden ? 'block' : 'none';
  if (isHidden) {
    const inp = document.getElementById('reply-input-' + commentId);
    if (inp) inp.focus();
  }
}

/* ============================================================
   SHARE
   ============================================================ */
function sharePost(postId, content, imageUrl) {
  const modal   = document.getElementById('shareModal');
  const preview = document.getElementById('sharePreview');
  let html = `<p>${escapeHtml(content.substring(0, 200))}${content.length > 200 ? '…' : ''}</p>`;
  if (imageUrl && imageUrl !== 'null' && imageUrl !== '') {
    html += `<img src="${imageUrl}" style="max-width:100%;max-height:200px;object-fit:contain;border-radius:8px;margin-top:10px;">`;
  }
  preview.innerHTML = html;
  modal.style.display = 'block';
}

function closeShareModal() {
  document.getElementById('shareModal').style.display = 'none';
}

function copyToClipboard() {
  navigator.clipboard.writeText(window.location.href).then(() => {
    closeShareModal();
    _toast('Link copied to clipboard!');
  });
}

function shareOnTwitter() {
  const text = encodeURIComponent('Check out this post on Workify!');
  const url  = encodeURIComponent(window.location.href);
  window.open('https://twitter.com/intent/tweet?text=' + text + '&url=' + url, '_blank');
  closeShareModal();
}

function shareOnFacebook() {
  window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(window.location.href), '_blank');
  closeShareModal();
}

function shareOnLinkedIn() {
  window.open('https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(window.location.href), '_blank');
  closeShareModal();
}

/* ============================================================
   FILTER / DISPLAY
   ============================================================ */
function filterMyPosts() {
  const fd = new FormData();
  fd.append('action',  'get_user_posts');
  fd.append('user_id', CURRENT_USER_ID);
  fetch(BASE_URL, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        displayPosts(data.posts);
        document.getElementById('totalPosts').textContent = data.posts.length;
      }
    });
}

function filterFeed(type) {
  if (type === 'all') location.reload();
}

function displayPosts(posts) {
  const container = document.getElementById('postsContainer');
  container.innerHTML = '';
  posts.forEach(post => {
    const isOwner = post.user_id === CURRENT_USER_ID;
    container.innerHTML += `
    <div class="pub-post-card" data-post-id="${post.id}" data-user-id="${post.user_id}">
      <div class="pub-post-header">
        <div class="pub-post-author">
          <div class="wf-avatar wf-avatar-40 ${post.user_avatar} clickable-avatar"
               onclick="startChatFromAvatar('${post.user_id}','${escapeAttr(post.user_name)}','${escapeAttr(post.user_init)}','${post.user_avatar}', ${post.id})"
               title="Message ${escapeAttr(post.user_name)} about this post"
               style="cursor:pointer;">
            ${escapeHtml(post.user_init)}
          </div>
          <div class="pub-author-info">
            <div class="pub-author-name">${escapeHtml(post.user_name)}</div>
            <div class="pub-author-meta">
              <span class="pub-role-badge ${post.user_role === 'Client' ? 'badge-client' : 'badge-freelancer'}">${post.user_role}</span>
              · <span>${new Date(post.created_at).toLocaleString()}</span>
              ${isOwner ? '· <span class="owner-badge">(You)</span>' : ''}
            </div>
          </div>
        </div>
        ${isOwner ? `<button class="pub-post-menu-btn" onclick="showPostOptions(${post.id})">⋮</button>` : ''}
      </div>
      <div class="pub-post-body">
        <p class="pub-post-text" id="post-content-${post.id}">${escapeHtml(post.content).replace(/\n/g,'<br>')}</p>
        ${post.image_url ? `<div class="pub-post-image"><img src="${post.image_url}" style="max-width:100%;border-radius:10px;margin-top:10px;"></div>` : ''}
      </div>
      <div class="pub-post-actions">
        <button class="pub-action-btn like-btn" onclick="toggleLike(this,${post.id},${post.likes})">
          👍 <span class="like-count-${post.id}">${post.likes}</span> Likes
        </button>
        <button class="pub-action-btn" onclick="toggleComments(${post.id})">💬 <span class="comment-count-${post.id}">0</span> Comments</button>
        <button class="pub-action-btn" onclick="sharePost(${post.id},'${escapeAttr(post.content)}','${post.image_url||''}')">📤 Share</button>
      </div>
      <div class="pub-comments-section" id="comments-${post.id}" style="display:none;">
        <div class="pub-add-comment">
          <div class="wf-avatar wf-avatar-32 av-blue">YO</div>
          <input type="text" class="pub-comment-input" id="comment-input-${post.id}" placeholder="Write a comment…" onkeypress="if(event.key==='Enter')addComment(${post.id})">
          <button class="pub-comment-send" onclick="addComment(${post.id})">Send</button>
        </div>
        <div class="pub-comments-list" id="comments-list-${post.id}"></div>
      </div>
    </div>`;
  });
}

/* ============================================================
   TOAST
   ============================================================ */
function _toast(msg) {
  let t = document.getElementById('wf-toast');
  if (!t) {
    t = document.createElement('div');
    t.id = 'wf-toast';
    t.style.cssText = 'position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:var(--text-1);color:#fff;padding:10px 20px;border-radius:8px;font-size:13px;z-index:9999;opacity:0;transition:opacity .2s';
    document.body.appendChild(t);
  }
  t.textContent = msg;
  t.style.opacity = '1';
  clearTimeout(t._timer);
  t._timer = setTimeout(() => { t.style.opacity = '0'; }, 2500);
}

/* ============================================================
   UTILITIES
   ============================================================ */
function escapeHtml(text) {
  if (text == null) return '';
  const div = document.createElement('div');
  div.textContent = String(text);
  return div.innerHTML;
}

function escapeAttr(text) {
  if (text == null) return '';
  return String(text)
    .replace(/&/g, '&amp;')
    .replace(/'/g, '&#39;')
    .replace(/"/g, '&quot;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');
}

/* ============================================================
   INIT
   ============================================================ */
document.addEventListener('DOMContentLoaded', function () {
  const ta = document.getElementById('newPostText');
  if (ta) autoResize(ta);
  document.querySelectorAll('.like-btn').forEach(btn => {
    const card = btn.closest('.pub-post-card');
    if (card) postLikeStates[card.dataset.postId] = btn.classList.contains('liked');
  });
  window.addEventListener('click', function (e) {
    ['editModal','shareModal','validationModal','confirmModal','postOptionsModal','editCommentModal'].forEach(id => {
      const m = document.getElementById(id);
      if (m && e.target === m) m.style.display = 'none';
    });
  });
});