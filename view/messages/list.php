<?php
$pageTitle = 'Messages';
$activeModule = 'messages';
$contacts = isset($contacts) ? $contacts : [];
$messages = isset($messages) ? $messages : [];
$selectedUser = isset($selectedUser) ? $selectedUser : null;
$currentUser = isset($currentUser) ? $currentUser : [];
$errors = isset($errors) ? $errors : [];
include __DIR__ . '/../includes/header.php';

function messageAvatar($avatar, $initials, $class = '')
{
    $safeClass = trim('avatar-pill ' . $class);
    if (!empty($avatar) && strpos($avatar, 'uploads/') === 0) {
        return '<span class="' . htmlspecialchars($safeClass, ENT_QUOTES) . ' avatar-image"><img src="../' . htmlspecialchars($avatar, ENT_QUOTES) . '" alt=""></span>';
    }
    return '<span class="' . htmlspecialchars($safeClass, ENT_QUOTES) . '">' . htmlspecialchars($initials, ENT_QUOTES) . '</span>';
}
?>

<div class="toolbar">
    <div>
        <p class="eyebrow">Messagerie Workify</p>
        <h2>Conversations</h2>
        <p class="muted">Discutez avec les talents, clients et formateurs depuis un espace clair et rapide.</p>
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

<div class="message-workspace">
    <aside class="message-contacts">
        <div class="message-profile-card">
            <?= messageAvatar(isset($currentUser['avatar_url']) ? $currentUser['avatar_url'] : '', strtoupper(substr($currentUser['first_name'], 0, 1) . substr($currentUser['last_name'], 0, 1))); ?>
            <div>
                <h3><?= htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name'], ENT_QUOTES); ?></h3>
                <p class="muted"><?= htmlspecialchars($currentUser['email'], ENT_QUOTES); ?></p>
            </div>
        </div>

        <div class="message-tabs" aria-label="Filtres messages">
            <span class="active">All</span>
            <span>Unread</span>
            <span>Recent</span>
        </div>

        <h3>Inbox</h3>
        <?php if (empty($contacts)) { ?>
            <p class="muted">Aucun contact disponible.</p>
        <?php } ?>
        <?php foreach ($contacts as $contactItem) { ?>
            <a class="contact-row <?= $selectedUser && (int) $selectedUser['id'] === (int) $contactItem['id'] ? 'active' : ''; ?>" href="../controller/MessageC.php?office=front&action=list&with=<?= (int) $contactItem['id']; ?>">
                <?= messageAvatar(isset($contactItem['avatar_url']) ? $contactItem['avatar_url'] : '', strtoupper(substr($contactItem['first_name'], 0, 1) . substr($contactItem['last_name'], 0, 1)), 'small'); ?>
                <span>
                    <strong><?= htmlspecialchars($contactItem['first_name'] . ' ' . $contactItem['last_name'], ENT_QUOTES); ?></strong>
                    <small><?= htmlspecialchars($contactItem['role_name'], ENT_QUOTES); ?></small>
                </span>
            </a>
        <?php } ?>
    </aside>

    <section class="message-panel modern-message-panel">
        <?php if (!$selectedUser) { ?>
            <div class="empty-state">
                <h3>Choisissez un contact</h3>
                <p class="muted">Selectionnez un utilisateur pour commencer la conversation.</p>
            </div>
        <?php } else { ?>
            <div class="message-head">
                <div>
                    <p class="eyebrow">Conversation</p>
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

            <form class="message-form modern-message-form" data-validate="message" action="../controller/MessageC.php?office=front&action=send" method="post">
                <div class="error-box field-full"></div>
                <input type="hidden" name="receiver_id" value="<?= (int) $selectedUser['id']; ?>">
                <textarea name="content" placeholder="Write a message..." required></textarea>
                <button class="btn btn-primary" type="submit">
                    <svg viewBox="0 0 24 24"><path d="M3 12 21 3l-4 18-5-7-7-2z"/></svg>
                    Envoyer
                </button>
            </form>
        <?php } ?>
    </section>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
