<?php
$pageTitle = 'Gestion Messages';
$activeModule = 'messages';
$messages = isset($messages) ? $messages : [];
include __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <div>
        <p class="eyebrow">Espace prive</p>
        <h2>Centre des messages</h2>
        <p class="muted">Suivez les conversations, signalez les messages sensibles et gardez l espace propre.</p>
    </div>
</div>

<div class="table-box">
    <table>
        <thead>
            <tr>
                <th>Expediteur</th>
                <th>Destinataire</th>
                <th>Message</th>
                <th>Statut</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($messages)) { ?>
                <tr><td colspan="6">Aucun message trouve.</td></tr>
            <?php } ?>
            <?php foreach ($messages as $messageItem) { ?>
                <tr>
                    <td data-label="Expediteur"><?= htmlspecialchars($messageItem['sender_name'], ENT_QUOTES); ?></td>
                    <td data-label="Destinataire"><?= htmlspecialchars($messageItem['receiver_name'], ENT_QUOTES); ?></td>
                    <td data-label="Message"><?= nl2br(htmlspecialchars(substr($messageItem['content'], 0, 180), ENT_QUOTES)); ?></td>
                    <td data-label="Statut">
                        <span class="badge <?= (int) $messageItem['is_flagged'] === 1 ? 'badge-amber' : 'badge-green'; ?>">
                            <?= (int) $messageItem['is_flagged'] === 1 ? 'Signale' : 'Valide'; ?>
                        </span>
                    </td>
                    <td data-label="Date"><?= htmlspecialchars($messageItem['created_at'], ENT_QUOTES); ?></td>
                    <td data-label="Actions" class="actions">
                        <a class="btn btn-danger" href="../controller/MessageC.php?office=back&action=delete&id=<?= (int) $messageItem['id']; ?>" onclick="return confirm('Supprimer ce message ?');">Supprimer</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
