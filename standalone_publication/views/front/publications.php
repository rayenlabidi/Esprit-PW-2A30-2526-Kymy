<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../controllers/PublicationC.php';

$current_user_id = 'current_user';
$current_user_name = 'You';
$current_user_init = 'YO';
$current_user_avatar = 'av-blue';

$controller = new PublicationC();
$posts = $controller->ListePublications();
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
    <li><a href="../front/messages.php" onclick="filterFeed('messages'); return false;">Messages</a></li>
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
          $comments = $controller->ListeComments($post['id']);
          $commentCount = 0;
          foreach($comments as $c) {
              $commentCount++;
              if(!empty($c['replies'])) {
                  $commentCount += count($c['replies']);
              }
          }
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
              <div class="pub-comment"><div class="pub-comment-content"><em>Loading comments...</em></div></div>
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
  <div class="modal-content share-modal" style="max-width: 500px; max-height: 80vh; overflow-y: auto;">
    <span class="close" onclick="closeShareModal()">&times;</span>
    <h3>📤 Share this post</h3>
    <div class="share-preview" id="sharePreview" style="max-height: 300px; overflow-y: auto;"></div>
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
</script>
<script src="../../public/js/publications.js"></script>
</body>
</html>