<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../controllers/PublicationController.php';

$controller = new PublicationController();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $_POST['user_id'] = 'admin';
                $result = $controller->create($_POST);
                if ($result['success']) {
                    $message = 'Publication created successfully!';
                    header('Location: admin.php?success=1');
                    exit;
                } else {
                    $error = implode(', ', $result['errors']);
                }
                break;
            case 'update':
                $post = $controller->getOne($_POST['id']);
                if ($post) {
                    $result = $controller->update($_POST['id'], $_POST['content'], $post['user_id']);
                    if ($result['success']) {
                        $message = 'Publication updated successfully!';
                        header('Location: admin.php?success=1');
                        exit;
                    } else {
                        $error = implode(', ', $result['errors']);
                    }
                } else {
                    $error = 'Publication not found';
                }
                break;
            case 'delete':
                $post = $controller->getOne($_POST['id']);
                if ($post) {
                    $result = $controller->delete($_POST['id'], $post['user_id']);
                    if ($result['success']) {
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
                $result = $controller->deleteComment($_POST['comment_id']);
                if ($result['success']) {
                    $message = 'Comment deleted successfully!';
                    header('Location: admin.php?success=1');
                    exit;
                } else {
                    $error = 'Error deleting comment';
                }
                break;
            case 'edit_comment':
                $result = $controller->editComment($_POST['comment_id'], $_POST['comment'], $_POST['user_name']);
                if ($result['success']) {
                    $message = 'Comment updated successfully!';
                    header('Location: admin.php?success=1');
                    exit;
                } else {
                    $error = $result['error'] ?? 'Error updating comment';
                }
                break;
        }
    }
}

if (isset($_GET['success'])) {
    $message = 'Operation completed successfully!';
}

$posts = $controller->getAll();
$comments = $controller->getAllComments();
$editPost = null;
$editComment = null;

if (isset($_GET['edit'])) {
    $editPost = $controller->getOne($_GET['edit']);
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
    <form method="POST" action="" class="crud-form" id="createForm">
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
          <tr><th>ID</th><th>Author</th><th>Content</th><th>Likes</th><th>Created</th><th>Actions</th></tr>
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
            </div>
            <td class="content-cell"><?php echo htmlspecialchars(substr($post['content'], 0, 80)) . (strlen($post['content']) > 80 ? '...' : ''); ?></td>
            <td><?php echo $post['likes']; ?></td>
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

<script>
const createForm = document.getElementById('createForm');
if (createForm) {
    createForm.addEventListener('submit', function(e) {
        const name = document.getElementById('user_name').value.trim();
        const initials = document.getElementById('user_init').value.trim();
        const content = document.getElementById('create_content').value.trim();
        
        document.getElementById('nameError').textContent = '';
        document.getElementById('initError').textContent = '';
        document.getElementById('contentError').textContent = '';
        
        let isValid = true;
        
        if (name.length < 2) {
            document.getElementById('nameError').textContent = 'Name must be at least 2 characters';
            isValid = false;
        }
        
        if (initials.length === 0 || initials.length > 5) {
            document.getElementById('initError').textContent = 'Initials must be 1-5 characters';
            isValid = false;
        }
        
        if (content.length === 0) {
            document.getElementById('contentError').textContent = 'Content cannot be empty';
            isValid = false;
        } else if (content.length < 5) {
            document.getElementById('contentError').textContent = 'Content must be at least 5 characters';
            isValid = false;
        } else if (content.length > 5000) {
            document.getElementById('contentError').textContent = 'Content cannot exceed 5000 characters';
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
}

const editForm = document.getElementById('editForm');
if (editForm) {
    editForm.addEventListener('submit', function(e) {
        const content = document.getElementById('edit_content').value.trim();
        
        document.getElementById('editContentError').textContent = '';
        
        if (content.length === 0) {
            document.getElementById('editContentError').textContent = 'Content cannot be empty';
            e.preventDefault();
        } else if (content.length < 5) {
            document.getElementById('editContentError').textContent = 'Content must be at least 5 characters';
            e.preventDefault();
        } else if (content.length > 5000) {
            document.getElementById('editContentError').textContent = 'Content cannot exceed 5000 characters';
            e.preventDefault();
        }
    });
}

const editCommentForm = document.getElementById('editCommentForm');
if (editCommentForm) {
    editCommentForm.addEventListener('submit', function(e) {
        const comment = document.getElementById('edit_comment_content').value.trim();
        
        document.getElementById('editCommentError').textContent = '';
        
        if (comment.length === 0) {
            document.getElementById('editCommentError').textContent = 'Comment cannot be empty';
            e.preventDefault();
        } else if (comment.length < 2) {
            document.getElementById('editCommentError').textContent = 'Comment must be at least 2 characters';
            e.preventDefault();
        } else if (comment.length > 5000) {
            document.getElementById('editCommentError').textContent = 'Comment cannot exceed 5000 characters';
            e.preventDefault();
        }
    });
}
</script>
</body>
</html>