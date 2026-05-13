<?php
$pageTitle = 'Detail Evenement';
$activeModule = 'events';
$eventImage = !empty($event['image_url']) ? $event['image_url'] : 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=1200&q=80';
include __DIR__ . '/../includes/header.php';
AuthC::startSession();
$flash = isset($_SESSION['event_flash']) ? $_SESSION['event_flash'] : '';
unset($_SESSION['event_flash']);
?>

<div class="detail-box">
    <div class="toolbar">
        <div>
            <p class="eyebrow"><?= htmlspecialchars($event['category_name'], ENT_QUOTES); ?></p>
            <h2><?= htmlspecialchars($event['title'], ENT_QUOTES); ?></h2>
            <p class="muted">Organisateur: <?= htmlspecialchars($event['organizer_name'], ENT_QUOTES); ?></p>
        </div>
        <span class="badge badge-green"><?= htmlspecialchars($event['status'], ENT_QUOTES); ?></span>
    </div>

    <img class="detail-hero-image" src="<?= strpos($eventImage, 'uploads/') === 0 ? '../' . htmlspecialchars($eventImage, ENT_QUOTES) : htmlspecialchars($eventImage, ENT_QUOTES); ?>" alt="<?= htmlspecialchars($event['title'], ENT_QUOTES); ?>">

    <p><?= nl2br(htmlspecialchars($event['description'], ENT_QUOTES)); ?></p>

    <div class="stats-grid">
        <div class="card">Date <strong style="font-size: 22px;"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($event['event_date'])), ENT_QUOTES); ?></strong></div>
        <div class="card">Type <strong style="font-size: 22px;"><?= $event['is_online'] ? 'En ligne' : 'Presentiel'; ?></strong></div>
        <div class="card">Places <strong><?= (int) $event['max_participants']; ?></strong></div>
        <div class="card">Categorie <strong style="font-size: 20px;"><?= htmlspecialchars($event['category_name'], ENT_QUOTES); ?></strong></div>
    </div>

    <p><strong>Lieu:</strong> <?= htmlspecialchars($event['location'], ENT_QUOTES); ?></p>
    <?php if (!empty($event['latitude']) && !empty($event['longitude'])) { ?>
        <p><strong>GPS:</strong> <?= htmlspecialchars($event['latitude'], ENT_QUOTES); ?>, <?= htmlspecialchars($event['longitude'], ENT_QUOTES); ?></p>
    <?php } ?>

    <?php if ($flash !== '') { ?><div class="success-box"><?= htmlspecialchars($flash, ENT_QUOTES); ?></div><?php } ?>

    <div class="actions" style="margin-top: 18px;">
        <a class="btn" href="../controller/EventC.php?office=<?= htmlspecialchars($office, ENT_QUOTES); ?>&action=list">Retour</a>
        <?php if ($office === 'front') { ?>
            <?php if (!empty($isRegistered)) { ?>
                <span class="badge badge-green">Participation deja confirmee</span>
            <?php } else { ?>
                <a class="btn btn-primary" href="../controller/EventC.php?office=front&action=register&id=<?= (int) $event['id']; ?>">Participer a cet evenement</a>
            <?php } ?>
        <?php } ?>
        <?php if ($office === 'back') { ?>
            <a class="btn btn-primary" href="../controller/EventC.php?office=back&action=edit&id=<?= (int) $event['id']; ?>">Modifier</a>
        <?php } ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
