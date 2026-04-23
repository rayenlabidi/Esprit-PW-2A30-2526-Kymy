<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../controllers/PublicationC.php';

$current_user_id     = 'current_user';
$current_user_name   = 'You';
$current_user_init   = 'YO';
$current_user_avatar = 'av-blue';

$controller = new PublicationC();
$posts      = $controller->ListePublications();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Publications – Workify</title>
<link rel="stylesheet" href="../../public/css/workify-tokens.css">
<link rel="stylesheet" href="../../public/css/publications.css">
<style>
.clickable-avatar { transition: box-shadow .15s; cursor: pointer; }
.clickable-avatar:hover { box-shadow: 0 0 0 3px var(--blue-mid); }

.post-options-modal-content {
  position: fixed; top: 50%; left: 50%;
  transform: translate(-50%, -50%);
  background: var(--bg-card);
  border-radius: var(--radius-lg);
  padding: 28px 28px 22px;
  width: 340px; max-width: 90%;
  box-shadow: var(--shadow-lg);
  animation: slideIn .25s ease;
  text-align: center;
}
.post-options-modal-content h3 { margin-bottom: 18px; font-size: 17px; }
.post-options-btns { display: flex; flex-direction: column; gap: 10px; }
.post-options-btns button {
  padding: 11px 20px; border: none; border-radius: var(--radius-md);
  font-family: var(--font); font-size: 14px; font-weight: 600; cursor: pointer;
  transition: all .15s;
}
.btn-opt-edit   { background: var(--blue-light); color: var(--blue); }
.btn-opt-edit:hover   { background: var(--blue); color: white; }
.btn-opt-delete { background: var(--red-light);  color: var(--red);  }
.btn-opt-delete:hover { background: var(--red);  color: white; }
.btn-opt-cancel { background: var(--bg); color: var(--text-3); border: 1px solid var(--border) !important; }
.btn-opt-cancel:hover { background: var(--bg-hover); }
#editCommentModal .modal-content textarea {
  width: 100%; padding: 10px 12px;
  border: 1.5px solid var(--border); border-radius: var(--radius-md);
  font-family: var(--font); font-size: 14px; resize: vertical; min-height: 80px;
}
#editCommentModal .modal-content textarea:focus { outline: none; border-color: var(--blue); }
@keyframes slideIn {
  from { opacity:0; transform: translate(-50%, -60%); }
  to   { opacity:1; transform: translate(-50%, -50%); }
}
</style>
</head>
<body>

<nav class="wf-nav">
  <a href="#" class="wf-nav-logo">
    <div class="wf-nav-logo-box">
      <svg viewBox="0 0 16 16"><rect x="2" y="4" width="5" height="8" rx="1"/>
        <rect x="9" y="4" width="5" height="8" rx="1"/><rect x="2" y="1" width="12" height="2" rx="1"/>
      </svg>
    </div>
    Workify
  </a>
  <ul class="wf-nav-links">
    <li><a href="#" onclick="filterFeed('all'); return false;">Home</a></li>
    <li><a href="#" onclick="filterFeed('browse'); return false;">Browse jobs</a></li>
    <li><a href="#" class="active" onclick="filterFeed('all'); return false;">Publications</a></li>
    <li><a href="../front/messages.php">Messages</a></li>
  </ul>
  <div class="wf-nav-right">
    <a href="../back/admin.php" class="wf-nav-icon-btn" style="text-decoration:none;" title="Admin">
      <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
      </svg>
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
          <textarea class="pub-create-input" id="newPostText" rows="1"
                    placeholder="Share something about your work, projects, or skills…"
                    oninput="autoResize(this)"></textarea>
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
        <div id="imgPreviewWrap" style="display:none; margin-top:10px;">
          <div style="position:relative; display:inline-block;">
            <img id="imgPreview" style="max-width:100%; max-height:200px; border-radius:8px;" src="" alt="Preview">
            <button onclick="removeImage()" style="position:absolute;top:-8px;right:-8px;background:red;color:white;border:none;border-radius:50%;width:25px;height:25px;cursor:pointer;">×</button>
          </div>
        </div>
      </div>

      <div id="postsContainer">
        <?php foreach($posts as $post):
          $comments = $controller->ListeComments($post['id']);
          $commentCount = 0;
          foreach($comments as $c) {
            $commentCount++;
            if(!empty($c['replies'])) $commentCount += count($c['replies']);
          }
          $isOwner = ($post['user_id'] == $current_user_id);
          $isOtherUser = ($post['user_id'] != $current_user_id);
        ?>
        <div class="pub-post-card" data-post-id="<?php echo $post['id']; ?>" data-user-id="<?php echo htmlspecialchars($post['user_id']); ?>">
          <div class="pub-post-header">
            <div class="pub-post-author">
              <div class="wf-avatar wf-avatar-40 <?php echo $post['user_avatar']; ?><?php echo $isOtherUser ? ' clickable-avatar' : ''; ?>"
                   <?php if($isOtherUser): ?>
                   onclick="startChatFromAvatar('<?php echo htmlspecialchars($post['user_id']); ?>','<?php echo htmlspecialchars(addslashes($post['user_name'])); ?>','<?php echo htmlspecialchars($post['user_init']); ?>','<?php echo $post['user_avatar']; ?>', <?php echo $post['id']; ?>)"
                   title="Message <?php echo htmlspecialchars($post['user_name']); ?> about this post"
                   style="cursor:pointer;"
                   <?php endif; ?>>
                <?php echo htmlspecialchars($post['user_init']); ?>
              </div>
              <div class="pub-author-info">
                <div class="pub-author-name"><?php echo htmlspecialchars($post['user_name']); ?></div>
                <div class="pub-author-meta">
                  <span class="pub-role-badge <?php echo $post['user_role'] === 'Client' ? 'badge-client' : 'badge-freelancer'; ?>"><?php echo $post['user_role']; ?></span>
                  · <span><?php echo date('M j, g:i a', strtotime($post['created_at'])); ?></span>
                  <?php if($isOwner): ?>· <span class="owner-badge">(You)</span><?php endif; ?>
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
              <img src="<?php echo htmlspecialchars($post['image_url']); ?>" alt="Post image" style="max-width:100%;border-radius:10px;margin-top:10px;">
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
              <input type="text" class="pub-comment-input" id="comment-input-<?php echo $post['id']; ?>"
                     placeholder="Write a comment…"
                     onkeypress="if(event.key==='Enter') addComment(<?php echo $post['id']; ?>)">
              <button class="pub-comment-send" onclick="addComment(<?php echo $post['id']; ?>)">Send</button>
            </div>
            <div class="pub-comments-list" id="comments-list-<?php echo $post['id']; ?>">
              <div class="pub-comment"><div class="pub-comment-content"><em>Loading comments…</em></div></div>
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

<div id="validationModal" class="modal" style="background:rgba(0,0,0,.55);backdrop-filter:blur(3px);">
  <div class="validation-modal-content" style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:var(--bg-card);padding:30px;border-radius:var(--radius-lg);width:420px;max-width:90%;text-align:center;box-shadow:var(--shadow-lg);animation:slideIn .25s ease;">
    <div style="width:56px;height:56px;border-radius:50%;background:var(--red-light);display:flex;align-items:center;justify-content:center;margin:0 auto 18px;">
      <svg width="28" height="28" fill="none" stroke="var(--red)" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
    </div>
    <h3 style="font-size:18px;margin-bottom:14px;color:var(--text-1);">Validation Error</h3>
    <div style="background:var(--bg);padding:12px 16px;border-radius:var(--radius-md);margin-bottom:22px;text-align:left;max-height:180px;overflow-y:auto;">
      <ul id="validationErrorList" style="margin:0;padding-left:18px;"></ul>
    </div>
    <button onclick="closeValidationModal()"
            style="padding:10px 28px;background:var(--blue);color:white;border:none;border-radius:var(--radius-md);font-family:var(--font);font-size:14px;font-weight:600;cursor:pointer;">
      OK
    </button>
  </div>
</div>

<div id="confirmModal" class="modal" style="background:rgba(0,0,0,.55);backdrop-filter:blur(3px);">
  <div style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:var(--bg-card);padding:30px;border-radius:var(--radius-lg);width:380px;max-width:90%;text-align:center;box-shadow:var(--shadow-lg);animation:slideIn .25s ease;">
    <div style="width:56px;height:56px;border-radius:50%;background:var(--red-light);display:flex;align-items:center;justify-content:center;margin:0 auto 18px;">
      <svg width="28" height="28" fill="none" stroke="var(--red)" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
    </div>
    <h3 style="font-size:18px;margin-bottom:10px;">Are you sure?</h3>
    <p id="confirmMessage" style="color:var(--text-2);margin-bottom:22px;line-height:1.6;font-size:14px;"></p>
    <div style="display:flex;gap:10px;justify-content:center;">
      <button id="confirmOkBtn"
              style="padding:10px 24px;background:var(--red);color:white;border:none;border-radius:var(--radius-md);font-family:var(--font);font-size:14px;font-weight:600;cursor:pointer;">
        Confirm
      </button>
      <button onclick="closeConfirmModal()"
              style="padding:10px 24px;background:var(--bg);color:var(--text-2);border:1px solid var(--border);border-radius:var(--radius-md);font-family:var(--font);font-size:14px;font-weight:600;cursor:pointer;">
        Cancel
      </button>
    </div>
  </div>
</div>

<div id="postOptionsModal" class="modal" style="background:rgba(0,0,0,.55);backdrop-filter:blur(3px);">
  <div class="post-options-modal-content">
    <h3>Post Options</h3>
    <div class="post-options-btns">
      <button class="btn-opt-edit"   onclick="postOptionsEdit()">✏️ Edit this post</button>
      <button class="btn-opt-delete" onclick="postOptionsDelete()">🗑️ Delete this post</button>
      <button class="btn-opt-cancel" onclick="closePostOptionsModal()">Cancel</button>
    </div>
  </div>
</div>

<div id="editModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <h3>Edit Publication</h3>
    <textarea id="editContent" rows="6" style="width:100%;padding:12px;border:1.5px solid var(--border);border-radius:var(--radius-md);font-family:var(--font);font-size:14px;resize:vertical;margin-bottom:20px;"></textarea>
    <input type="hidden" id="editId">
    <div class="modal-buttons">
      <button class="btn-save" onclick="saveEdit()">Save Changes</button>
      <button class="btn-cancel" onclick="closeModal()">Cancel</button>
    </div>
  </div>
</div>

<!-- ============================================================
     MODAL: Edit Comment
     ============================================================ -->
<div id="editCommentModal" class="modal">
  <div class="modal-content" id="editCommentModal">
    <span class="close" onclick="closeEditCommentModal()">&times;</span>
    <h3>Edit Comment</h3>
    <div class="form-group">
      <textarea id="editCommentText" rows="4" placeholder="Edit your comment…"></textarea>
    </div>
    <div class="modal-buttons">
      <button class="btn-save" onclick="saveEditComment()">Save</button>
      <button class="btn-cancel" onclick="closeEditCommentModal()">Cancel</button>
    </div>
  </div>
</div>

<div id="shareModal" class="modal">
  <div class="modal-content share-modal" style="max-width:500px;max-height:80vh;overflow-y:auto;">
    <span class="close" onclick="closeShareModal()">&times;</span>
    <h3>📤 Share this post</h3>
    <div class="share-preview" id="sharePreview" style="max-height:300px;overflow-y:auto;"></div>
    <div class="share-buttons">
      <button onclick="copyToClipboard()">📋 Copy Link</button>
      <button onclick="shareOnTwitter()">🐦 Twitter</button>
      <button onclick="shareOnFacebook()">📘 Facebook</button>
      <button onclick="shareOnLinkedIn()">💼 LinkedIn</button>
    </div>
  </div>
</div>

<script>
const CURRENT_USER_ID   = '<?php echo $current_user_id; ?>';
const CURRENT_USER_NAME = '<?php echo $current_user_name; ?>';
const BASE_URL          = '<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>';
</script>
<script src="../../public/js/publications.js"></script>
</body>
</html>