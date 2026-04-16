<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../controllers/PublicationController.php';

$current_user_id = 'current_user';
$current_user_name = 'You';
$current_user_init = 'YO';
$current_user_avatar = 'av-blue';

$controller = new PublicationController();
$posts = $controller->getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Publications – Workify</title>
<link rel="stylesheet" href="../../public/css/workify-tokens.css">
<link rel="stylesheet" href="../../public/css/publications.css">
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
    <li><a href="#" onclick="filterFeed('all'); return false;">Home</a></li>
    <li><a href="#" onclick="filterFeed('browse'); return false;">Browse jobs</a></li>
    <li><a href="#" class="active" onclick="filterFeed('all'); return false;">Publications</a></li>
    <li><a href="#" onclick="filterFeed('messages'); return false;">Messages</a></li>
  </ul>
  <div class="wf-nav-right">
    <a href="../back/admin.php" class="wf-nav-icon-btn" style="text-decoration:none;">
      <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
    </a>
    <div class="wf-avatar wf-avatar-36 av-blue" style="cursor:pointer;"><?php echo $current_user_init; ?></div>
  </div>
</nav>

<div class="pub-shell">
  <div class="pub-grid">
    <aside class="pub-sidebar-left">
      <div class="pub-profile-card">
        <div class="pub-profile-banner"></div>
        <div class="pub-profile-body">
          <div class="pub-profile-avatar-wrap">
            <div class="pub-profile-avatar"><?php echo $current_user_init; ?></div>
          </div>
          <div class="pub-profile-name"><?php echo $current_user_name; ?></div>
          <div class="pub-profile-role">UI/UX Designer · Freelancer</div>
          <div class="pub-profile-stats">
            <div class="pub-stat-row"><span class="pub-stat-label">Profile views</span><span class="pub-stat-val">142</span></div>
            <div class="pub-stat-row"><span class="pub-stat-label">Connections</span><span class="pub-stat-val">87</span></div>
            <div class="pub-stat-row"><span class="pub-stat-label">Posts</span><span class="pub-stat-val" id="totalPosts"><?php echo count($posts); ?></span></div>
          </div>
        </div>
      </div>
      <nav class="pub-nav-card">
        <a class="pub-nav-item active" href="#" onclick="filterFeed('all'); return false;">📰 Feed</a>
        <a class="pub-nav-item" href="#" onclick="filterFeed('network'); return false;">🌐 Network</a>
        <a class="pub-nav-item" href="#" onclick="filterMyPosts(); return false;">📌 My Posts</a>
      </nav>
    </aside>

    <main class="pub-feed">
      <div class="pub-create-card">
        <div class="pub-create-top">
          <div class="wf-avatar wf-avatar-40 <?php echo $current_user_avatar; ?>"><?php echo $current_user_init; ?></div>
          <textarea class="pub-create-input" id="newPostText" rows="1" placeholder="Share something about your work, projects, or skills…" oninput="autoResize(this)"></textarea>
        </div>
        <div class="pub-create-actions">
          <div class="pub-create-tools">
            <label class="pub-tool-btn" style="cursor:pointer;">
              📷 Photo
              <input type="file" id="imgUploadInput" accept="image/*" style="display:none" onchange="previewImage(this)">
            </label>
          </div>
          <button class="wf-btn wf-btn-primary" onclick="submitPost()">Post</button>
        </div>
        <div id="imgPreviewWrap" style="display:none; margin-top: 10px;">
          <div style="position:relative; display:inline-block;">
            <img id="imgPreview" style="max-width:100%; max-height:200px; border-radius:8px;" src="">
            <button onclick="removeImage()" style="position:absolute; top:-8px; right:-8px; background:red; color:white; border:none; border-radius:50%; width:25px; height:25px; cursor:pointer;">×</button>
          </div>
        </div>
      </div>

      <div id="postsContainer">
        <?php foreach($posts as $post): 
          $comments = $controller->getComments($post['id']);
          $commentCount = count($comments);
          $isOwner = ($post['user_id'] == $current_user_id);
        ?>
        <div class="pub-post-card" data-post-id="<?php echo $post['id']; ?>" data-user-id="<?php echo $post['user_id']; ?>">
          <div class="pub-post-header">
            <div class="pub-post-author">
              <div class="wf-avatar wf-avatar-40 <?php echo $post['user_avatar']; ?>"><?php echo htmlspecialchars($post['user_init']); ?></div>
              <div class="pub-author-info">
                <div class="pub-author-name"><?php echo htmlspecialchars($post['user_name']); ?></div>
                <div class="pub-author-meta">
                  <span class="pub-role-badge <?php echo $post['user_role'] === 'Client' ? 'badge-client' : 'badge-freelancer'; ?>"><?php echo $post['user_role']; ?></span>
                  · <span><?php echo date('M j, g:i a', strtotime($post['created_at'])); ?></span>
                  <?php if($isOwner): ?>
                  · <span class="owner-badge">(You)</span>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <?php if($isOwner): ?>
            <button class="pub-post-menu-btn" onclick="showPostOptions(<?php echo $post['id']; ?>)">⋮</button>
            <?php endif; ?>
          </div>
          
          <div class="pub-post-body">
            <p class="pub-post-text" id="post-content-<?php echo $post['id']; ?>"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
            <?php if($post['image_url']): ?>
            <div class="pub-post-image">
              <img src="<?php echo htmlspecialchars($post['image_url']); ?>" alt="Post image" style="max-width:100%; border-radius:10px; margin-top:10px;">
            </div>
            <?php endif; ?>
          </div>
          
          <div class="pub-post-actions">
            <button class="pub-action-btn like-btn" onclick="toggleLike(this, <?php echo $post['id']; ?>, <?php echo $post['likes']; ?>)">
              👍 <span class="like-count-<?php echo $post['id']; ?>"><?php echo $post['likes']; ?></span> Likes
            </button>
            <button class="pub-action-btn" onclick="toggleComments(<?php echo $post['id']; ?>)">
              💬 <span class="comment-count-<?php echo $post['id']; ?>"><?php echo $commentCount; ?></span> Comments
            </button>
            <button class="pub-action-btn" onclick="sharePost(<?php echo $post['id']; ?>, '<?php echo htmlspecialchars(addslashes($post['content'])); ?>', '<?php echo htmlspecialchars(addslashes($post['image_url'])); ?>')">
              📤 Share
            </button>
          </div>
          
          <div class="pub-comments-section" id="comments-<?php echo $post['id']; ?>" style="display:none;">
            <div class="pub-add-comment">
              <div class="wf-avatar wf-avatar-32 <?php echo $current_user_avatar; ?>"><?php echo $current_user_init; ?></div>
              <input type="text" class="pub-comment-input" id="comment-input-<?php echo $post['id']; ?>" placeholder="Write a comment..." onkeypress="if(event.key==='Enter') addComment(<?php echo $post['id']; ?>)">
              <button class="pub-comment-send" onclick="addComment(<?php echo $post['id']; ?>)">Send</button>
            </div>
            <div class="pub-comments-list" id="comments-list-<?php echo $post['id']; ?>">
              <?php foreach($comments as $comment): ?>
              <div class="comment-wrapper" data-comment-id="<?php echo $comment['id']; ?>">
                <div class="pub-comment">
                  <div class="wf-avatar wf-avatar-32 <?php echo $comment['user_avatar']; ?>"><?php echo htmlspecialchars($comment['user_init']); ?></div>
                  <div class="pub-comment-content">
                    <div class="pub-comment-author"><?php echo htmlspecialchars($comment['user_name']); ?></div>
                    <div class="pub-comment-text" id="comment-text-<?php echo $comment['id']; ?>"><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></div>
                    <div class="pub-comment-actions">
                      <button class="comment-like-btn" onclick="toggleCommentLike(this, <?php echo $comment['id']; ?>, <?php echo $comment['likes']; ?>)">
                        👍 <span class="comment-like-count-<?php echo $comment['id']; ?>"><?php echo $comment['likes']; ?></span>
                      </button>
                      <button class="comment-reply-btn" onclick="showReplyForm(<?php echo $post['id']; ?>, <?php echo $comment['id']; ?>)">
                        💬 Reply
                      </button>
                      <?php if($comment['user_name'] == $current_user_name): ?>
                      <button class="comment-edit-btn" onclick="editComment(<?php echo $comment['id']; ?>, '<?php echo htmlspecialchars(addslashes($comment['comment'])); ?>')">
                        ✏️ Edit
                      </button>
                      <button class="comment-delete-btn" onclick="deleteComment(<?php echo $comment['id']; ?>)">
                        🗑️ Delete
                      </button>
                      <?php endif; ?>
                    </div>
                    <div class="reply-form-container" id="reply-form-<?php echo $comment['id']; ?>" style="display:none; margin-top:10px;">
                      <div class="pub-add-comment" style="padding-left: 40px;">
                        <div class="wf-avatar wf-avatar-32 <?php echo $current_user_avatar; ?>"><?php echo $current_user_init; ?></div>
                        <input type="text" class="pub-comment-input" id="reply-input-<?php echo $comment['id']; ?>" placeholder="Write a reply...">
                        <button class="pub-comment-send" onclick="addReply(<?php echo $post['id']; ?>, <?php echo $comment['id']; ?>)">Send</button>
                      </div>
                    </div>
                  </div>
                </div>
                <?php if(!empty($comment['replies'])): ?>
                <div class="comment-replies" style="margin-left: 50px; margin-top: 10px;">
                  <?php foreach($comment['replies'] as $reply): ?>
                  <div class="pub-comment" style="margin-bottom: 8px;">
                    <div class="wf-avatar wf-avatar-32 <?php echo $reply['user_avatar']; ?>"><?php echo htmlspecialchars($reply['user_init']); ?></div>
                    <div class="pub-comment-content">
                      <div class="pub-comment-author"><?php echo htmlspecialchars($reply['user_name']); ?></div>
                      <div class="pub-comment-text" id="comment-text-<?php echo $reply['id']; ?>"><?php echo nl2br(htmlspecialchars($reply['comment'])); ?></div>
                      <div class="pub-comment-actions">
                        <button class="comment-like-btn" onclick="toggleCommentLike(this, <?php echo $reply['id']; ?>, <?php echo $reply['likes']; ?>)">
                          👍 <span class="comment-like-count-<?php echo $reply['id']; ?>"><?php echo $reply['likes']; ?></span>
                        </button>
                        <?php if($reply['user_name'] == $current_user_name): ?>
                        <button class="comment-edit-btn" onclick="editComment(<?php echo $reply['id']; ?>, '<?php echo htmlspecialchars(addslashes($reply['comment'])); ?>')">
                          ✏️ Edit
                        </button>
                        <button class="comment-delete-btn" onclick="deleteComment(<?php echo $reply['id']; ?>)">
                          🗑️ Delete
                        </button>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                  <?php endforeach; ?>
                </div>
                <?php endif; ?>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </main>

    <aside class="pub-sidebar-right">
      <div class="pub-widget">
        <div class="pub-widget-title">Trending topics</div>
        <div class="pub-tag-cloud">
          <a class="pub-tag" href="#">#UIDesign</a>
          <a class="pub-tag" href="#">#Freelance</a>
          <a class="pub-tag" href="#">#WebDev</a>
          <a class="pub-tag" href="#">#React</a>
          <a class="pub-tag" href="#">#Figma</a>
          <a class="pub-tag" href="#">#RemoteWork</a>
        </div>
      </div>
    </aside>
  </div>
</div>

<div id="editModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <h3>Edit Publication</h3>
    <textarea id="editContent" rows="6"></textarea>
    <input type="hidden" id="editId">
    <div class="modal-buttons">
      <button class="btn-save" onclick="saveEdit()">Save Changes</button>
      <button class="btn-cancel" onclick="closeModal()">Cancel</button>
    </div>
  </div>
</div>

<div id="shareModal" class="modal">
  <div class="modal-content share-modal">
    <span class="close" onclick="closeShareModal()">&times;</span>
    <h3>📤 Share this post</h3>
    <div class="share-preview" id="sharePreview"></div>
    <div class="share-buttons">
      <button onclick="copyToClipboard()">📋 Copy Link</button>
      <button onclick="shareOnTwitter()">🐦 Twitter</button>
      <button onclick="shareOnFacebook()">📘 Facebook</button>
      <button onclick="shareOnLinkedIn()">💼 LinkedIn</button>
    </div>
  </div>
</div>

<script>
const CURRENT_USER_ID = '<?php echo $current_user_id; ?>';
const CURRENT_USER_NAME = '<?php echo $current_user_name; ?>';
const BASE_URL = window.location.href;
let pendingImage = null;
let postLikeStates = {};
let isLiking = false;
let isCommentLiking = false;

function autoResize(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 200) + 'px';
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imgPreview').src = e.target.result;
            document.getElementById('imgPreviewWrap').style.display = 'block';
            pendingImage = input.files[0];
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage() {
    document.getElementById('imgPreviewWrap').style.display = 'none';
    document.getElementById('imgPreview').src = '';
    document.getElementById('imgUploadInput').value = '';
    pendingImage = null;
}

function submitPost() {
    const textarea = document.getElementById('newPostText');
    const content = textarea.value.trim();
    
    if (!content || content.length < 5) {
        alert('Content must be at least 5 characters');
        return;
    }
    
    if (pendingImage) {
        const imageFormData = new FormData();
        imageFormData.append('action', 'upload_image');
        imageFormData.append('image', pendingImage);
        
        fetch(BASE_URL, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: imageFormData
        })
        .then(response => response.json())
        .then(imageData => {
            if (imageData.success) {
                sendPostWithImage(content, imageData.filename);
            } else {
                alert('Image upload failed: ' + imageData.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Image upload failed');
        });
    } else {
        sendPostWithImage(content, '');
    }
}

function sendPostWithImage(content, imageUrl) {
    const formData = new FormData();
    formData.append('action', 'create');
    formData.append('user_id', CURRENT_USER_ID);
    formData.append('user_name', CURRENT_USER_NAME);
    formData.append('user_init', 'YO');
    formData.append('user_role', 'Freelancer');
    formData.append('user_avatar', 'av-blue');
    formData.append('content', content);
    formData.append('image_url', imageUrl);
    
    fetch(BASE_URL, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + (data.errors ? data.errors.join(', ') : 'Could not create post'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while creating the post');
    });
}

function filterMyPosts() {
    const formData = new FormData();
    formData.append('action', 'get_user_posts');
    formData.append('user_id', CURRENT_USER_ID);
    
    fetch(BASE_URL, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayPosts(data.posts);
            document.getElementById('totalPosts').textContent = data.posts.length;
        }
    });
}

function filterFeed(type) {
    if (type === 'all') {
        location.reload();
    }
}

function displayPosts(posts) {
    const container = document.getElementById('postsContainer');
    container.innerHTML = '';
    
    posts.forEach(post => {
        const isOwner = (post.user_id == CURRENT_USER_ID);
        const postHtml = `
        <div class="pub-post-card" data-post-id="${post.id}">
          <div class="pub-post-header">
            <div class="pub-post-author">
              <div class="wf-avatar wf-avatar-40 ${post.user_avatar}">${escapeHtml(post.user_init)}</div>
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
            <p class="pub-post-text">${escapeHtml(post.content).replace(/\n/g, '<br>')}</p>
            ${post.image_url ? `<div class="pub-post-image"><img src="${post.image_url}" style="max-width:100%; border-radius:10px; margin-top:10px;"></div>` : ''}
          </div>
          <div class="pub-post-actions">
            <button class="pub-action-btn like-btn" onclick="toggleLike(this, ${post.id}, ${post.likes})">
              👍 <span class="like-count-${post.id}">${post.likes}</span> Likes
            </button>
            <button class="pub-action-btn" onclick="toggleComments(${post.id})">💬 0 Comments</button>
            <button class="pub-action-btn" onclick="sharePost(${post.id}, '${escapeHtml(post.content)}', '${post.image_url || ''}')">📤 Share</button>
          </div>
          <div class="pub-comments-section" id="comments-${post.id}" style="display:none;">
            <div class="pub-add-comment">
              <div class="wf-avatar wf-avatar-32 av-blue">YO</div>
              <input type="text" class="pub-comment-input" id="comment-input-${post.id}" placeholder="Write a comment...">
              <button class="pub-comment-send" onclick="addComment(${post.id})">Send</button>
            </div>
            <div class="pub-comments-list" id="comments-list-${post.id}"></div>
          </div>
        </div>
        `;
        container.innerHTML += postHtml;
    });
}

function toggleLike(btn, postId, currentLikes) {
    if (isLiking) return;
    isLiking = true;
    
    const isCurrentlyLiked = postLikeStates[postId] || false;
    let newLikes = isCurrentlyLiked ? currentLikes - 1 : currentLikes + 1;
    newLikes = Math.max(0, newLikes);
    
    const formData = new FormData();
    formData.append('action', 'update_likes');
    formData.append('id', postId);
    formData.append('likes', newLikes);
    
    fetch(BASE_URL, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        isLiking = false;
        if (data.success) {
            postLikeStates[postId] = !isCurrentlyLiked;
            btn.classList.toggle('liked');
            const likeSpan = document.querySelector('.like-count-' + postId);
            if (likeSpan) likeSpan.textContent = data.likes;
            if (!isCurrentlyLiked) {
                btn.style.color = 'var(--blue)';
                btn.style.background = 'var(--blue-light)';
            } else {
                btn.style.color = 'var(--text-3)';
                btn.style.background = 'none';
            }
        }
    })
    .catch(error => {
        isLiking = false;
        console.error('Error:', error);
    });
}

function toggleCommentLike(btn, commentId, currentLikes) {
    if (isCommentLiking) return;
    isCommentLiking = true;
    
    const isLiked = btn.classList.contains('liked');
    let newLikes = isLiked ? currentLikes - 1 : currentLikes + 1;
    newLikes = Math.max(0, newLikes);
    
    const formData = new FormData();
    formData.append('action', 'update_comment_likes');
    formData.append('comment_id', commentId);
    formData.append('likes', newLikes);
    
    fetch(BASE_URL, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        isCommentLiking = false;
        if (data.success) {
            btn.classList.toggle('liked');
            const likeSpan = document.querySelector('.comment-like-count-' + commentId);
            if (likeSpan) likeSpan.textContent = data.likes;
            if (isLiked) {
                btn.style.color = 'var(--text-3)';
            } else {
                btn.style.color = 'var(--blue)';
            }
        }
    })
    .catch(error => {
        isCommentLiking = false;
        console.error('Error:', error);
    });
}

function toggleComments(postId) {
    const commentsSection = document.getElementById('comments-' + postId);
    if (commentsSection.style.display === 'none' || commentsSection.style.display === '') {
        commentsSection.style.display = 'block';
        loadComments(postId);
    } else {
        commentsSection.style.display = 'none';
    }
}

function loadComments(postId) {
    const formData = new FormData();
    formData.append('action', 'get_comments');
    formData.append('publication_id', postId);
    
    fetch(BASE_URL, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.comments) {
            const commentsList = document.getElementById('comments-list-' + postId);
            const commentSpan = document.querySelector('.comment-count-' + postId);
            commentsList.innerHTML = '';
            data.comments.forEach(comment => {
                const commentHtml = `
                <div class="pub-comment">
                  <div class="wf-avatar wf-avatar-32 ${comment.user_avatar}">${comment.user_init}</div>
                  <div class="pub-comment-content">
                    <div class="pub-comment-author">${escapeHtml(comment.user_name)}</div>
                    <div class="pub-comment-text">${escapeHtml(comment.comment).replace(/\n/g, '<br>')}</div>
                    <div class="pub-comment-actions">
                      <button class="comment-like-btn" onclick="toggleCommentLike(this, ${comment.id}, ${comment.likes})">👍 <span class="comment-like-count-${comment.id}">${comment.likes}</span></button>
                      <button class="comment-reply-btn" onclick="showReplyForm(${postId}, ${comment.id})">💬 Reply</button>
                      ${comment.user_name == CURRENT_USER_NAME ? `<button class="comment-edit-btn" onclick="editComment(${comment.id}, '${escapeHtml(comment.comment)}')">✏️ Edit</button>
                      <button class="comment-delete-btn" onclick="deleteComment(${comment.id})">🗑️ Delete</button>` : ''}
                    </div>
                    <div class="reply-form-container" id="reply-form-${comment.id}" style="display:none; margin-top:10px;">
                      <div class="pub-add-comment" style="padding-left: 40px;">
                        <div class="wf-avatar wf-avatar-32 av-blue">YO</div>
                        <input type="text" class="pub-comment-input" id="reply-input-${comment.id}" placeholder="Write a reply...">
                        <button class="pub-comment-send" onclick="addReply(${postId}, ${comment.id})">Send</button>
                      </div>
                    </div>
                  </div>
                </div>`;
                commentsList.innerHTML += commentHtml;
            });
            if (commentSpan) commentSpan.textContent = data.comments.length;
        }
    });
}

function addComment(postId) {
    const input = document.getElementById('comment-input-' + postId);
    const comment = input.value.trim();
    if (!comment || comment.length < 2) {
        alert('Comment must be at least 2 characters');
        return;
    }
    const formData = new FormData();
    formData.append('action', 'add_comment');
    formData.append('publication_id', postId);
    formData.append('user_name', CURRENT_USER_NAME);
    formData.append('user_init', 'YO');
    formData.append('user_avatar', 'av-blue');
    formData.append('comment', comment);
    fetch(BASE_URL, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Could not add comment'));
        }
    });
}

function addReply(postId, parentCommentId) {
    const input = document.getElementById('reply-input-' + parentCommentId);
    const comment = input.value.trim();
    if (!comment || comment.length < 2) {
        alert('Reply must be at least 2 characters');
        return;
    }
    const formData = new FormData();
    formData.append('action', 'add_comment');
    formData.append('publication_id', postId);
    formData.append('user_name', CURRENT_USER_NAME);
    formData.append('user_init', 'YO');
    formData.append('user_avatar', 'av-blue');
    formData.append('comment', comment);
    formData.append('parent_id', parentCommentId);
    fetch(BASE_URL, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Could not add reply'));
        }
    });
}

function showReplyForm(postId, commentId) {
    const replyForm = document.getElementById('reply-form-' + commentId);
    if (replyForm.style.display === 'none' || replyForm.style.display === '') {
        replyForm.style.display = 'block';
        document.getElementById('reply-input-' + commentId).focus();
    } else {
        replyForm.style.display = 'none';
    }
}

function editComment(commentId, currentText) {
    const newText = prompt('Edit your comment:', currentText);
    if (newText && newText.trim().length >= 2) {
        const formData = new FormData();
        formData.append('action', 'edit_comment');
        formData.append('comment_id', commentId);
        formData.append('comment', newText.trim());
        formData.append('user_name', CURRENT_USER_NAME);
        fetch(BASE_URL, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.error || 'Could not edit comment'));
            }
        });
    }
}

function deleteComment(commentId) {
    if (confirm('Are you sure you want to delete this comment?')) {
        const formData = new FormData();
        formData.append('action', 'delete_comment');
        formData.append('comment_id', commentId);
        fetch(BASE_URL, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) location.reload();
            else alert('Error deleting comment');
        });
    }
}

function sharePost(postId, content, imageUrl) {
    const modal = document.getElementById('shareModal');
    const sharePreview = document.getElementById('sharePreview');
    let previewHtml = '<p>' + escapeHtml(content.substring(0, 150)) + (content.length > 150 ? '...' : '') + '</p>';
    if (imageUrl && imageUrl !== 'null' && imageUrl !== '') {
        previewHtml += '<img src="' + imageUrl + '" style="max-width:100%; border-radius:8px; margin-top:10px;">';
    }
    sharePreview.innerHTML = previewHtml;
    modal.style.display = 'block';
}

function copyToClipboard() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        alert('Link copied to clipboard!');
        closeShareModal();
    });
}

function shareOnTwitter() {
    const text = encodeURIComponent('Check out this post on Workify!');
    const url = encodeURIComponent(window.location.href);
    window.open('https://twitter.com/intent/tweet?text=' + text + '&url=' + url, '_blank');
    closeShareModal();
}

function shareOnFacebook() {
    const url = encodeURIComponent(window.location.href);
    window.open('https://www.facebook.com/sharer/sharer.php?u=' + url, '_blank');
    closeShareModal();
}

function shareOnLinkedIn() {
    const url = encodeURIComponent(window.location.href);
    window.open('https://www.linkedin.com/sharing/share-offsite/?url=' + url, '_blank');
    closeShareModal();
}

function closeShareModal() {
    document.getElementById('shareModal').style.display = 'none';
}

function editPost(postId) {
    const contentElement = document.getElementById('post-content-' + postId);
    if (contentElement) {
        document.getElementById('editId').value = postId;
        document.getElementById('editContent').value = contentElement.innerText;
        document.getElementById('editModal').style.display = 'block';
    }
}

function saveEdit() {
    const postId = document.getElementById('editId').value;
    const newContent = document.getElementById('editContent').value.trim();
    if (newContent.length < 5) {
        alert('Content must be at least 5 characters');
        return;
    }
    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('id', postId);
    formData.append('content', newContent);
    formData.append('user_id', CURRENT_USER_ID);
    fetch(BASE_URL, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) location.reload();
        else alert('Error: ' + (data.errors ? data.errors.join(', ') : 'Could not update post'));
    });
}

function deletePost(postId) {
    if (confirm('Are you sure you want to delete this post?')) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', postId);
        formData.append('user_id', CURRENT_USER_ID);
        fetch(BASE_URL, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) location.reload();
            else alert('Error: ' + (data.errors ? data.errors.join(', ') : 'Cannot delete'));
        });
    }
}

function showPostOptions(postId) {
    const choice = confirm('Edit this post?\n\nOK = Edit\nCancel = Delete');
    if (choice) editPost(postId);
    else deletePost(postId);
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

window.onclick = function(event) {
    const editModal = document.getElementById('editModal');
    const shareModal = document.getElementById('shareModal');
    if (event.target == editModal) editModal.style.display = 'none';
    if (event.target == shareModal) shareModal.style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('newPostText');
    if (textarea) autoResize(textarea);
    const likeButtons = document.querySelectorAll('.like-btn');
    for (let i = 0; i < likeButtons.length; i++) {
        const btn = likeButtons[i];
        const postCard = btn.closest('.pub-post-card');
        if (postCard) {
            const postId = postCard.dataset.postId;
            postLikeStates[postId] = btn.classList.contains('liked');
        }
    }
});
</script>
</body>
</html>