<?php
$pageTitle = 'Workify';
$office = 'front';
$activeModule = 'home';
include __DIR__ . '/../includes/header.php';

$contactErrors = isset($_SESSION['contact_errors']) ? $_SESSION['contact_errors'] : [];
$contactSuccess = isset($_SESSION['contact_success']) ? $_SESSION['contact_success'] : '';
$contactOld = isset($_SESSION['contact_old']) ? $_SESSION['contact_old'] : [
    'full_name' => '',
    'email' => '',
    'subject' => '',
    'message' => ''
];
unset($_SESSION['contact_errors'], $_SESSION['contact_success'], $_SESSION['contact_old']);
?>

<section class="hero-home premium-hero">
    <div class="hero-copy">
        <p class="eyebrow">Workify access</p>
        <h2>Workify.</h2>
        <h3>Une plateforme moderne pour apprendre, trouver une mission, publier une offre et collaborer avec les bons talents.</h3>
        <p class="muted">Workify rapproche freelances, formateurs et entreprises dans une experience rapide, claire et animee avec des parcours de formation, des opportunites et un suivi centralise.</p>
        <div class="hero-actions">
            <a class="btn btn-primary glow-btn" href="../controller/AuthController.php?action=signup">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 14c-2.7 0-8 1.4-8 4.2V20h16v-1.8c0-2.8-5.3-4.2-8-4.2zM15 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM6 10V7H3V5h3V2h2v3h3v2H8v3H6z"/></svg>
                Creer un compte
            </a>
            <a class="btn glass-btn" href="../controller/FormationC.php?office=front&action=list">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16v14H7l-3 3V4zm4 4v2h8V8H8zm0 4v2h6v-2H8z"/></svg>
                Explorer les formations
            </a>
            <a class="btn glass-btn" href="#contact">Parler a Workify</a>
        </div>

        <div class="home-proof-grid">
            <article>
                <strong><?= isset($stats['jobs']) ? (int) $stats['jobs'] : 0; ?>+</strong>
                <span>Missions et opportunites visibles depuis un meme espace.</span>
            </article>
            <article>
                <strong><?= isset($stats['formations']) ? (int) $stats['formations'] : 0; ?>+</strong>
                <span>Formations structurees pour progresser avec des objectifs clairs.</span>
            </article>
            <article>
                <strong>1</strong>
                <span>Compte unique pour candidater, apprendre et garder le contact.</span>
            </article>
        </div>
    </div>

    <div class="hero-board">
        <div class="hero-photo" aria-hidden="true"></div>
        <div class="opportunity-panel">
            <div class="panel-status"></div>
            <h3>Opportunites en direct</h3>
            <article class="opportunity-card">
                <span class="tile-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 4h4a2 2 0 0 1 2 2v2h4v12H4V8h4V6a2 2 0 0 1 2-2zm4 4V6h-4v2h4z"/></svg>
                </span>
                <div>
                    <strong>Application mobile e-commerce</strong>
                    <p class="muted">Client recherche un developpeur Flutter disponible cette semaine.</p>
                </div>
                <b>900 DT</b>
            </article>
            <article class="opportunity-card">
                <span class="tile-icon tile-icon-teal">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 17.2V21h3.8L17.9 9.9l-3.8-3.8L3 17.2zM20.7 7c.4-.4.4-1 0-1.4l-2.3-2.3a1 1 0 0 0-1.4 0l-1.8 1.8 3.8 3.8L20.7 7z"/></svg>
                </span>
                <div>
                    <strong>Identite visuelle pour startup</strong>
                    <p class="muted">Mission courte: logo, charte graphique et kit social media.</p>
                </div>
                <b>450 DT</b>
            </article>
            <div class="panel-badges">
                <span>Profils verifies</span>
                <span>Contact rapide</span>
                <span>Suivi clair</span>
            </div>
        </div>
    </div>
</section>

<section class="module-strip" aria-label="Workify modules">
    <a class="module-tile" href="../controller/JobC.php?office=front&action=list">
        <span class="tile-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 4h4a2 2 0 0 1 2 2v2h4v12H4V8h4V6a2 2 0 0 1 2-2zm4 4V6h-4v2h4z"/></svg></span>
        <h3>Jobs</h3>
        <p class="muted">Trouver des missions et postuler avec votre compte.</p>
    </a>
    <a class="module-tile" href="../controller/PublicationC.php?office=front&action=list">
        <span class="tile-icon tile-icon-teal"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16v2H4V5zm0 6h16v2H4v-2zm0 6h10v2H4v-2z"/></svg></span>
        <h3>Publication</h3>
        <p class="muted">Consulter les annonces et les nouveautes de la communaute.</p>
    </a>
    <a class="module-tile" href="#services">
        <span class="tile-icon tile-icon-amber"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 2h2v3h6V2h2v3h3v17H4V5h3V2zm11 8H6v10h12V10z"/></svg></span>
        <h3>Evenement</h3>
        <p class="muted">Suivre les rencontres, ateliers et sessions utiles.</p>
    </a>
    <a class="module-tile" href="../controller/MessageC.php?office=front&action=list">
        <span class="tile-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16v12H7l-3 4V4z"/></svg></span>
        <h3>Message</h3>
        <p class="muted">Centraliser les echanges importants de vos projets.</p>
    </a>
    <a class="module-tile" href="../controller/FormationC.php?office=front&action=list">
        <span class="tile-icon tile-icon-teal"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16v14H7l-3 3V4zm4 4v2h8V8H8zm0 4v2h6v-2H8z"/></svg></span>
        <h3>Formation</h3>
        <p class="muted">Developper vos competences avec des parcours guides.</p>
    </a>
</section>

<section class="site-section" id="services">
    <div class="section-heading">
        <p class="eyebrow">Services</p>
        <h2>Tout ce qu il faut pour avancer vite.</h2>
        <p class="muted">Des parcours lisibles, des candidatures propres, et une interface qui reste simple meme quand le projet grandit.</p>
    </div>
    <div class="feature-grid">
        <article class="feature-card">
            <span class="tile-icon"><svg viewBox="0 0 24 24"><path d="M10 4h4a2 2 0 0 1 2 2v2h4v12H4V8h4V6a2 2 0 0 1 2-2zm4 4V6h-4v2h4z"/></svg></span>
            <h3>Marketplace jobs</h3>
            <p class="muted">Publiez, filtrez et suivez les missions avec des candidatures rattachees aux comptes.</p>
        </article>
        <article class="feature-card">
            <span class="tile-icon tile-icon-teal"><svg viewBox="0 0 24 24"><path d="M4 4h16v14H7l-3 3V4zm4 4v2h8V8H8zm0 4v2h6v-2H8z"/></svg></span>
            <h3>Formations guidees</h3>
            <p class="muted">Catalogue, detail, inscription securisee et assistant visible uniquement sur les pages formation.</p>
        </article>
        <article class="feature-card">
            <span class="tile-icon tile-icon-amber"><svg viewBox="0 0 24 24"><path d="M12 2 2 7l10 5 10-5-10-5zm0 7.8L6.4 7 12 4.2 17.6 7 12 9.8zM4 10l8 4 8-4v3l-8 4-8-4v-3zm0 5 8 4 8-4v3l-8 4-8-4v-3z"/></svg></span>
            <h3>Espace prive</h3>
            <p class="muted">Un dashboard protege par session pour gerer utilisateurs, jobs, formations et contacts.</p>
        </article>
    </div>
</section>

<section class="site-section split-section" id="about">
    <div>
        <p class="eyebrow">A propos</p>
        <h2>Workify donne une structure claire aux opportunites.</h2>
        <p class="muted">Le site connecte trois besoins: les freelances cherchent des missions, les formateurs valorisent leurs parcours, et les entreprises trouvent des profils actifs. Le design reste coherent entre front office et espace prive.</p>
        <div class="mini-timeline">
            <span>01. Decouvrir</span>
            <span>02. Creer un compte</span>
            <span>03. Postuler ou s inscrire</span>
            <span>04. Suivre depuis l espace prive</span>
        </div>
    </div>
    <div class="about-panel">
        <strong>Premium MVC</strong>
        <p>PHP, sessions, PDO, requetes preparees, hash des mots de passe, formulaires valides et interface responsive.</p>
    </div>
</section>

<section class="site-section" id="projects">
    <div class="section-heading">
        <p class="eyebrow">Apercu projets</p>
        <h2>Des cas d usage concrets pour la demo.</h2>
    </div>
    <div class="project-grid">
        <article class="project-card">
            <div class="project-media media-one"></div>
            <h3>Plateforme PHP MVC</h3>
            <p class="muted">Gestion formations, jobs, comptes et candidatures dans une architecture claire.</p>
        </article>
        <article class="project-card">
            <div class="project-media media-two"></div>
            <h3>Interface learning</h3>
            <p class="muted">Catalogue formation anime, assistant dedie et inscription liee au compte.</p>
        </article>
        <article class="project-card">
            <div class="project-media media-three"></div>
            <h3>Back office securise</h3>
            <p class="muted">Dashboard reserve aux comptes autorises avec gestion et suivi des messages.</p>
        </article>
    </div>
</section>

<section class="site-section testimonial-section">
    <div class="section-heading">
        <p class="eyebrow">Temoignages</p>
        <h2>Une experience pensee pour plusieurs profils.</h2>
    </div>
    <div class="audience-band">
        <article class="audience-card">
            <p class="eyebrow">Freelance</p>
            <h3>Je comprends vite ou postuler.</h3>
            <p class="muted">Les formations et les jobs sont dans le meme univers, sans formulaire anonyme.</p>
        </article>
        <article class="audience-card">
            <p class="eyebrow">Formateur</p>
            <h3>Mes parcours sont mieux presentes.</h3>
            <p class="muted">Les cartes, filtres et details donnent un rendu professionnel et lisible.</p>
        </article>
        <article class="audience-card">
            <p class="eyebrow">Entreprise</p>
            <h3>Le suivi devient plus simple.</h3>
            <p class="muted">Les candidatures restent reliees aux comptes et faciles a verifier.</p>
        </article>
    </div>
</section>

<section class="site-section contact-section" id="contact">
    <div>
        <p class="eyebrow">Contact</p>
        <h2>Parlez-nous de votre besoin.</h2>
        <p class="muted">Une question, une demande de formation, ou une collaboration a preparer ? Envoyez un message depuis ce formulaire.</p>
        <div class="contact-list">
            <span>workifytn@gmail.com</span>
            <span>Tunis, Tunisie</span>
            <span>Reponse rapide depuis l espace prive</span>
        </div>
    </div>
    <form class="form-box contact-form" data-validate="contact" action="../controller/ContactC.php?action=send" method="post">
        <?php if (!empty($contactErrors)) { ?>
            <div class="error-box">
                <ul>
                    <?php foreach ($contactErrors as $contactError) { ?>
                        <li><?= htmlspecialchars($contactError, ENT_QUOTES); ?></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>
        <?php if ($contactSuccess !== '') { ?>
            <div class="success-box"><?= htmlspecialchars($contactSuccess, ENT_QUOTES); ?></div>
        <?php } ?>
        <div class="form-grid">
            <div>
                <label for="full_name">Nom complet</label>
                <input id="full_name" name="full_name" value="<?= htmlspecialchars($contactOld['full_name'], ENT_QUOTES); ?>" required>
            </div>
            <div>
                <label for="contact_email">Email</label>
                <input id="contact_email" name="email" type="email" value="<?= htmlspecialchars($contactOld['email'], ENT_QUOTES); ?>" required>
            </div>
            <div class="field-full">
                <label for="subject">Sujet</label>
                <input id="subject" name="subject" value="<?= htmlspecialchars($contactOld['subject'], ENT_QUOTES); ?>" required>
            </div>
            <div class="field-full">
                <label for="message">Message</label>
                <textarea id="message" name="message" required><?= htmlspecialchars($contactOld['message'], ENT_QUOTES); ?></textarea>
            </div>
        </div>
        <button class="btn btn-primary" type="submit">Envoyer le message</button>
    </form>
</section>

<section class="site-section faq-section" id="faq">
    <div class="section-heading">
        <p class="eyebrow">FAQ</p>
        <h2>Questions rapides.</h2>
    </div>
    <div class="faq-grid">
        <details open>
            <summary>Faut-il un compte pour postuler ?</summary>
            <p>Oui. Les candidatures et inscriptions aux formations sont reservees aux comptes connectes.</p>
        </details>
        <details>
            <summary>Qui peut ouvrir l espace prive ?</summary>
            <p>Seuls les comptes autorises par role peuvent acceder au dashboard de gestion.</p>
        </details>
        <details>
            <summary>Les messages de contact sont-ils stockes ?</summary>
            <p>Oui. Ils sont enregistres dans la base et consultables depuis l espace prive.</p>
        </details>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
