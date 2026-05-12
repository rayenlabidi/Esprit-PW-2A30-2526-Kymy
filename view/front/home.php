<?php
$pageTitle = 'Workify';
$office = 'front';
$activeModule = 'home';
include __DIR__ . '/../includes/header.php';
?>

<section class="hero-home">
    <div class="hero-copy">
        <p class="eyebrow">Workify access</p>
        <h2>Workify.</h2>
        <h3>Le nouvel espace simple et professionnel pour trouver une mission, publier une offre et collaborer avec les bons talents.</h3>
        <p class="muted">Workify rapproche clients, freelances et formateurs dans une interface claire, rapide et accessible: moins d'etapes, plus d'opportunites, et une experience pensee pour avancer avec confiance.</p>
        <div class="hero-actions">
            <a class="btn btn-primary glow-btn" href="../controller/AuthController.php?action=login">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 17l5-5-5-5v3H3v4h7v3zm2 4h8V3h-8v2h6v14h-6v2z"/></svg>
                Se connecter
            </a>
            <a class="btn glass-btn" href="../controller/JobC.php?office=front&action=list">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 4h4a2 2 0 0 1 2 2v2h4v12H4V8h4V6a2 2 0 0 1 2-2zm4 4V6h-4v2h4z"/></svg>
                Decouvrir Workify
            </a>
        </div>

        <div class="home-proof-grid">
            <article>
                <strong>Rapide</strong>
                <span>Publier une mission et recevoir les premiers profils sans complexite.</span>
            </article>
            <article>
                <strong>Simple</strong>
                <span>Freelance et client dans une experience unifiee.</span>
            </article>
            <article>
                <strong>Ouvert</strong>
                <span>Accessible aux nouveaux talents comme aux equipes confirmees.</span>
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
        <p class="muted">Find missions and apply quickly.</p>
    </a>
    <a class="module-tile" href="../controller/PublicationC.php?office=front&action=list">
        <span class="tile-icon tile-icon-teal"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16v2H4V5zm0 6h16v2H4v-2zm0 6h10v2H4v-2z"/></svg></span>
        <h3>Publication</h3>
        <p class="muted">Follow updates from the community.</p>
    </a>
    <a class="module-tile" href="#events">
        <span class="tile-icon tile-icon-amber"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 2h2v3h6V2h2v3h3v17H4V5h3V2zm11 8H6v10h12V10z"/></svg></span>
        <h3>Evenement</h3>
        <p class="muted">Reserved for the next module.</p>
    </a>
    <a class="module-tile" href="#messages">
        <span class="tile-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16v12H7l-3 4V4z"/></svg></span>
        <h3>Message</h3>
        <p class="muted">Messaging entry ready for integration.</p>
    </a>
    <a class="module-tile" href="../controller/FormationC.php?office=front&action=list">
        <span class="tile-icon tile-icon-teal"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16v14H7l-3 3V4zm4 4v2h8V8H8zm0 4v2h6v-2H8z"/></svg></span>
        <h3>Formation</h3>
        <p class="muted">Learn skills with guided tracks.</p>
    </a>
</section>

<section class="audience-band" id="events">
    <article class="audience-card">
        <p class="eyebrow">For freelancers</p>
        <h3>Build skills and apply with confidence.</h3>
        <p class="muted">Move between learning and job opportunities without leaving the platform.</p>
    </article>
    <article class="audience-card">
        <p class="eyebrow">For trainers</p>
        <h3>Showcase practical courses.</h3>
        <p class="muted">Create formations that connect directly with market needs and job categories.</p>
    </article>
    <article class="audience-card">
        <p class="eyebrow">For bosses</p>
        <h3>Find people already growing.</h3>
        <p class="muted">Post jobs, receive candidatures, and match with profiles shaped by training.</p>
    </article>
</section>

<section class="placeholder-band" id="messages">
    <div>
        <p class="eyebrow">Bientot disponible</p>
        <h2>Evenement et Message sont prets dans la navigation.</h2>
        <p class="muted">Les boutons existent deja pour garder la plateforme complete pendant l'integration des prochains modules.</p>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
