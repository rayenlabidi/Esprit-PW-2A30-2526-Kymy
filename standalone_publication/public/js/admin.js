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

const createForm = document.getElementById('createForm');
if (createForm) {
    createForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const name = document.getElementById('user_name').value;
        const initials = document.getElementById('user_init').value;
        const content = document.getElementById('create_content').value;
        
        const nameValidation = validateName(name);
        const initialsValidation = validateInitials(initials);
        const contentValidation = validateContent(content);
        
        document.getElementById('nameError').textContent = '';
        document.getElementById('initError').textContent = '';
        document.getElementById('contentError').textContent = '';
        
        let isValid = true;
        
        if (!nameValidation.valid) {
            document.getElementById('nameError').textContent = nameValidation.message;
            document.getElementById('nameError').style.color = 'var(--red)';
            isValid = false;
        }
        
        if (!initialsValidation.valid) {
            document.getElementById('initError').textContent = initialsValidation.message;
            document.getElementById('initError').style.color = 'var(--red)';
            isValid = false;
        }
        
        if (!contentValidation.valid) {
            document.getElementById('contentError').textContent = contentValidation.message;
            document.getElementById('contentError').style.color = 'var(--red)';
            isValid = false;
        }
        
        if (isValid) {
            this.submit();
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
            document.getElementById('editContentError').style.color = 'var(--red)';
            return;
        }
        
        this.submit();
    });
}

const nameInput = document.getElementById('user_name');
const initialsInput = document.getElementById('user_init');
const contentInput = document.getElementById('create_content');

if (nameInput) {
    nameInput.addEventListener('input', function() {
        const validation = validateName(this.value);
        const errorSpan = document.getElementById('nameError');
        if (!validation.valid) {
            errorSpan.textContent = validation.message;
            errorSpan.style.color = 'var(--red)';
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
            errorSpan.style.color = 'var(--red)';
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
            errorSpan.style.color = 'var(--red)';
        } else {
            errorSpan.textContent = '';
        }
    });
}

function deletePublication(id) {
    if (confirm('Are you sure you want to delete this publication? This action cannot be undone!')) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);
        
        fetch(window.location.href, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting publication: ' + (data.errors ? data.errors.join(', ') : 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the publication');
        });
    }
}

const editContent = document.getElementById('edit_content');
if (editContent) {
    editContent.addEventListener('input', function() {
        const validation = validateContent(this.value);
        const errorSpan = document.getElementById('editContentError');
        if (!validation.valid) {
            errorSpan.textContent = validation.message;
            errorSpan.style.color = 'var(--red)';
        } else {
            errorSpan.textContent = '';
        }
    });
}