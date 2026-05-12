function validateName(name) {
    if (!name || name.trim().length === 0) {
        return { valid: false, message: 'Name is required' };
    }
    if (name.trim().length < 2) {
        return { valid: false, message: 'Name must be at least 2 characters' };
    }
    if (name.trim().length > 100) {
        return { valid: false, message: 'Name cannot exceed 100 characters' };
    }
    return { valid: true, message: '' };
}

function validateInitials(initials) {
    if (!initials || initials.trim().length === 0) {
        return { valid: false, message: 'Initials are required' };
    }
    if (initials.trim().length > 5) {
        return { valid: false, message: 'Initials must be 1-5 characters' };
    }
    if (!/^[A-Za-z]+$/.test(initials.trim())) {
        return { valid: false, message: 'Initials must contain only letters' };
    }
    return { valid: true, message: '' };
}

function validateContent(content) {
    if (!content || content.trim().length === 0) {
        return { valid: false, message: 'Content cannot be empty' };
    }
    if (content.trim().length < 5) {
        return { valid: false, message: 'Content must be at least 5 characters' };
    }
    if (content.trim().length > 5000) {
        return { valid: false, message: 'Content cannot exceed 5000 characters' };
    }
    return { valid: true, message: '' };
}

function validateImage(file) {
    if (!file) return { valid: true, message: '' };
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        return { valid: false, message: 'Only JPG, PNG, GIF, WEBP images are allowed' };
    }
    if (file.size > 5000000) {
        return { valid: false, message: 'Image must be less than 5MB' };
    }
    return { valid: true, message: '' };
}

const createForm = document.getElementById('createForm');
if (createForm) {
    const imageInput = document.getElementById('create_image');
    const imagePreview = document.getElementById('imagePreview');
    const imagePreviewWrap = document.getElementById('imagePreviewWrap');
    
    if (imageInput) {
        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const validation = validateImage(this.files[0]);
                if (!validation.valid) {
                    document.getElementById('imageError').textContent = validation.message;
                    document.getElementById('imageError').style.color = '#dc2626';
                    this.value = '';
                    if (imagePreviewWrap) imagePreviewWrap.style.display = 'none';
                    return;
                }
                document.getElementById('imageError').textContent = '';
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreviewWrap.style.display = 'block';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
    
    createForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const name = document.getElementById('user_name').value;
        const initials = document.getElementById('user_init').value;
        const content = document.getElementById('create_content').value;
        const imageFile = document.getElementById('create_image') ? document.getElementById('create_image').files[0] : null;
        
        const nameValidation = validateName(name);
        const initialsValidation = validateInitials(initials);
        const contentValidation = validateContent(content);
        const imageValidation = validateImage(imageFile);
        
        document.getElementById('nameError').textContent = '';
        document.getElementById('initError').textContent = '';
        document.getElementById('contentError').textContent = '';
        document.getElementById('imageError').textContent = '';
        
        let isValid = true;
        
        if (!nameValidation.valid) {
            document.getElementById('nameError').textContent = nameValidation.message;
            document.getElementById('nameError').style.color = '#dc2626';
            isValid = false;
        }
        
        if (!initialsValidation.valid) {
            document.getElementById('initError').textContent = initialsValidation.message;
            document.getElementById('initError').style.color = '#dc2626';
            isValid = false;
        }
        
        if (!contentValidation.valid) {
            document.getElementById('contentError').textContent = contentValidation.message;
            document.getElementById('contentError').style.color = '#dc2626';
            isValid = false;
        }
        
        if (!imageValidation.valid) {
            document.getElementById('imageError').textContent = imageValidation.message;
            document.getElementById('imageError').style.color = '#dc2626';
            isValid = false;
        }
        
        if (isValid) {
            if (imageFile) {
                const imageFormData = new FormData();
                imageFormData.append('action', 'upload_image');
                imageFormData.append('image', imageFile);
                
                fetch(window.location.href, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: imageFormData
                })
                .then(response => response.json())
                .then(imageData => {
                    if (imageData.success) {
                        const formData = new FormData();
                        formData.append('action', 'create');
                        formData.append('user_id', 'admin');
                        formData.append('user_name', name);
                        formData.append('user_init', initials);
                        formData.append('user_role', document.getElementById('user_role').value);
                        formData.append('user_avatar', document.getElementById('user_avatar').value);
                        formData.append('content', content);
                        formData.append('image_url', imageData.filename);
                        
                        fetch(window.location.href, {
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
                        });
                    } else {
                        alert('Image upload failed: ' + imageData.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred during image upload');
                });
            } else {
                const formData = new FormData();
                formData.append('action', 'create');
                formData.append('user_id', 'admin');
                formData.append('user_name', name);
                formData.append('user_init', initials);
                formData.append('user_role', document.getElementById('user_role').value);
                formData.append('user_avatar', document.getElementById('user_avatar').value);
                formData.append('content', content);
                formData.append('image_url', '');
                
                fetch(window.location.href, {
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
        }
    });
}

const editForm = document.getElementById('editForm');
if (editForm) {
    editForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const content = document.getElementById('edit_content').value;
        const contentValidation = validateContent(content);
        
        document.getElementById('editContentError').textContent = '';
        
        if (!contentValidation.valid) {
            document.getElementById('editContentError').textContent = contentValidation.message;
            document.getElementById('editContentError').style.color = '#dc2626';
            return;
        }
        
        this.submit();
    });
}

const editCommentForm = document.getElementById('editCommentForm');
if (editCommentForm) {
    editCommentForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const comment = document.getElementById('edit_comment_content').value;
        
        document.getElementById('editCommentError').textContent = '';
        
        if (!comment || comment.trim().length < 2) {
            document.getElementById('editCommentError').textContent = 'Comment must be at least 2 characters';
            document.getElementById('editCommentError').style.color = '#dc2626';
            return;
        }
        
        if (comment.trim().length > 5000) {
            document.getElementById('editCommentError').textContent = 'Comment cannot exceed 5000 characters';
            document.getElementById('editCommentError').style.color = '#dc2626';
            return;
        }
        
        this.submit();
    });
}

const nameInput = document.getElementById('user_name');
const initialsInput = document.getElementById('user_init');
const contentInput = document.getElementById('create_content');
const imageInputField = document.getElementById('create_image');

if (nameInput) {
    nameInput.addEventListener('input', function() {
        const validation = validateName(this.value);
        const errorSpan = document.getElementById('nameError');
        if (!validation.valid) {
            errorSpan.textContent = validation.message;
            errorSpan.style.color = '#dc2626';
        } else {
            errorSpan.textContent = '';
        }
    });
}

if (initialsInput) {
    initialsInput.addEventListener('input', function() {
        const validation = validateInitials(this.value);
        const errorSpan = document.getElementById('initError');
        if (!validation.valid) {
            errorSpan.textContent = validation.message;
            errorSpan.style.color = '#dc2626';
        } else {
            errorSpan.textContent = '';
        }
    });
}

if (contentInput) {
    contentInput.addEventListener('input', function() {
        const validation = validateContent(this.value);
        const errorSpan = document.getElementById('contentError');
        if (!validation.valid) {
            errorSpan.textContent = validation.message;
            errorSpan.style.color = '#dc2626';
        } else {
            errorSpan.textContent = '';
        }
    });
}

if (imageInputField) {
    imageInputField.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const validation = validateImage(this.files[0]);
            const errorSpan = document.getElementById('imageError');
            if (!validation.valid) {
                errorSpan.textContent = validation.message;
                errorSpan.style.color = '#dc2626';
                this.value = '';
                const previewWrap = document.getElementById('imagePreviewWrap');
                if (previewWrap) previewWrap.style.display = 'none';
            } else {
                errorSpan.textContent = '';
            }
        }
    });
}

const editContentInput = document.getElementById('edit_content');
if (editContentInput) {
    editContentInput.addEventListener('input', function() {
        const validation = validateContent(this.value);
        const errorSpan = document.getElementById('editContentError');
        if (!validation.valid) {
            errorSpan.textContent = validation.message;
            errorSpan.style.color = '#dc2626';
        } else {
            errorSpan.textContent = '';
        }
    });
}

const editCommentContentInput = document.getElementById('edit_comment_content');
if (editCommentContentInput) {
    editCommentContentInput.addEventListener('input', function() {
        const value = this.value.trim();
        const errorSpan = document.getElementById('editCommentError');
        if (value.length === 0) {
            errorSpan.textContent = 'Comment cannot be empty';
            errorSpan.style.color = '#dc2626';
        } else if (value.length < 2) {
            errorSpan.textContent = 'Comment must be at least 2 characters';
            errorSpan.style.color = '#dc2626';
        } else if (value.length > 5000) {
            errorSpan.textContent = 'Comment cannot exceed 5000 characters';
            errorSpan.style.color = '#dc2626';
        } else {
            errorSpan.textContent = '';
        }
    });
}

// Toast notification function
function showAdminToast(message, type) {
    // Remove existing toasts
    const existing = document.querySelectorAll('.admin-toast');
    existing.forEach(t => t.remove());

    const toast = document.createElement('div');
    toast.className = 'admin-toast';
    toast.style.cssText = `
        position: fixed; top: 20px; right: 20px; z-index: 99999;
        padding: 14px 24px; border-radius: 10px; font-size: 14px; font-weight: 600;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15); animation: slideInToast 0.3s ease;
        display: flex; align-items: center; gap: 10px; max-width: 400px;
    `;
    if (type === 'success') {
        toast.style.background = '#10b981';
        toast.style.color = '#fff';
        toast.innerHTML = '✅ ' + message;
    } else {
        toast.style.background = '#ef4444';
        toast.style.color = '#fff';
        toast.innerHTML = '❌ ' + message;
    }
    document.body.appendChild(toast);

    // Add animation keyframes if not present
    if (!document.getElementById('toastAnimStyle')) {
        const style = document.createElement('style');
        style.id = 'toastAnimStyle';
        style.textContent = `
            @keyframes slideInToast {
                from { opacity: 0; transform: translateX(60px); }
                to { opacity: 1; transform: translateX(0); }
            }
            @keyframes slideOutToast {
                from { opacity: 1; transform: translateX(0); }
                to { opacity: 0; transform: translateX(60px); }
            }
        `;
        document.head.appendChild(style);
    }

    setTimeout(() => {
        toast.style.animation = 'slideOutToast 0.3s ease forwards';
        setTimeout(() => toast.remove(), 350);
    }, 2000);
}

// Delete Modal Logic
let deleteModalTarget = null;
let deleteModalId = null;

function adminDeletePublication(id) {
    deleteModalTarget = 'publication';
    deleteModalId = id;
    document.getElementById('deleteModalTitle').textContent = 'Delete Publication';
    document.getElementById('adminDeleteModal').style.display = 'block';
}

function adminDeleteComment(id) {
    deleteModalTarget = 'comment';
    deleteModalId = id;
    document.getElementById('deleteModalTitle').textContent = 'Delete Comment';
    document.getElementById('adminDeleteModal').style.display = 'block';
}

function closeAdminDeleteModal() {
    document.getElementById('adminDeleteModal').style.display = 'none';
    deleteModalTarget = null;
    deleteModalId = null;
}

const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
if (confirmDeleteBtn) {
    confirmDeleteBtn.addEventListener('click', function() {
        if (!deleteModalTarget || !deleteModalId) {
            showAdminToast('Error: No item selected for deletion', 'error');
            closeAdminDeleteModal();
            return;
        }

        const formData = new FormData();

        if (deleteModalTarget === 'publication') {
            formData.append('action', 'delete');
            formData.append('id', deleteModalId);
        } else if (deleteModalTarget === 'comment') {
            formData.append('action', 'delete_comment');
            formData.append('comment_id', deleteModalId);
        }

        // Disable button to prevent double-click
        this.disabled = true;
        this.textContent = 'Deleting...';

        // Use the admin.php URL directly (strip any query params)
        const adminUrl = window.location.pathname;

        fetch(adminUrl, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Server responded with status: ' + response.status);
            }
            return response.text();
        })
        .then(text => {
            console.log('Delete response:', text);
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error('Invalid JSON response:', text);
                throw new Error('Server returned invalid response');
            }
            if (data.success) {
                closeAdminDeleteModal();
                showAdminToast('Deleted successfully!', 'success');
                setTimeout(() => location.reload(), 800);
            } else {
                showAdminToast('Error: ' + (data.error || 'Could not delete item'), 'error');
                // Re-enable button
                confirmDeleteBtn.disabled = false;
                confirmDeleteBtn.textContent = 'Delete';
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            showAdminToast('Network error: ' + error.message, 'error');
            // Re-enable button
            confirmDeleteBtn.disabled = false;
            confirmDeleteBtn.textContent = 'Delete';
        });
    });
}

window.addEventListener('click', function(event) {
    const deleteModal = document.getElementById('adminDeleteModal');
    if (event.target == deleteModal) {
        closeAdminDeleteModal();
    }
});