document.addEventListener('DOMContentLoaded', function () {
    var forms = document.querySelectorAll('[data-validate]');

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
        validateText(form, 'nom', 3, 'Le nom doit contenir au moins 3 caracteres.', errors);
        validateEmail(form, 'email', 'Veuillez saisir un email valide.', errors);
        validatePhone(form, 'telephone', 'Le telephone doit contenir au moins 8 chiffres.', errors);
    }

    return errors;
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
    });
});
