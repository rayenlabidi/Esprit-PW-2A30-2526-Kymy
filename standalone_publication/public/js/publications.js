let pendingImg = null;
let currentSharePostId = null;
let currentShareContent = '';
let isLiking = false;
let postLikeStates = {};

function autoResize(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 200) + 'px';
}

function triggerImgUpload() {
    document.getElementById('imgUploadInput').click();
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
    
    const formData = new FormData();
    formData.append('action', 'create');
    formData.append('user_id', CURRENT_USER_ID);
    formData.append('user_name', 'You');
    formData.append('user_init', 'YO');
    formData.append('user_role', 'Freelancer');
    formData.append('user_avatar', 'av-blue');
    formData.append('content', content);
    formData.append('has_image', pendingImg ? '1' : '0');
    
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

function toggleLike(btn, postId, currentLikes) {
    if (isLiking) return;
    isLiking = true;
    
    const isCurrentlyLiked = postLikeStates[postId] || false;
    let newLikes;
    
    if (isCurrentlyLiked) {
        newLikes = currentLikes - 1;
    } else {
        newLikes = currentLikes + 1;
    }
    
    if (newLikes < 0) {
        newLikes = 0;
        isLiking = false;
        return;
    }
    
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
            
            if (!isCurrentlyLiked) {
                btn.classList.add('liked');
                btn.style.color = 'var(--blue)';
                btn.style.background = 'var(--blue-light)';
            } else {
                btn.classList.remove('liked');
                btn.style.color = 'var(--text-3)';
                btn.style.background = 'none';
            }
            
            const likeSpan = document.querySelector(`.like-count-${postId}`);
            if (likeSpan) {
                likeSpan.textContent = data.likes;
            }
        }
    })
    .catch(error => {
        isLiking = false;
        console.error('Error:', error);
    });
}

function toggleComments(postId) {
    const commentsSection = document.getElementById(`comments-${postId}`);
    if (commentsSection.style.display === 'none' || commentsSection.style.display === '') {
        commentsSection.style.display = 'block';
        loadComments(postId);
    } else {
        commentsSection.style.display = 'none';
    }
}

function addComment(postId) {
    const input = document.getElementById(`comment-input-${postId}`);
    const comment = input.value.trim();
    
    if (!comment) {
        alert('Please enter a comment');
        return;
    }
    
    if (comment.length < 2) {
        alert('Comment must be at least 2 characters');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'add_comment');
    formData.append('publication_id', postId);
    formData.append('user_name', 'You');
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
    })
    .catch(error => console.error('Error:', error));
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
            const commentsList = document.getElementById(`comments-list-${postId}`);
            const commentSpan = document.querySelector(`.comment-count-${postId}`);
            
            if (commentsList) {
                commentsList.innerHTML = '';
                
                if (data.comments.comments && data.comments.comments.length > 0) {
                    data.comments.comments.forEach(comment => {
                        const commentHtml = `
                            <div class="pub-comment">
                                <div class="wf-avatar wf-avatar-32 av-blue">${escapeHtml(comment.user.substring(0, 2))}</div>
                                <div class="pub-comment-content">
                                    <div class="pub-comment-author">${escapeHtml(comment.user)}</div>
                                    <div class="pub-comment-text">${escapeHtml(comment.comment)}</div>
                                </div>
                            </div>
                        `;
                        commentsList.innerHTML += commentHtml;
                    });
                } else {
                    commentsList.innerHTML = '<div class="pub-comment"><div class="pub-comment-content"><em>No comments yet. Be the first to comment!</em></div></div>';
                }
            }
            
            if (commentSpan) commentSpan.textContent = data.comments.count;
        }
    })
    .catch(error => console.error('Error:', error));
}

function sharePost(postId, content) {
    currentSharePostId = postId;
    currentShareContent = content;
    const modal = document.getElementById('shareModal');
    const sharePreview = document.getElementById('sharePreview');
    if (sharePreview) {
        sharePreview.innerHTML = `<p>${escapeHtml(content.substring(0, 150))}${content.length > 150 ? '...' : ''}</p>`;
    }
    if (modal) modal.style.display = 'block';
}

function copyToClipboard() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        alert('Link copied to clipboard!');
        closeShareModal();
    }).catch(() => {
        alert('Could not copy link. Please copy manually: ' + url);
    });
}

function shareOnTwitter() {
    const text = encodeURIComponent('Check out this post on Workify!');
    const url = encodeURIComponent(window.location.href);
    window.open(`https://twitter.com/intent/tweet?text=${text}&url=${url}`, '_blank', 'width=600,height=400');
    closeShareModal();
}

function shareOnFacebook() {
    const url = encodeURIComponent(window.location.href);
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
    closeShareModal();
}

function shareOnLinkedIn() {
    const url = encodeURIComponent(window.location.href);
    window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, '_blank', 'width=600,height=400');
    closeShareModal();
}

function closeShareModal() {
    const modal = document.getElementById('shareModal');
    if (modal) modal.style.display = 'none';
}

function editPost(postId) {
    const contentElement = document.getElementById(`post-content-${postId}`);
    if (contentElement) {
        const currentContent = contentElement.innerText;
        document.getElementById('editId').value = postId;
        document.getElementById('editContent').value = currentContent;
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
    if (newContent.length > 5000) {
        alert('Content cannot exceed 5000 characters');
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
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + (data.errors ? data.errors.join(', ') : 'Could not update post'));
        }
    })
    .catch(error => console.error('Error:', error));
}

function deletePost(postId) {
    if (confirm('Are you sure you want to delete this post? This action cannot be undone!')) {
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
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.errors ? data.errors.join(', ') : 'You can only delete your own posts'));
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

function showPostOptions(postId) {
    const choice = confirm('Edit this post?\n\nOK = Edit\nCancel = Delete');
    if (choice) {
        editPost(postId);
    } else {
        deletePost(postId);
    }
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
    if (event.target == editModal) {
        editModal.style.display = 'none';
    }
    if (event.target == shareModal) {
        shareModal.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('newPostText');
    if (textarea) {
        autoResize(textarea);
    }
    
    const likeButtons = document.querySelectorAll('.like-btn');
    likeButtons.forEach(btn => {
        const postCard = btn.closest('.pub-post-card');
        if (postCard) {
            const postId = postCard.dataset.postId;
            postLikeStates[postId] = btn.classList.contains('liked');
        }
    });
});