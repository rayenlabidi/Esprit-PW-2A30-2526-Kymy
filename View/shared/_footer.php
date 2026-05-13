<?php
/**
 * Public footer shared by Events pages.
 */
if (!function_exists('workifyIcon')) {
    function workifyIcon(string $path): string
    {
        return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="' . htmlspecialchars($path, ENT_QUOTES) . '"/></svg>';
    }
}
?>
<footer class="site-footer events-site-footer">
    <div class="footer-grid">
        <div>
            <a class="public-brand footer-brand" href="../../controller/HomeC.php">
                <span class="brand-mark brand-briefcase" aria-hidden="true">
                    <?= workifyIcon('M10 5h4a2 2 0 0 1 2 2v2h4v10H4V9h4V7a2 2 0 0 1 2-2zm4 4V7h-4v2h4zm-8 4v4h12v-4h-3v2H9v-2H6z'); ?>
                </span>
                <span class="brand-text">Workify</span>
            </a>
            <p class="muted">Une plateforme professionnelle pour apprendre, recruter, postuler et collaborer avec une experience fluide.</p>
        </div>
        <div>
            <h3>Navigation</h3>
            <a href="../../controller/HomeC.php#services">Services</a>
            <a href="../../controller/HomeC.php#about">A propos</a>
            <a href="../../controller/HomeC.php#projects">Projets</a>
            <a href="../../controller/HomeC.php#contact">Contact</a>
        </div>
        <div>
            <h3>Modules</h3>
            <a href="#">Jobs</a>
            <a href="../../controller/FormationC.php?office=front&action=list">Formations</a>
            <a href="../../feed/public/index.php">Publications</a>
            <a href="index.php">Evenements</a>
        </div>
        <div>
            <h3>Contact</h3>
            <span>workifytn@gmail.com</span>
            <span>Tunis, Tunisie</span>
            <div class="social-links">
                <a href="../../controller/HomeC.php#contact">LinkedIn</a>
                <a href="../../controller/HomeC.php#contact">Instagram</a>
                <a href="../../controller/HomeC.php#contact">Facebook</a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <span>&copy; <?= date('Y'); ?> Workify. Tous droits reserves.</span>
        <a href="../admin/index.php">Espace admin</a>
    </div>
</footer>
