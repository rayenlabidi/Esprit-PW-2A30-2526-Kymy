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
    <li><a href="#">Home</a></li>
    <li><a href="#">Browse jobs</a></li>
    <li><a href="#" class="active">Publications</a></li>
    <li><a href="#">Messages</a></li>
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
        <a class="pub-nav-item active" href="#">Feed</a>
        <a class="pub-nav-item" href="#">Network</a>
        <a class="pub-nav-item" href="#">My Posts</a>
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
            <button class="pub-tool-btn" onclick="triggerImgUpload()">📷 Photo</button>
          </div>
          <button class="wf-btn wf-btn-primary" onclick="submitPost()">Post</button>
        </div>
        <input type="file" id="imgUploadInput" accept="image/*" style="display:none">
        <div id="imgPreviewWrap" style="display:none;"></div>
      </div>

      <div id="postsContainer">
        <?php foreach($posts as $post): 
          $comments = $controller->getComments($post['id']);
          $commentCount = count($comments);
          $isOwner = ($post['user_id'] == $current_user_id);
        ?>
        <div class="pub-post-card" data-post-id="<?php echo $post['id']; ?>">
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
          </div>
          
          <div class="pub-post-actions">
            <button class="pub-action-btn like-btn" onclick="toggleLike(this, <?php echo $post['id']; ?>, <?php echo $post['likes']; ?>)">
              👍 <span class="like-count-<?php echo $post['id']; ?>"><?php echo $post['likes']; ?></span> Likes
            </button>
            <button class="pub-action-btn" onclick="toggleComments(<?php echo $post['id']; ?>)">
              💬 <span class="comment-count-<?php echo $post['id']; ?>"><?php echo $commentCount; ?></span> Comments
            </button>
            <button class="pub-action-btn" onclick="sharePost(<?php echo $post['id']; ?>, '<?php echo htmlspecialchars(addslashes($post['content'])); ?>')">
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
</script>
<script src="../../public/js/publications.js"></script>
</body>
</html>