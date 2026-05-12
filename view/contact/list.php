<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="toolbar">
    <div>
        <p class="eyebrow">Relation client</p>
        <h2>Messages contact</h2>
        <p class="muted">Consultez les demandes envoyees depuis le formulaire public.</p>
    </div>
</div>

<div class="table-box">
    <table>
        <thead>
            <tr>
                <th>Contact</th>
                <th>Sujet</th>
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
                    <td data-label="Contact">
                        <strong><?= htmlspecialchars($messageItem['full_name'], ENT_QUOTES); ?></strong><br>
                        <span class="muted"><?= htmlspecialchars($messageItem['email'], ENT_QUOTES); ?></span>
                    </td>
                    <td data-label="Sujet"><?= htmlspecialchars($messageItem['subject'], ENT_QUOTES); ?></td>
                    <td data-label="Message"><?= nl2br(htmlspecialchars(substr($messageItem['message'], 0, 180), ENT_QUOTES)); ?></td>
                    <td data-label="Statut"><span class="badge"><?= htmlspecialchars($messageItem['status'], ENT_QUOTES); ?></span></td>
                    <td data-label="Date"><?= htmlspecialchars($messageItem['created_at'], ENT_QUOTES); ?></td>
                    <td data-label="Actions" class="actions">
                        <a class="btn" href="../controller/ContactC.php?action=status&id=<?= (int) $messageItem['id']; ?>&status=read">Lu</a>
                        <a class="btn" href="../controller/ContactC.php?action=status&id=<?= (int) $messageItem['id']; ?>&status=archived">Archiver</a>
                        <a class="btn btn-danger" href="../controller/ContactC.php?action=delete&id=<?= (int) $messageItem['id']; ?>" onclick="return confirm('Supprimer ce message ?');">Supprimer</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
