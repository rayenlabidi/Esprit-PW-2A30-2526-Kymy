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

function validatePost(content) {
    if (!content || content.trim().length === 0) {
        alert('Please enter some content for your post');
        return false;
    }
    if (content.trim().length < 5) {
        alert('Content must be at least 5 characters long');
        return false;
    }
    if (content.trim().length > 5000) {
        alert('Content cannot exceed 5000 characters');
        return false;
    }
    return true;
}

function submitPost() {
    const textarea = document.getElementById('newPostText');
    const content = textarea.value.trim();
    
    if (!validatePost(content)) {
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
            
            let totalComments = 0;
            
            if (data.comments.length === 0) {
                commentsList.innerHTML = '<div class="pub-comment"><div class="pub-comment-content"><em>No comments yet. Be the first to comment!</em></div></div>';
            } else {
                data.comments.forEach(comment => {
                    totalComments++;
                    
                    let repliesHtml = '';
                    if (comment.replies && comment.replies.length > 0) {
                        repliesHtml = '<div class="comment-replies" style="margin-left: 50px; margin-top: 10px;">';
                        comment.replies.forEach(reply => {
                            totalComments++;
                            repliesHtml += `
                            <div class="pub-comment" data-comment-id="${reply.id}" style="margin-bottom: 8px;">
                                <div class="wf-avatar wf-avatar-32 ${reply.user_avatar}">${escapeHtml(reply.user_init)}</div>
                                <div class="pub-comment-content">
                                    <div class="pub-comment-author">${escapeHtml(reply.user_name)}</div>
                                    <div class="pub-comment-text">${escapeHtml(reply.comment).replace(/\n/g, '<br>')}</div>
                                    <div class="pub-comment-actions">
                                        <button class="comment-like-btn" onclick="toggleCommentLike(this, ${reply.id}, ${reply.likes})">
                                            👍 <span class="comment-like-count-${reply.id}">${reply.likes}</span>
                                        </button>
                                        ${reply.user_name == CURRENT_USER_NAME ? `
                                        <button class="comment-edit-btn" onclick="editComment(${reply.id}, '${escapeHtml(reply.comment)}')">✏️ Edit</button>
                                        <button class="comment-delete-btn" onclick="deleteComment(${reply.id})">🗑️ Delete</button>` : ''}
                                    </div>
                                </div>
                            </div>`;
                        });
                        repliesHtml += '</div>';
                    }
                    
                    const commentHtml = `
                    <div class="comment-wrapper" data-comment-id="${comment.id}">
                        <div class="pub-comment">
                            <div class="wf-avatar wf-avatar-32 ${comment.user_avatar}">${escapeHtml(comment.user_init)}</div>
                            <div class="pub-comment-content">
                                <div class="pub-comment-author">${escapeHtml(comment.user_name)}</div>
                                <div class="pub-comment-text" id="comment-text-${comment.id}">${escapeHtml(comment.comment).replace(/\n/g, '<br>')}</div>
                                <div class="pub-comment-actions">
                                    <button class="comment-like-btn" onclick="toggleCommentLike(this, ${comment.id}, ${comment.likes})">
                                        👍 <span class="comment-like-count-${comment.id}">${comment.likes}</span>
                                    </button>
                                    <button class="comment-reply-btn" onclick="showReplyForm(${postId}, ${comment.id})">💬 Reply</button>
                                    ${comment.user_name == CURRENT_USER_NAME ? `
                                    <button class="comment-edit-btn" onclick="editComment(${comment.id}, '${escapeHtml(comment.comment)}')">✏️ Edit</button>
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
                        </div>
                        ${repliesHtml}
                    </div>`;
                    commentsList.innerHTML += commentHtml;
                });
            }
            if (commentSpan) commentSpan.textContent = totalComments;
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
            loadComments(postId);
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
            loadComments(postId);
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
                const postCard = document.querySelector(`.comment-wrapper[data-comment-id="${commentId}"]`).closest('.pub-post-card');
                if (postCard) {
                    loadComments(postCard.dataset.postId);
                }
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
            if (data.success) {
                const commentWrapper = document.querySelector(`.comment-wrapper[data-comment-id="${commentId}"]`);
                if (commentWrapper) {
                    const postCard = commentWrapper.closest('.pub-post-card');
                    if (postCard) {
                        loadComments(postCard.dataset.postId);
                    }
                }
            } else {
                alert('Error deleting comment');
            }
        });
    }
}

function sharePost(postId, content, imageUrl) {
    const modal = document.getElementById('shareModal');
    const sharePreview = document.getElementById('sharePreview');
    let previewHtml = '<div style="max-height: 250px; overflow-y: auto;"><p>' + escapeHtml(content.substring(0, 200)) + (content.length > 200 ? '...' : '') + '</p>';
    if (imageUrl && imageUrl !== 'null' && imageUrl !== '') {
        previewHtml += '<img src="' + imageUrl + '" style="max-width:100%; max-height:200px; object-fit:contain; border-radius:8px; margin-top:10px;">';
    }
    previewHtml += '</div>';
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