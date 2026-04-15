let pendingImg = null;
let currentSharePostId = null;
let currentShareContent = '';
let isLiking = false;
let isCommentLiking = false;
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
    formData.append('user_name', CURRENT_USER_NAME);
    formData.append('user_init', 'YO');
    formData.append('user_role', 'Freelancer');
    formData.append('user_avatar', 'av-blue');
    formData.append('content', content);
    
    fetch(BASE_URL, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            location.reload();
        } else {
            let errorMsg = 'Error: ';
            if (data.errors) {
                errorMsg += data.errors.join(', ');
            } else {
                errorMsg += 'Could not create post';
            }
            alert(errorMsg);
        }
    })
    .catch(function(error) {
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
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
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
            
            const likeSpan = document.querySelector('.like-count-' + postId);
            if (likeSpan) {
                likeSpan.textContent = data.likes;
            }
        }
    })
    .catch(function(error) {
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
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        isCommentLiking = false;
        if (data.success) {
            btn.classList.toggle('liked');
            const likeSpan = document.querySelector('.comment-like-count-' + commentId);
            if (likeSpan) {
                likeSpan.textContent = data.likes;
            }
            
            if (isLiked) {
                btn.style.color = 'var(--text-3)';
            } else {
                btn.style.color = 'var(--blue)';
            }
        }
    })
    .catch(function(error) {
        isCommentLiking = false;
        console.error('Error:', error);
    });
}

function toggleComments(postId) {
    const commentsSection = document.getElementById('comments-' + postId);
    if (commentsSection.style.display === 'none' || commentsSection.style.display === '') {
        commentsSection.style.display = 'block';
    } else {
        commentsSection.style.display = 'none';
    }
}

function addComment(postId) {
    const input = document.getElementById('comment-input-' + postId);
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
    formData.append('user_name', CURRENT_USER_NAME);
    formData.append('user_init', 'YO');
    formData.append('user_avatar', 'av-blue');
    formData.append('comment', comment);
    
    fetch(BASE_URL, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            input.value = '';
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Could not add comment'));
        }
    })
    .catch(function(error) {
        console.error('Error:', error);
    });
}

function addReply(postId, parentCommentId) {
    const input = document.getElementById('reply-input-' + parentCommentId);
    const comment = input.value.trim();
    
    if (!comment) {
        alert('Please enter a reply');
        return;
    }
    
    if (comment.length < 2) {
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
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            input.value = '';
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Could not add reply'));
        }
    })
    .catch(function(error) {
        console.error('Error:', error);
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
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.error || 'Could not edit comment'));
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
        });
    } else if (newText && newText.trim().length < 2) {
        alert('Comment must be at least 2 characters');
    }
}

function deleteComment(commentId) {
    if (confirm('Are you sure you want to delete this comment? This action cannot be undone!')) {
        const formData = new FormData();
        formData.append('action', 'delete_comment');
        formData.append('comment_id', commentId);
        
        fetch(BASE_URL, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: Could not delete comment');
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
        });
    }
}

function sharePost(postId, content) {
    currentSharePostId = postId;
    currentShareContent = content;
    const modal = document.getElementById('shareModal');
    const sharePreview = document.getElementById('sharePreview');
    if (sharePreview) {
        sharePreview.innerHTML = '<p>' + escapeHtml(content.substring(0, 150)) + (content.length > 150 ? '...' : '') + '</p>';
    }
    if (modal) modal.style.display = 'block';
}

function copyToClipboard() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(function() {
        alert('Link copied to clipboard!');
        closeShareModal();
    }).catch(function() {
        alert('Could not copy link. Please copy manually: ' + url);
    });
}

function shareOnTwitter() {
    const text = encodeURIComponent('Check out this post on Workify!');
    const url = encodeURIComponent(window.location.href);
    window.open('https://twitter.com/intent/tweet?text=' + text + '&url=' + url, '_blank', 'width=600,height=400');
    closeShareModal();
}

function shareOnFacebook() {
    const url = encodeURIComponent(window.location.href);
    window.open('https://www.facebook.com/sharer/sharer.php?u=' + url, '_blank', 'width=600,height=400');
    closeShareModal();
}

function shareOnLinkedIn() {
    const url = encodeURIComponent(window.location.href);
    window.open('https://www.linkedin.com/sharing/share-offsite/?url=' + url, '_blank', 'width=600,height=400');
    closeShareModal();
}

function closeShareModal() {
    const modal = document.getElementById('shareModal');
    if (modal) modal.style.display = 'none';
}

function editPost(postId) {
    const contentElement = document.getElementById('post-content-' + postId);
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
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + (data.errors ? data.errors.join(', ') : 'Could not update post'));
        }
    })
    .catch(function(error) {
        console.error('Error:', error);
    });
}

function deletePost(postId) {
    if (confirm('Are you sure you want to delete this post? This action cannot be undone!')) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', postId);
        formData.append('user_id', CURRENT_USER_ID);
        
        fetch(BASE_URL, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.errors ? data.errors.join(', ') : 'You can only delete your own posts'));
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
        });
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
    for (let i = 0; i < likeButtons.length; i++) {
        const btn = likeButtons[i];
        const postCard = btn.closest('.pub-post-card');
        if (postCard) {
            const postId = postCard.dataset.postId;
            postLikeStates[postId] = btn.classList.contains('liked');
        }
    }
});