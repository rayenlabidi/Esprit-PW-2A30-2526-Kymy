document.addEventListener('DOMContentLoaded', function () {
    var forms = document.querySelectorAll('[data-validate]');
    setupCustomCaptchas();

    for (var i = 0; i < forms.length; i++) {
        forms[i].addEventListener('submit', function (event) {
            var errors = validateForm(this);

            if (errors.length > 0) {
                event.preventDefault();
                showErrors(this, errors);
            }
        });
    }
});

function validateForm(form) {
    clearErrors(form);

    var module = form.getAttribute('data-validate');
    var errors = [];

    validateCustomCaptcha(form, errors);

    if (module === 'formation') {
        validateText(form, 'titre', 3, 'Le titre doit contenir au moins 3 caracteres.', errors);
        validateText(form, 'description', 10, 'La description doit contenir au moins 10 caracteres.', errors);
        validateDate(form, 'date_debut', 'La date de debut doit etre au format YYYY-MM-DD.', errors);
        validateDate(form, 'date_fin', 'La date de fin doit etre au format YYYY-MM-DD.', errors);
        validateInteger(form, 'duree', 1, 'La duree doit etre un entier positif.', errors);
        validateInteger(form, 'places', 1, 'Le nombre de places doit etre un entier positif.', errors);
        validateNumber(form, 'prix', 0, 'Le prix doit etre un nombre positif.', errors);
        validateSelect(form, 'niveau', 'Veuillez choisir un niveau.', errors);
        validateSelect(form, 'statut', 'Veuillez choisir un statut.', errors);
        validateSelect(form, 'mode', 'Veuillez choisir un mode.', errors);
        validateSelect(form, 'id_categorie', 'Veuillez choisir une categorie.', errors);
        validateSelect(form, 'id_formateur', 'Veuillez choisir un formateur.', errors);

        if (getValue(form, 'date_debut') !== '' && getValue(form, 'date_fin') !== '') {
            if (new Date(getValue(form, 'date_fin')) < new Date(getValue(form, 'date_debut'))) {
                addFieldError(form, 'date_fin', 'La date de fin doit etre apres la date de debut.');
                errors.push('La date de fin doit etre apres la date de debut.');
            }
        }
    }

    if (module === 'inscription') {
        validatePhone(form, 'telephone', 'Le telephone doit contenir au moins 8 chiffres.', errors);
    }

    if (module === 'job') {
        validateText(form, 'titre', 4, 'Le titre doit contenir au moins 4 caracteres.', errors);
        validateText(form, 'description', 20, 'La description doit contenir au moins 20 caracteres.', errors);
        validateNumber(form, 'budget', 0, 'Le budget doit etre un nombre positif.', errors);
        validateSelect(form, 'id_categorie', 'Veuillez choisir une categorie.', errors);
        validateText(form, 'localisation', 2, 'La localisation doit contenir au moins 2 caracteres.', errors);
        validateSelect(form, 'type', 'Veuillez choisir un type.', errors);
        validateSelect(form, 'statut', 'Veuillez choisir un statut.', errors);
    }

    if (module === 'candidature') {
        validateText(form, 'message', 20, 'Le message doit contenir au moins 20 caracteres.', errors);
    }

    if (module === 'user') {
        validateSelect(form, 'role_id', 'Veuillez choisir un role.', errors);
        validateText(form, 'first_name', 2, 'Le prenom doit contenir au moins 2 caracteres.', errors);
        validateText(form, 'last_name', 2, 'Le nom doit contenir au moins 2 caracteres.', errors);
        validateEmail(form, 'email', 'Veuillez saisir un email valide.', errors);
        validateText(form, 'headline', 3, 'Le titre doit contenir au moins 3 caracteres.', errors);
        validateText(form, 'bio', 10, 'La bio doit contenir au moins 10 caracteres.', errors);
        validateSelect(form, 'status', 'Veuillez choisir un statut.', errors);

        if (form.elements.password && form.elements.password.value.trim() !== '' && form.elements.password.value.trim().length < 6) {
            addFieldError(form, 'password', 'Le mot de passe doit contenir au moins 6 caracteres.');
            errors.push('Le mot de passe doit contenir au moins 6 caracteres.');
        }
    }

    if (module === 'signup') {
        validateText(form, 'first_name', 2, 'Le prenom doit contenir au moins 2 caracteres.', errors);
        validateText(form, 'last_name', 2, 'Le nom doit contenir au moins 2 caracteres.', errors);
        validateEmail(form, 'email', 'Veuillez saisir un email valide.', errors);
        validateSelect(form, 'role', 'Veuillez choisir un type de compte.', errors);
        validateText(form, 'password', 8, 'Le mot de passe doit contenir au moins 8 caracteres.', errors);

        if (getValue(form, 'password') !== getValue(form, 'password_confirm')) {
            addFieldError(form, 'password_confirm', 'Les mots de passe ne correspondent pas.');
            errors.push('Les mots de passe ne correspondent pas.');
        }
    }

    if (module === 'contact') {
        validateText(form, 'full_name', 3, 'Le nom doit contenir au moins 3 caracteres.', errors);
        validateEmail(form, 'email', 'Veuillez saisir un email valide.', errors);
        validateText(form, 'subject', 4, 'Le sujet doit contenir au moins 4 caracteres.', errors);
        validateText(form, 'message', 20, 'Le message doit contenir au moins 20 caracteres.', errors);
    }

    return errors;
}

function setupCustomCaptchas() {
    var captchas = document.querySelectorAll('.workify-captcha');

    for (var i = 0; i < captchas.length; i++) {
        bindCaptcha(captchas[i]);
    }
}

function bindCaptcha(captcha) {
    if (captcha.getAttribute('data-captcha-bound') === '1') {
        return;
    }

    captcha.setAttribute('data-captcha-bound', '1');
    captcha.addEventListener('click', function (event) {
        var choice = event.target.closest('[data-captcha-choice]');
        var answer = captcha.querySelector('input[name="captcha_answer"]');

        if (choice && captcha.contains(choice)) {
            selectCaptchaChoice(captcha, choice, answer);
            return;
        }

        if (event.target.closest('[data-captcha-refresh]')) {
            refreshCaptcha(captcha);
            return;
        }

        if (event.target.closest('[data-captcha-audio]')) {
            speakCaptcha(captcha);
        }
    });
}

function selectCaptchaChoice(captcha, button, answer) {
    var choices = captcha.querySelectorAll('[data-captcha-choice]');

    for (var i = 0; i < choices.length; i++) {
        choices[i].classList.remove('is-selected');
        choices[i].setAttribute('aria-pressed', 'false');
    }

    button.classList.add('is-selected');
    button.setAttribute('aria-pressed', 'true');

    if (answer) {
        answer.value = button.getAttribute('data-captcha-choice') || '';
    }
}

function refreshCaptcha(captcha) {
    var scope = captcha.getAttribute('data-captcha-scope') || 'default';
    var refreshUrl = captcha.getAttribute('data-captcha-refresh-url') || 'CaptchaC.php';
    var url = refreshUrl + '?scope=' + encodeURIComponent(scope) + '&t=' + Date.now();

    captcha.classList.add('is-loading');

    fetch(url, { credentials: 'same-origin' })
        .then(function (response) {
            if (!response.ok) {
                throw new Error('captcha');
            }
            return response.json();
        })
        .then(function (data) {
            renderCaptcha(captcha, data);
        })
        .catch(function () {
            captcha.classList.add('has-refresh-error');
        })
        .finally(function () {
            captcha.classList.remove('is-loading');
        });
}

function renderCaptcha(captcha, data) {
    var prompt = captcha.querySelector('[data-captcha-prompt]');
    var id = captcha.querySelector('input[name="captcha_id"]');
    var answer = captcha.querySelector('input[name="captcha_answer"]');
    var options = captcha.querySelector('.captcha-options');

    if (prompt) {
        prompt.textContent = data.prompt || '';
    }

    if (id) {
        id.value = data.id || '';
    }

    if (answer) {
        answer.value = '';
    }

    if (!options || !data.choices) {
        return;
    }

    options.innerHTML = '';
    options.setAttribute('aria-label', data.prompt || '');

    for (var i = 0; i < data.choices.length; i++) {
        options.appendChild(createCaptchaChoice(data.choices[i]));
    }

    bindCaptcha(captcha);
}

function createCaptchaChoice(choice) {
    var button = document.createElement('button');
    var visual = document.createElement('span');
    var label = document.createElement('span');
    var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    var path = document.createElementNS('http://www.w3.org/2000/svg', 'path');

    button.className = 'captcha-choice';
    button.type = 'button';
    button.setAttribute('data-captcha-choice', choice.key || '');
    button.setAttribute('aria-pressed', 'false');

    visual.className = 'captcha-visual';
    visual.setAttribute('aria-hidden', 'true');
    svg.setAttribute('viewBox', '0 0 24 24');
    path.setAttribute('d', choice.icon || '');
    svg.appendChild(path);
    visual.appendChild(svg);

    label.textContent = choice.label || '';
    button.appendChild(visual);
    button.appendChild(label);

    return button;
}

function speakCaptcha(captcha) {
    if (!('speechSynthesis' in window) || typeof SpeechSynthesisUtterance === 'undefined') {
        return;
    }

    var prompt = captcha.querySelector('[data-captcha-prompt]');
    var text = prompt ? prompt.textContent : '';

    if (text === '') {
        return;
    }

    window.speechSynthesis.cancel();
    var utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = 'fr-FR';
    utterance.rate = 0.95;
    window.speechSynthesis.speak(utterance);
}

function validateCustomCaptcha(form, errors) {
    var captcha = form.querySelector('.workify-captcha');

    if (!captcha) {
        return;
    }

    var answer = captcha.querySelector('input[name="captcha_answer"]');
    if (!answer || answer.value.trim() === '') {
        captcha.classList.add('input-error');
        errors.push('Choisissez l image demandee par le captcha.');
    } else {
        captcha.classList.remove('input-error');
    }
}

function validateText(form, name, min, message, errors) {
    if (getValue(form, name).length < min) {
        addFieldError(form, name, message);
        errors.push(message);
    }
}

function validateSelect(form, name, message, errors) {
    if (getValue(form, name) === '') {
        addFieldError(form, name, message);
        errors.push(message);
    }
}

function validateDate(form, name, message, errors) {
    var value = getValue(form, name);
    var dateRegex = /^\d{4}-\d{2}-\d{2}$/;

    if (!dateRegex.test(value)) {
        addFieldError(form, name, message);
        errors.push(message);
    }
}

function validateNumber(form, name, min, message, errors) {
    var value = getValue(form, name);

    if (value === '' || isNaN(value) || parseFloat(value) < min) {
        addFieldError(form, name, message);
        errors.push(message);
    }
}

function validateEmail(form, name, message, errors) {
    var value = getValue(form, name);
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailRegex.test(value)) {
        addFieldError(form, name, message);
        errors.push(message);
    }
}

function validatePhone(form, name, message, errors) {
    var value = getValue(form, name);
    var phoneRegex = /^[0-9+\s-]{8,20}$/;

    if (!phoneRegex.test(value)) {
        addFieldError(form, name, message);
        errors.push(message);
    }
}

function validateInteger(form, name, min, message, errors) {
    var value = getValue(form, name);

    if (value === '' || isNaN(value) || parseInt(value, 10) < min || parseFloat(value) !== parseInt(value, 10)) {
        addFieldError(form, name, message);
        errors.push(message);
    }
}

function getValue(form, name) {
    if (!form.elements[name]) {
        return '';
    }

    return form.elements[name].value.trim();
}

function addFieldError(form, name, message) {
    var input = form.elements[name];

    if (!input) {
        return;
    }

    input.className += ' input-error';

    var span = document.createElement('span');
    span.className = 'error-message';
    span.innerHTML = message;
    input.parentNode.insertBefore(span, input.nextSibling);
}

function showErrors(form, errors) {
    var box = form.querySelector('.error-box');

    if (!box) {
        return;
    }

    var html = '<ul>';
    for (var i = 0; i < errors.length; i++) {
        html += '<li>' + errors[i] + '</li>';
    }
    html += '</ul>';
    box.innerHTML = html;
}

function clearErrors(form) {
    var box = form.querySelector('.error-box');
    var messages = form.querySelectorAll('.error-message');
    var inputs = form.querySelectorAll('.input-error');
    var i;

    if (box) {
        box.innerHTML = '';
    }

    for (i = 0; i < messages.length; i++) {
        messages[i].parentNode.removeChild(messages[i]);
    }

    for (i = 0; i < inputs.length; i++) {
        inputs[i].className = inputs[i].className.replace('input-error', '').trim();
    }
}

document.addEventListener('DOMContentLoaded', function () {
    var btn = document.getElementById('generatePlanBtn');
    var title = document.getElementById('titre');
    var description = document.getElementById('description');
    var duree = document.getElementById('duree');
    var imageUrl = document.getElementById('image_url');
    var imagePreview = document.getElementById('formationImagePreview');

    if (!btn || !title || !description || !duree) {
        return;
    }

    btn.addEventListener('click', function () {
        var courseTitle = title.value.trim();
        var hours = duree.value.trim();

        if (courseTitle === '') {
            courseTitle = 'cette formation';
        }

        if (hours === '' || isNaN(hours)) {
            hours = '12';
        }

        description.value =
            'Objectif general : Maitriser les bases de ' + courseTitle + '.\n\n' +
            'Programme propose :\n' +
            '- Introduction et objectifs de la formation\n' +
            '- Ateliers pratiques guides\n' +
            '- Mini-projet ou cas reel Workify\n' +
            '- Evaluation finale et feedback\n\n' +
            'Charge horaire estimee : ' + hours + ' heures.';

        if (imageUrl) {
            imageUrl.value = pickFormationImage(courseTitle);
            renderFormationImagePreview(imagePreview, imageUrl.value, courseTitle);
        }
    });
});

function pickFormationImage(title) {
    var text = String(title || '').toLowerCase();

    if (text.indexOf('mysql') !== -1 || text.indexOf('data') !== -1 || text.indexOf('sql') !== -1) {
        return 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=1200&q=80';
    }

    if (text.indexOf('ui') !== -1 || text.indexOf('ux') !== -1 || text.indexOf('design') !== -1) {
        return 'https://images.unsplash.com/photo-1559028012-481c04fa702d?auto=format&fit=crop&w=1200&q=80';
    }

    if (text.indexOf('marketing') !== -1 || text.indexOf('content') !== -1) {
        return 'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=1200&q=80';
    }

    if (text.indexOf('php') !== -1 || text.indexOf('mvc') !== -1 || text.indexOf('web') !== -1) {
        return 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?auto=format&fit=crop&w=1200&q=80';
    }

    return 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1200&q=80';
}

function renderFormationImagePreview(container, url, title) {
    if (!container || !url) {
        return;
    }

    container.innerHTML = '';
    var image = document.createElement('img');
    image.src = url;
    image.alt = title || 'Apercu formation';
    container.appendChild(image);
}
