<?php
$pageTitle = 'Workify';
$office = 'front';
$activeModule = 'home';
include __DIR__ . '/../includes/header.php';
?>

<section class="hero-home">
    <div class="hero-copy">
        <p class="eyebrow">Workify marketplace</p>
        <h2>Learn, hire, publish and grow in one place.</h2>
        <p class="muted">A single workspace for freelancers, trainers and project owners: discover formations, publish opportunities, follow community updates and move faster with a clean Workify experience.</p>
        <div class="hero-actions">
            <a class="btn btn-primary" href="../controller/FormationC.php?office=front&action=list">Explore Formations</a>
            <a class="btn" href="../controller/JobC.php?office=front&action=list">Browse Jobs</a>
            <a class="btn" href="../controller/PublicationC.php?office=front&action=list">See Publications</a>
        </div>
    </div>

    <div class="hero-board" aria-hidden="true">
        <div class="hero-photo"></div>
        <div class="floating-card one">
            Formations live
            <strong><?= isset($stats['formations']) ? (int) $stats['formations'] : 0; ?></strong>
            <span class="muted">Trainers can share practical tracks.</span>
        </div>
        <div class="floating-card two">
            Jobs open
            <strong><?= isset($stats['jobs']) ? (int) $stats['jobs'] : 0; ?></strong>
            <span class="muted">Bosses find ready-to-work talent.</span>
        </div>
    </div>
</section>

<section class="module-strip" aria-label="Workify modules">
    <a class="module-tile" href="../controller/JobC.php?office=front&action=list">
        <span>01</span>
        <h3>Jobs</h3>
        <p class="muted">Find missions and apply quickly.</p>
    </a>
    <a class="module-tile" href="../controller/PublicationC.php?office=front&action=list">
        <span>02</span>
        <h3>Publication</h3>
        <p class="muted">Follow updates from the community.</p>
    </a>
    <a class="module-tile" href="#events">
        <span>03</span>
        <h3>Evenement</h3>
        <p class="muted">Reserved for the next module.</p>
    </a>
    <a class="module-tile" href="#messages">
        <span>04</span>
        <h3>Message</h3>
        <p class="muted">Messaging entry ready for integration.</p>
    </a>
    <a class="module-tile" href="../controller/FormationC.php?office=front&action=list">
        <span>05</span>
        <h3>Formation</h3>
        <p class="muted">Learn skills with guided tracks.</p>
    </a>
</section>

<section class="audience-band">
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

<?php include __DIR__ . '/../includes/footer.php'; ?>
