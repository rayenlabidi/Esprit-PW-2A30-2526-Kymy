let adminEditMessageId = null;

function showValidationModal(errors) {
    const errorList = document.getElementById('errorList');
    errorList.innerHTML = '';
    if (Array.isArray(errors)) {
        errors.forEach(error => {
            const li = document.createElement('li');
            li.textContent = error;
            errorList.appendChild(li);
        });
    } else {
        const li = document.createElement('li');
        li.textContent = errors;
        errorList.appendChild(li);
    }
    document.getElementById('validationModal').style.display = 'block';
}

function closeValidationModal() {
    document.getElementById('validationModal').style.display = 'none';
}

function validateMessage(content) {
    const errors = [];
    if (!content || content.trim().length === 0) {
        errors.push('Message cannot be empty');
    }
    if (content && content.trim().length > 5000) {
        errors.push('Message cannot exceed 5000 characters');
    }
    return errors;
}

function validateUser(userId, name, init) {
    const errors = [];
    if (!userId || userId.trim().length === 0) {
        errors.push('User ID is required');
    }
    if (!name || name.trim().length < 2) {
        errors.push('Name must be at least 2 characters');
    }
    if (!init || init.trim().length < 1 || init.trim().length > 5) {
        errors.push('Initials must be 1-5 characters');
    }
    if (init && !/^[A-Za-z]+$/.test(init.trim())) {
        errors.push('Initials must contain only letters');
    }
    return errors;
}

function showAdminMessage(msg, isError = false) {
    const alertDiv = document.getElementById(isError ? 'errorAlert' : 'messageAlert');
    alertDiv.innerHTML = `<div class="alert ${isError ? 'alert-error' : 'alert-success'}">${isError ? '❌' : '✅'} ${msg}</div>`;
    setTimeout(() => {
        alertDiv.innerHTML = '';
    }, 3000);
}

function adminAddUser() {
    const userId = document.getElementById('adminUserId').value.trim();
    const name = document.getElementById('adminUserName').value.trim();
    const init = document.getElementById('adminUserInit').value.trim().toUpperCase();
    const avatar = document.getElementById('adminUserAvatar').value;
    const role = document.getElementById('adminUserRole').value;
    
    const errors = validateUser(userId, name, init);
    if (errors.length > 0) {
        showValidationModal(errors);
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'add_user');
    formData.append('user_id', userId);
    formData.append('name', name);
    formData.append('init', init);
    formData.append('avatar', avatar);
    formData.append('role', role);
    
    fetch(window.location.href, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAdminMessage('User added successfully!');
            setTimeout(() => location.reload(), 1500);
        } else {
            showValidationModal([data.error || 'Error adding user']);
        }
    });
}

function adminEditMessage(id, content) {
    adminEditMessageId = id;
    document.getElementById('adminEditContent').value = content;
    document.getElementById('adminEditModal').style.display = 'block';
}

function adminSaveEditMessage() {
    const newContent = document.getElementById('adminEditContent').value.trim();
    const errors = validateMessage(newContent);
    
    if (errors.length > 0) {
        showValidationModal(errors);
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'edit_message_admin');
    formData.append('message_id', adminEditMessageId);
    formData.append('content', newContent);
    
    fetch(window.location.href, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeAdminEditModal();
            showAdminMessage('Message updated successfully!');
            setTimeout(() => location.reload(), 1000);
        } else {
            showValidationModal([data.error || 'Error updating message']);
        }
    });
}

function adminDeleteMessage(id) {
    if (confirm('Are you sure you want to delete this message?')) {
        const formData = new FormData();
        formData.append('action', 'delete_message_admin');
        formData.append('message_id', id);
        
        fetch(window.location.href, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAdminMessage('Message deleted successfully!');
                setTimeout(() => location.reload(), 1000);
            } else {
                showValidationModal(['Error deleting message']);
            }
        });
    }
}

function closeAdminEditModal() {
    document.getElementById('adminEditModal').style.display = 'none';
    adminEditMessageId = null;
}

window.onclick = function(event) {
    const modal = document.getElementById('adminEditModal');
    const validationModal = document.getElementById('validationModal');
    if (event.target == modal) {
        closeAdminEditModal();
    }
    if (event.target == validationModal) {
        closeValidationModal();
    }
}