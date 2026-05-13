(function () {
    function ready(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback);
            return;
        }
        callback();
    }

    ready(function () {
        setupRipples();
        setupReveal();
        setupAnimatedInputs();
        setupFormationPulse();
        setupPointerBackground();
    });

    function setupRipples() {
        var targets = document.querySelectorAll('.btn, .public-nav a, .nav-link, .module-tile, .formation-card, .captcha-choice, .captcha-icon-btn');

        targets.forEach(function (target) {
            target.classList.add('mui-ripple-surface');
            target.addEventListener('pointerdown', function (event) {
                var rect = target.getBoundingClientRect();
                var ripple = document.createElement('span');
                var size = Math.max(rect.width, rect.height);

                ripple.className = 'mui-ripple';
                ripple.style.width = size + 'px';
                ripple.style.height = size + 'px';
                ripple.style.left = (event.clientX - rect.left - size / 2) + 'px';
                ripple.style.top = (event.clientY - rect.top - size / 2) + 'px';

                target.appendChild(ripple);
                window.setTimeout(function () {
                    ripple.remove();
                }, 650);
            });
        });
    }

    function setupReveal() {
        var animated = document.querySelectorAll('.hero-copy, .hero-board, .module-tile, .audience-card, .formation-card, .card, .table-box, .form-box, .detail-box');

        animated.forEach(function (item, index) {
            item.classList.add('mui-reveal');
            item.style.setProperty('--reveal-delay', Math.min(index * 45, 320) + 'ms');
        });

        if (!('IntersectionObserver' in window)) {
            animated.forEach(function (item) {
                item.classList.add('is-visible');
            });
            return;
        }

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        animated.forEach(function (item) {
            observer.observe(item);
        });
    }

    function setupAnimatedInputs() {
        var fields = document.querySelectorAll('input, select, textarea');

        fields.forEach(function (field) {
            field.addEventListener('focus', function () {
                var parent = field.closest('div');
                if (parent) {
                    parent.classList.add('mui-field-active');
                }
            });

            field.addEventListener('blur', function () {
                var parent = field.closest('div');
                if (parent) {
                    parent.classList.remove('mui-field-active');
                }
            });
        });
    }

    function setupFormationPulse() {
        var formationLinks = document.querySelectorAll('[data-module="formations"]');
        formationLinks.forEach(function (link) {
            link.addEventListener('mouseenter', function () {
                link.classList.add('formation-tab-pulse');
            });
            link.addEventListener('animationend', function () {
                link.classList.remove('formation-tab-pulse');
            });
        });
    }

    function setupPointerBackground() {
        var shell = document.querySelector('.front-shell');
        if (!shell || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        document.addEventListener('pointermove', function (event) {
            var x = Math.round((event.clientX / window.innerWidth) * 100);
            var y = Math.round((event.clientY / window.innerHeight) * 100);
            shell.style.setProperty('--pointer-x', x + '%');
            shell.style.setProperty('--pointer-y', y + '%');
        });
    }
})();
