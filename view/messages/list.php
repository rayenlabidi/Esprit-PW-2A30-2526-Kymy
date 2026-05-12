<?php
$pageTitle = 'Messages';
$activeModule = 'messages';
$contacts = isset($contacts) ? $contacts : [];
$messages = isset($messages) ? $messages : [];
$selectedUser = isset($selectedUser) ? $selectedUser : null;
$currentUser = isset($currentUser) ? $currentUser : [];
$errors = isset($errors) ? $errors : [];
include __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <div>
        <p class="eyebrow">Messagerie Workify</p>
        <h2>Conversations</h2>
        <p class="muted">Les messages utilisent maintenant le MVC principal et la table `utilisateurs`.</p>
    </div>
</div>

<?php if (!empty($errors)) { ?>
    <div class="error-box">
        <ul>
            <?php foreach ($errors as $errorItem) { ?>
                <li><?= htmlspecialchars($errorItem, ENT_QUOTES); ?></li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>

<div class="message-layout">
    <aside class="message-contacts">
        <h3>Contacts</h3>
        <?php if (empty($contacts)) { ?>
            <p class="muted">Aucun contact disponible.</p>
        <?php } ?>
        <?php foreach ($contacts as $contactItem) { ?>
            <a class="contact-row <?= $selectedUser && (int) $selectedUser['id'] === (int) $contactItem['id'] ? 'active' : ''; ?>" href="../controller/MessageC.php?office=front&action=list&with=<?= (int) $contactItem['id']; ?>">
                <span class="avatar-pill small"><?= htmlspecialchars(strtoupper(substr($contactItem['first_name'], 0, 1) . substr($contactItem['last_name'], 0, 1)), ENT_QUOTES); ?></span>
                <span>
                    <strong><?= htmlspecialchars($contactItem['first_name'] . ' ' . $contactItem['last_name'], ENT_QUOTES); ?></strong>
                    <small><?= htmlspecialchars($contactItem['role_name'], ENT_QUOTES); ?></small>
                </span>
            </a>
        <?php } ?>
    </aside>

    <section class="message-panel">
        <?php if (!$selectedUser) { ?>
            <div class="empty-state">
                <h3>Choisissez un contact</h3>
                <p class="muted">Selectionnez un utilisateur pour commencer la conversation.</p>
            </div>
        <?php } else { ?>
            <div class="message-head">
                <div>
                    <p class="eyebrow">Conversation avec</p>
                    <h3><?= htmlspecialchars($selectedUser['first_name'] . ' ' . $selectedUser['last_name'], ENT_QUOTES); ?></h3>
                </div>
                <span class="badge"><?= htmlspecialchars($selectedUser['role_name'], ENT_QUOTES); ?></span>
            </div>

            <div class="message-thread">
                <?php if (empty($messages)) { ?>
                    <p class="muted">Aucun message pour cette conversation.</p>
                <?php } ?>
                <?php foreach ($messages as $messageItem) { ?>
                    <?php $isMine = (string) $messageItem['sender_id'] === (string) $currentUser['id']; ?>
                    <div class="message-bubble <?= $isMine ? 'mine' : ''; ?>">
                        <strong><?= htmlspecialchars($messageItem['sender_name'], ENT_QUOTES); ?></strong>
                        <p><?= nl2br(htmlspecialchars($messageItem['content'], ENT_QUOTES)); ?></p>
                        <small><?= htmlspecialchars($messageItem['created_at'], ENT_QUOTES); ?></small>
                    </div>
                <?php } ?>
            </div>

            <form class="message-form" action="../controller/MessageC.php?office=front&action=send" method="post">
                <input type="hidden" name="receiver_id" value="<?= (int) $selectedUser['id']; ?>">
                <textarea name="content" placeholder="Ecrire un message..." required></textarea>
                <button class="btn btn-primary" type="submit">
                    <svg viewBox="0 0 24 24"><path d="M3 12 21 3l-4 18-5-7-7-2z"/></svg>
                    Envoyer
                </button>
            </form>
        <?php } ?>
    </section>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
