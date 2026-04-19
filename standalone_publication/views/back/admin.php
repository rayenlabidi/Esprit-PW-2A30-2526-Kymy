<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../controllers/PublicationC.php';

$controller = new PublicationC();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $pub = new publication(
                    $_POST['user_name'],
                    $_POST['user_init'],
                    $_POST['user_role'],
                    $_POST['user_avatar'],
                    $_POST['content'],
                    $_POST['image_url'] ?? ''
                );
                $pub->setUserId('admin');
                $result = $controller->AddPublication($pub);
                if ($result) {
                    $message = 'Publication created successfully!';
                    header('Location: admin.php?success=1');
                    exit;
                } else {
                    $error = 'Error creating publication';
                }
                break;
            case 'update':
                $post = $controller->GetPublicationById($_POST['id']);
                if ($post) {
                    $pub = new publication('', '', '', '', $_POST['content']);
                    $result = $controller->UpdatePublication($pub, $_POST['id'], $post['user_id']);
                    if ($result) {
                        $message = 'Publication updated successfully!';
                        header('Location: admin.php?success=1');
                        exit;
                    } else {
                        $error = 'Update failed';
                    }
                } else {
                    $error = 'Publication not found';
                }
                break;
            case 'delete':
                $post = $controller->GetPublicationById($_POST['id']);
                if ($post) {
                    $result = $controller->DeletePublication($_POST['id'], $post['user_id']);
                    if ($result) {
                        $message = 'Publication deleted successfully!';
                        header('Location: admin.php?success=1');
                        exit;
                    } else {
                        $error = 'Error deleting publication';
                    }
                } else {
                    $error = 'Publication not found';
                }
                break;
            case 'delete_comment':
                $result = $controller->DeleteComment($_POST['comment_id']);
                if ($result) {
                    $message = 'Comment deleted successfully!';
                    header('Location: admin.php?success=1');
                    exit;
                } else {
                    $error = 'Error deleting comment';
                }
                break;
            case 'edit_comment':
                $result = $controller->EditComment($_POST['comment_id'], $_POST['comment'], $_POST['user_name']);
                if ($result) {
                    $message = 'Comment updated successfully!';
                    header('Location: admin.php?success=1');
                    exit;
                } else {
                    $error = 'Error updating comment';
                }
                break;
        }
    }
}

if (isset($_GET['success'])) {
    $message = 'Operation completed successfully!';
}

$posts = $controller->ListePublications();
$comments = $controller->ListeAllComments();
$editPost = null;
$editComment = null;

if (isset($_GET['edit'])) {
    $editPost = $controller->GetPublicationById($_GET['edit']);
}
if (isset($_GET['edit_comment'])) {
    foreach ($comments as $c) {
        if ($c['id'] == $_GET['edit_comment']) {
            $editComment = $c;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel - Complete CRUD | Workify</title>
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
    <h1>📋 Complete Publication Management</h1>
    <p>Total posts: <?php echo count($posts); ?> | Total comments: <?php echo count($comments); ?></p>
  </div>

  <?php if($message): ?>
    <div class="alert alert-success">✅ <?php echo htmlspecialchars($message); ?></div>
  <?php endif; ?>

  <?php if($error): ?>
    <div class="alert alert-error">❌ <?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>

  <div class="crud-section">
    <h2>➕ Create New Publication</h2>
    <form method="POST" action="" class="crud-form" id="createForm" enctype="multipart/form-data">
      <input type="hidden" name="action" value="create">
      <div class="form-row">
        <div class="form-group">
          <label>Name:</label>
          <input type="text" name="user_name" id="user_name">
          <small class="error-message" id="nameError"></small>
        </div>
        <div class="form-group">
          <label>Initials:</label>
          <input type="text" name="user_init" id="user_init" placeholder="SK">
          <small class="error-message" id="initError"></small>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Role:</label>
          <select name="user_role" id="user_role">
            <option value="Freelancer">Freelancer</option>
            <option value="Client">Client</option>
          </select>
        </div>
        <div class="form-group">
          <label>Avatar Color:</label>
          <select name="user_avatar" id="user_avatar">
            <option value="av-blue">Blue</option>
            <option value="av-green">Green</option>
            <option value="av-orange">Orange</option>
            <option value="av-purple">Purple</option>
            <option value="av-pink">Pink</option>
            <option value="av-teal">Teal</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label>Content:</label>
        <textarea name="content" id="create_content" rows="4"></textarea>
        <small class="error-message" id="contentError"></small>
        <small>Minimum 5 characters, maximum 5000 characters</small>
      </div>
      <div class="form-group">
        <label>Image (optional):</label>
        <input type="file" name="image" id="create_image" accept="image/*">
        <small class="error-message" id="imageError"></small>
        <div id="imagePreviewWrap" style="display:none; margin-top:10px;">
          <img id="imagePreview" style="max-width:200px; max-height:150px; border-radius:8px;">
        </div>
      </div>
      <button type="submit" class="btn-create">📝 Create Publication</button>
    </form>
  </div>

  <?php if($editPost): ?>
  <div class="crud-section">
    <h2>✏️ Edit Publication #<?php echo $editPost['id']; ?></h2>
    <form method="POST" action="" class="crud-form" id="editForm">
      <input type="hidden" name="action" value="update">
      <input type="hidden" name="id" value="<?php echo $editPost['id']; ?>">
      <div class="form-group">
        <label>Author:</label>
        <div class="author-info">
          <div class="wf-avatar wf-avatar-32 <?php echo $editPost['user_avatar']; ?>"><?php echo htmlspecialchars($editPost['user_init']); ?></div>
          <strong><?php echo htmlspecialchars($editPost['user_name']); ?></strong>
          <span class="role-badge"><?php echo $editPost['user_role']; ?></span>
        </div>
      </div>
      <div class="form-group">
        <label>Content:</label>
        <textarea name="content" id="edit_content" rows="6"><?php echo htmlspecialchars($editPost['content']); ?></textarea>
        <small class="error-message" id="editContentError"></small>
        <small>Minimum 5 characters, maximum 5000 characters</small>
      </div>
      <?php if($editPost['image_url']): ?>
      <div class="form-group">
        <label>Current Image:</label>
        <div>
          <img src="<?php echo htmlspecialchars($editPost['image_url']); ?>" style="max-width:200px; border-radius:8px;">
        </div>
      </div>
      <?php endif; ?>
      <div class="form-actions">
        <button type="submit" class="btn-update">💾 Update Publication</button>
        <a href="admin.php" class="btn-cancel">Cancel Edit</a>
      </div>
    </form>
  </div>
  <?php endif; ?>

  <?php if($editComment): ?>
  <div class="crud-section">
    <h2>✏️ Edit Comment #<?php echo $editComment['id']; ?></h2>
    <form method="POST" action="" class="crud-form" id="editCommentForm">
      <input type="hidden" name="action" value="edit_comment">
      <input type="hidden" name="comment_id" value="<?php echo $editComment['id']; ?>">
      <input type="hidden" name="user_name" value="<?php echo htmlspecialchars($editComment['user_name']); ?>">
      <div class="form-group">
        <label>Author:</label>
        <div class="author-info">
          <div class="wf-avatar wf-avatar-32 <?php echo $editComment['user_avatar']; ?>"><?php echo htmlspecialchars($editComment['user_init']); ?></div>
          <strong><?php echo htmlspecialchars($editComment['user_name']); ?></strong>
          <span>on post: <?php echo htmlspecialchars($editComment['post_author']); ?></span>
        </div>
      </div>
      <div class="form-group">
        <label>Comment:</label>
        <textarea name="comment" id="edit_comment_content" rows="4"><?php echo htmlspecialchars($editComment['comment']); ?></textarea>
        <small class="error-message" id="editCommentError"></small>
        <small>Minimum 2 characters, maximum 5000 characters</small>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn-update">💾 Update Comment</button>
        <a href="admin.php" class="btn-cancel">Cancel Edit</a>
      </div>
    </form>
  </div>
  <?php endif; ?>

  <div class="crud-section">
    <h2>📄 All Publications</h2>
    <div class="admin-table-wrapper">
      <table class="admin-table">
        <thead>
          <tr><th>ID</th><th>Author</th><th>Content</th><th>Likes</th><th>Image</th><th>Created</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php foreach($posts as $post): ?>
          <tr>
            <td><?php echo $post['id']; ?></td>
            <td>
              <div class="author-cell">
                <div class="wf-avatar wf-avatar-32 <?php echo $post['user_avatar']; ?>"><?php echo htmlspecialchars($post['user_init']); ?></div>
                <span><?php echo htmlspecialchars($post['user_name']); ?></span>
              </div>
            </td>
            <td class="content-cell"><?php echo htmlspecialchars(substr($post['content'], 0, 80)) . (strlen($post['content']) > 80 ? '...' : ''); ?></td>
            <td><?php echo $post['likes']; ?></td>
            <td>
              <?php if($post['image_url']): ?>
                <img src="<?php echo htmlspecialchars($post['image_url']); ?>" style="width:50px; height:50px; object-fit:cover; border-radius:5px;">
              <?php else: ?>
                -
              <?php endif; ?>
            </td>
            <td><?php echo date('M d, Y', strtotime($post['created_at'])); ?></td>
            <td>
              <div class="action-buttons">
                <a href="?edit=<?php echo $post['id']; ?>" class="btn-edit">✏️ Edit</a>
                <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Are you sure?')">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                  <button type="submit" class="btn-delete">🗑️ Delete</button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="crud-section">
    <h2>💬 All Comments</h2>
    <div class="admin-table-wrapper">
      <table class="admin-table">
        <thead>
          <tr><th>ID</th><th>User</th><th>Comment</th><th>On Post</th><th>Likes</th><th>Created</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php foreach($comments as $comment): ?>
          <tr>
            <td><?php echo $comment['id']; ?></td>
            <td>
              <div class="author-cell">
                <div class="wf-avatar wf-avatar-32 <?php echo $comment['user_avatar']; ?>"><?php echo htmlspecialchars($comment['user_init']); ?></div>
                <span><?php echo htmlspecialchars($comment['user_name']); ?></span>
              </div>
            </td>
            <td class="content-cell"><?php echo htmlspecialchars(substr($comment['comment'], 0, 80)) . (strlen($comment['comment']) > 80 ? '...' : ''); ?></td>
            <td><?php echo htmlspecialchars($comment['post_author']); ?></td>
            <td><?php echo $comment['likes']; ?></td>
            <td><?php echo date('M d, Y', strtotime($comment['created_at'])); ?></td>
            <td>
              <div class="action-buttons">
                <a href="?edit_comment=<?php echo $comment['id']; ?>" class="btn-edit">✏️ Edit</a>
                <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Delete this comment?')">
                  <input type="hidden" name="action" value="delete_comment">
                  <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                  <button type="submit" class="btn-delete">🗑️ Delete</button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="../../public/js/admin.js"></script>
</body>
</html>