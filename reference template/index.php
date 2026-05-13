<?php
// Vue publique: page d'introduction de l'application Workify.
include_once 'header.php';
?>

<style>
    body {
        background:
            linear-gradient(135deg, rgba(248, 251, 255, 0.92), rgba(236, 245, 255, 0.86)),
            url('https://images.unsplash.com/photo-1556761175-b413da4baf72?auto=format&fit=crop&w=1800&q=80') center/cover fixed;
    }

    .card.mb-5 {
        border: 0;
        border-radius: 28px;
        background:
            linear-gradient(145deg, rgba(255, 255, 255, 0.96), rgba(247, 251, 255, 0.92));
        box-shadow: 0 32px 80px rgba(15, 23, 42, 0.12);
    }

    .intro-shell {
        display: grid;
        grid-template-columns: minmax(0, 1.02fr) minmax(320px, 0.98fr);
        gap: 42px;
        align-items: stretch;
        min-height: 560px;
    }

    .intro-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 13px;
        border-radius: 999px;
        background: rgba(20, 184, 166, 0.11);
        color: #0f766e;
        font-size: 0.82rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .intro-title {
        max-width: 720px;
        margin: 20px 0 18px;
        color: #101827;
        font-size: clamp(2.35rem, 4.4vw, 4.65rem);
        line-height: 1.02;
        font-weight: 800;
        letter-spacing: 0;
    }

    .intro-text {
        color: #43536a;
        font-size: 1.12rem;
        line-height: 1.72;
        max-width: 690px;
    }

    .intro-lead {
        color: #0f172a;
        font-size: clamp(1.16rem, 2vw, 1.42rem);
        font-weight: 750;
        line-height: 1.45;
    }

    .intro-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 30px;
    }

    .intro-actions .btn {
        min-height: 48px;
        border-radius: 14px;
        font-weight: 800;
    }

    .intro-actions .btn-light {
        border: 1px solid rgba(148, 163, 184, 0.34);
        color: #0f172a;
        box-shadow: 0 12px 25px rgba(15, 23, 42, 0.06);
    }

    .intro-trust-row {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-top: 34px;
    }

    .intro-trust {
        min-height: 104px;
        padding: 18px;
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.72);
    }

    .intro-trust strong {
        display: block;
        color: #0f172a;
        font-size: 1.52rem;
        line-height: 1;
    }

    .intro-trust span {
        display: block;
        margin-top: 8px;
        color: #64748b;
        font-size: 0.88rem;
        font-weight: 650;
    }

    .intro-preview {
        position: relative;
        display: grid;
        align-content: end;
        min-height: 100%;
        padding: 20px;
        border: 1px solid rgba(148, 163, 184, 0.26);
        border-radius: 26px;
        background:
            linear-gradient(180deg, rgba(15, 23, 42, 0.04), rgba(15, 23, 42, 0.72)),
            url('https://images.unsplash.com/photo-1551434678-e076c223a692?auto=format&fit=crop&w=1100&q=80') center/cover;
        box-shadow: 0 28px 60px rgba(15, 23, 42, 0.2);
        overflow: hidden;
    }

    .intro-market-card {
        position: relative;
        z-index: 1;
        display: grid;
        gap: 16px;
        padding: 20px;
        border: 1px solid rgba(255, 255, 255, 0.45);
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.88);
        backdrop-filter: blur(18px);
        box-shadow: 0 22px 45px rgba(15, 23, 42, 0.16);
    }

    .market-card-head,
    .market-row,
    .market-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .market-card-head h2 {
        margin: 0;
        color: #0f172a;
        font-size: 1.2rem;
        font-weight: 800;
    }

    .market-pulse {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: #15803d;
        font-size: 0.82rem;
        font-weight: 800;
    }

    .market-pulse::before {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: #22c55e;
        box-shadow: 0 0 0 6px rgba(34, 197, 94, 0.14);
    }

    .market-row {
        padding: 14px;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 16px;
        background: #fff;
    }

    .market-icon {
        display: grid;
        place-items: center;
        flex: 0 0 44px;
        width: 44px;
        height: 44px;
        border-radius: 14px;
        color: #fff;
        background: linear-gradient(135deg, #2563eb, #14b8a6);
    }

    .market-content {
        min-width: 0;
        flex: 1;
    }

    .market-content strong {
        display: block;
        color: #111827;
        font-weight: 800;
    }

    .market-content span {
        display: block;
        color: #64748b;
        font-size: 0.88rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .market-price {
        color: #0f766e;
        font-weight: 800;
        white-space: nowrap;
    }

    .market-meta {
        justify-content: flex-start;
        flex-wrap: wrap;
    }

    .market-chip {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 11px;
        border-radius: 999px;
        color: #334155;
        background: #f1f5f9;
        font-size: 0.8rem;
        font-weight: 750;
    }

    .intro-module-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-top: 26px;
    }

    .intro-module {
        min-height: 210px;
        padding: 22px;
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.76);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .intro-module:hover {
        transform: translateY(-3px);
        box-shadow: 0 18px 35px rgba(15, 23, 42, 0.08);
    }

    .intro-module i {
        display: inline-grid;
        place-items: center;
        width: 44px;
        height: 44px;
        margin-bottom: 18px;
        border-radius: 14px;
        color: #ffffff;
        background: #2563eb;
        font-size: 1.05rem;
    }

    .intro-module:nth-child(2) i {
        background: #0f766e;
    }

    .intro-module:nth-child(3) i {
        background: #f97316;
    }

    .intro-module h3 {
        color: #0f172a;
        font-size: 1.06rem;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .intro-module p {
        color: #64748b;
        font-size: 0.94rem;
        line-height: 1.62;
        margin: 0;
    }

    .site-section {
        margin-top: 28px;
        padding: 34px;
        border: 1px solid rgba(148, 163, 184, 0.22);
        border-radius: 26px;
        background: rgba(255, 255, 255, 0.72);
    }

    .section-kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
        color: #0f766e;
        font-size: 0.78rem;
        font-weight: 850;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .site-section h2 {
        margin: 0 0 12px;
        color: #0f172a;
        font-size: clamp(1.8rem, 3vw, 2.75rem);
        font-weight: 850;
        letter-spacing: 0;
    }

    .site-section p {
        color: #64748b;
        line-height: 1.72;
    }

    .about-grid,
    .contact-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.1fr) minmax(280px, 0.9fr);
        gap: 24px;
        align-items: stretch;
    }

    .about-list,
    .contact-list {
        display: grid;
        gap: 12px;
        margin: 20px 0 0;
        padding: 0;
        list-style: none;
    }

    .about-list li,
    .contact-item {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        padding: 14px;
        border-radius: 16px;
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.9);
    }

    .about-list i,
    .contact-item i {
        display: inline-grid;
        place-items: center;
        flex: 0 0 36px;
        width: 36px;
        height: 36px;
        border-radius: 12px;
        color: #ffffff;
        background: linear-gradient(135deg, #2563eb, #14b8a6);
    }

    .about-visual {
        min-height: 340px;
        border-radius: 22px;
        background:
            linear-gradient(180deg, rgba(15, 23, 42, 0.08), rgba(15, 23, 42, 0.62)),
            url('https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=1000&q=80') center/cover;
        box-shadow: inset 0 -80px 100px rgba(15, 23, 42, 0.18);
    }

    .contact-panel {
        display: grid;
        align-content: center;
        padding: 26px;
        border-radius: 22px;
        color: #ffffff;
        background:
            linear-gradient(135deg, rgba(29, 78, 216, 0.94), rgba(15, 118, 110, 0.92)),
            url('https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=900&q=80') center/cover;
        box-shadow: 0 24px 45px rgba(15, 23, 42, 0.16);
    }

    .contact-panel h3 {
        margin: 0 0 10px;
        font-size: 1.5rem;
        font-weight: 850;
    }

    .contact-panel p {
        color: rgba(255, 255, 255, 0.86);
    }

    .contact-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 18px;
    }

    .contact-actions .btn {
        border-radius: 14px;
        font-weight: 800;
    }

    @media (max-width: 991.98px) {
        .intro-shell {
            grid-template-columns: 1fr;
            min-height: auto;
        }

        .intro-preview {
            min-height: 520px;
        }

        .intro-module-grid {
            grid-template-columns: 1fr;
        }

        .intro-trust-row {
            grid-template-columns: 1fr;
        }

        .about-grid,
        .contact-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 575.98px) {
        .card-body.p-4 {
            padding: 1.35rem !important;
        }

        .intro-preview {
            min-height: 430px;
            padding: 12px;
        }

        .market-row {
            align-items: flex-start;
        }
    }
</style>

<section id="home" class="intro-shell">
    <div>
        <span class="intro-eyebrow"><i class="fa-solid fa-bolt"></i> Plateforme freelance innovante</span>
        <h1 class="intro-title">Bienvenue sur Workify.</h1>
        <p class="intro-lead">
            Le nouvel espace simple et professionnel pour trouver une mission, publier une offre
            et collaborer avec les bons talents.
        </p>
        <p class="intro-text mb-0">
            Workify rapproche clients et freelances dans une interface claire, rapide et accessible:
            moins d'étapes, plus d'opportunités, et une expérience pensée pour avancer avec confiance.
        </p>

        <div class="intro-actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="listeUtilisateurs.php" class="btn btn-primary px-4 py-2">
                    <i class="fa-solid fa-table-columns me-2"></i>Ouvrir mon espace
                </a>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary px-4 py-2">
                    <i class="fa-solid fa-right-to-bracket me-2"></i>Se connecter
                </a>
            <?php endif; ?>
            <a href="#workify-features" class="btn btn-light px-4 py-2">
                <i class="fa-solid fa-compass me-2"></i>Découvrir Workify
            </a>
        </div>

        <div class="intro-trust-row" aria-label="Indicateurs Workify">
            <div class="intro-trust">
                <strong>Rapide</strong>
                <span>publier une mission et recevoir les premiers profils sans complexité.</span>
            </div>
            <div class="intro-trust">
                <strong>Simple</strong>
                <span>freelance et client dans une expérience unifiée.</span>
            </div>
            <div class="intro-trust">
                <strong>Ouvert</strong>
                <span>accessible aux nouveaux talents comme aux équipes confirmées.</span>
            </div>
        </div>
    </div>

    <div class="intro-preview" aria-label="Apercu Workify">
        <div class="intro-market-card">
            <div class="market-card-head">
                <h2>Opportunités en direct</h2>
                <span class="market-pulse">Active</span>
            </div>
            <div class="market-row">
                <span class="market-icon"><i class="fa-solid fa-briefcase"></i></span>
                <div class="market-content">
                    <strong>Application mobile e-commerce</strong>
                    <span>Client recherche un développeur Flutter disponible cette semaine.</span>
                </div>
                <span class="market-price">900 EUR</span>
            </div>
            <div class="market-row">
                <span class="market-icon"><i class="fa-solid fa-pen-nib"></i></span>
                <div class="market-content">
                    <strong>Identité visuelle pour startup</strong>
                    <span>Mission courte: logo, charte graphique et kit social media.</span>
                </div>
                <span class="market-price">450 EUR</span>
            </div>
            <div class="market-meta">
                <span class="market-chip"><i class="fa-solid fa-shield-halved text-primary"></i>Profils vérifiés</span>
                <span class="market-chip"><i class="fa-solid fa-comments text-primary"></i>Contact rapide</span>
                <span class="market-chip"><i class="fa-solid fa-chart-line text-primary"></i>Suivi clair</span>
            </div>
        </div>
    </div>
</section>

<section id="workify-features" class="intro-module-grid" aria-label="Fonctionnalites Workify">
    <article class="intro-module">
        <i class="fa-solid fa-magnifying-glass-chart"></i>
        <h3>Trouver plus vite</h3>
        <p>Les freelances repèrent les missions adaptées à leurs compétences, sans parcours compliqué.</p>
    </article>
    <article class="intro-module">
        <i class="fa-solid fa-bullhorn"></i>
        <h3>Publier simplement</h3>
        <p>Les clients déposent une offre claire, consultable rapidement par les bons talents.</p>
    </article>
    <article class="intro-module">
        <i class="fa-solid fa-handshake-angle"></i>
        <h3>Collaborer mieux</h3>
        <p>Une expérience sobre et moderne pour passer de l'opportunité à l'action avec confiance.</p>
    </article>
</section>

<section id="about" class="site-section" aria-label="A propos de Workify">
    <div class="about-grid">
        <div>
            <span class="section-kicker"><i class="fa-solid fa-circle-info"></i> À propos</span>
            <h2>Une plateforme pensée pour travailler plus vite et plus clairement.</h2>
            <p>
                Workify simplifie la rencontre entre les clients qui ont besoin d'expertise et les freelances
                qui veulent accéder à des missions sérieuses. L'objectif est simple: publier, trouver,
                contacter et avancer dans un cadre fluide, moderne et professionnel.
            </p>
            <ul class="about-list">
                <li>
                    <i class="fa-solid fa-user-check"></i>
                    <div><strong>Profils lisibles</strong><br><span class="text-muted">Chaque utilisateur peut présenter ses informations essentielles de façon professionnelle.</span></div>
                </li>
                <li>
                    <i class="fa-solid fa-rocket"></i>
                    <div><strong>Lancement rapide</strong><br><span class="text-muted">Une offre ou une demande de mission peut être préparée rapidement et suivie depuis le même espace.</span></div>
                </li>
                <li>
                    <i class="fa-solid fa-lock"></i>
                    <div><strong>Accès sécurisé</strong><br><span class="text-muted">La connexion reste protégée avec les contrôles déjà intégrés à votre application.</span></div>
                </li>
            </ul>
        </div>
        <div class="about-visual" aria-hidden="true"></div>
    </div>
</section>

<section id="contact" class="site-section" aria-label="Contact Workify">
    <div class="contact-grid">
        <div>
            <span class="section-kicker"><i class="fa-solid fa-headset"></i> Contact</span>
            <h2>Besoin d'aide ou d'une collaboration?</h2>
            <p>
                Notre équipe reste disponible pour accompagner les clients et les freelances.
                Contactez Workify pour une question, une demande de mission ou un accompagnement.
            </p>
            <div class="contact-list">
                <a class="contact-item text-decoration-none text-dark" href="tel:+21622822870">
                    <i class="fa-solid fa-phone"></i>
                    <div><strong>Téléphone</strong><br><span class="text-muted">+216 22822870</span></div>
                </a>
                <a class="contact-item text-decoration-none text-dark" href="mailto:contact@workify.tn">
                    <i class="fa-solid fa-envelope"></i>
                    <div><strong>Email</strong><br><span class="text-muted">contact@workify.tn</span></div>
                </a>
                <div class="contact-item">
                    <i class="fa-solid fa-location-dot"></i>
                    <div><strong>Zone</strong><br><span class="text-muted">Tunisie et missions à distance</span></div>
                </div>
            </div>
        </div>
        <div class="contact-panel">
            <h3>Rejoignez Workify aujourd'hui</h3>
            <p>Publiez une offre, trouvez une mission ou contactez un talent avec une interface moderne et directe.</p>
            <div class="contact-actions">
                <a href="login.php" class="btn btn-light px-4 py-2"><i class="fa-solid fa-user-plus me-2"></i>Nous rejoindre</a>
                <a href="mailto:contact@workify.tn" class="btn btn-outline-light px-4 py-2"><i class="fa-solid fa-paper-plane me-2"></i>Envoyer un email</a>
            </div>
        </div>
    </div>
</section>

<?php include_once 'footer.php'; ?>
