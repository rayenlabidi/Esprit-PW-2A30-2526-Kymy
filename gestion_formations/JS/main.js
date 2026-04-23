document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.js-validate').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const fields = form.querySelectorAll('[data-label]');
            let hasErrors = false;

            fields.forEach((field) => {
                const errorHolder = field.parentElement.querySelector('.field-error');
                const label = field.dataset.label || 'Champ';
                const value = (field.value || '').trim();
                let message = '';

                if (field.dataset.required === '1' && value === '') {
                    message = `${label} est obligatoire.`;
                } else if (field.dataset.email === '1' && value !== '' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                    message = `${label} doit etre un email valide.`;
                } else if (field.dataset.minlength && value !== '' && value.length < Number(field.dataset.minlength)) {
                    message = `${label} doit contenir au moins ${field.dataset.minlength} caracteres.`;
                } else if (field.dataset.positive === '1' && value !== '' && Number(value) < 0) {
                    message = `${label} doit etre positif.`;
                }

                if (errorHolder) {
                    errorHolder.textContent = message;
                }

                field.classList.toggle('field-invalid', message !== '');

                if (message !== '') {
                    hasErrors = true;
                }
            });

            if (hasErrors) {
                event.preventDefault();
            }
        });
    });
});
