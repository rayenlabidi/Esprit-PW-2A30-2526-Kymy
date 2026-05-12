<?php
$pageTitle = 'Gestion Utilisateurs';
$activeModule = 'users';
$search = isset($search) ? $search : '';
$role = isset($role) ? $role : '';
$status = isset($status) ? $status : '';
$liste = isset($liste) ? $liste : [];
$roles = isset($roles) ? $roles : [];
$statistiques = isset($statistiques) ? $statistiques : [];
include __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <div>
        <p class="eyebrow">Espace prive</p>
        <h2>User Control Center</h2>
    </div>
    <a class="btn btn-primary" href="../controller/UtilisateurC.php?action=add">
        <svg viewBox="0 0 24 24"><path d="M11 5h2v6h6v2h-6v6h-2v-6H5v-2h6V5z"/></svg>
        Ajouter
    </a>
</div>

<div class="stats-grid">
    <div class="card">Total <strong><?= isset($statistiques['total']) ? (int) $statistiques['total'] : 0; ?></strong></div>
    <div class="card">Admins <strong><?= isset($statistiques['admins']) ? (int) $statistiques['admins'] : 0; ?></strong></div>
    <div class="card">Freelancers <strong><?= isset($statistiques['freelancers']) ? (int) $statistiques['freelancers'] : 0; ?></strong></div>
    <div class="card">Boss <strong><?= isset($statistiques['boss']) ? (int) $statistiques['boss'] : 0; ?></strong></div>
</div>

<form class="filters" action="../controller/UtilisateurC.php" method="get">
    <input type="hidden" name="action" value="list">
    <input name="search" placeholder="Search by name or email" value="<?= htmlspecialchars($search, ENT_QUOTES); ?>">
    <select name="role">
        <option value="">Tous les roles</option>
        <?php foreach ($roles as $roleItem) { ?>
            <option value="<?= htmlspecialchars($roleItem['slug'], ENT_QUOTES); ?>" <?= $role === $roleItem['slug'] ? 'selected' : ''; ?>>
                <?= htmlspecialchars($roleItem['name'], ENT_QUOTES); ?>
            </option>
        <?php } ?>
    </select>
    <select name="status">
        <option value="">Tous les statuts</option>
        <option value="active" <?= $status === 'active' ? 'selected' : ''; ?>>Active</option>
        <option value="pending" <?= $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
        <option value="blocked" <?= $status === 'blocked' ? 'selected' : ''; ?>>Blocked</option>
    </select>
    <button class="btn btn-green" type="submit">Rechercher</button>
    <a class="btn" href="../controller/UtilisateurC.php?action=list">Initialiser</a>
</form>

<div class="table-box">
    <table>
        <thead>
            <tr>
                <th>Utilisateur</th>
                <th>Role</th>
                <th>Email</th>
                <th>Telephone</th>
                <th>Statut</th>
                <th>QR</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($liste)) { ?>
                <tr><td colspan="7">Aucun utilisateur trouve.</td></tr>
            <?php } ?>
            <?php foreach ($liste as $utilisateurItem) { ?>
                <tr>
                    <td data-label="Utilisateur"><strong><?= htmlspecialchars($utilisateurItem['first_name'] . ' ' . $utilisateurItem['last_name'], ENT_QUOTES); ?></strong><br><span class="muted"><?= htmlspecialchars($utilisateurItem['headline'], ENT_QUOTES); ?></span></td>
                    <td data-label="Role"><?= htmlspecialchars($utilisateurItem['role_name'], ENT_QUOTES); ?></td>
                    <td data-label="Email"><?= htmlspecialchars($utilisateurItem['email'], ENT_QUOTES); ?></td>
                    <td data-label="Telephone"><?= htmlspecialchars(isset($utilisateurItem['phone']) ? $utilisateurItem['phone'] : '', ENT_QUOTES); ?></td>
                    <td data-label="Statut"><span class="badge"><?= htmlspecialchars($utilisateurItem['status'], ENT_QUOTES); ?></span></td>
                    <td data-label="QR">
                        <button
                            class="btn qr-btn"
                            type="button"
                            data-name="<?= htmlspecialchars($utilisateurItem['first_name'] . ' ' . $utilisateurItem['last_name'], ENT_QUOTES); ?>"
                            data-email="<?= htmlspecialchars($utilisateurItem['email'], ENT_QUOTES); ?>"
                            data-phone="<?= htmlspecialchars(isset($utilisateurItem['phone']) ? $utilisateurItem['phone'] : '', ENT_QUOTES); ?>"
                            data-role="<?= htmlspecialchars($utilisateurItem['role_name'], ENT_QUOTES); ?>"
                            data-headline="<?= htmlspecialchars($utilisateurItem['headline'], ENT_QUOTES); ?>"
                        >QR</button>
                    </td>
                    <td data-label="Actions" class="actions">
                        <a class="btn" href="../controller/UtilisateurC.php?action=edit&id=<?= (int) $utilisateurItem['id']; ?>">Modifier</a>
                        <?php if (!isset($_SESSION['user_id']) || (int) $_SESSION['user_id'] !== (int) $utilisateurItem['id']) { ?>
                            <a class="btn btn-danger" href="../controller/UtilisateurC.php?action=delete&id=<?= (int) $utilisateurItem['id']; ?>" onclick="return confirm('Supprimer cet utilisateur ?');">Supprimer</a>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<div class="qr-modal" id="qrModal" aria-hidden="true">
    <div class="qr-dialog">
        <div class="section-head">
            <div>
                <p class="eyebrow">Contact QR</p>
                <h2 id="qrTitle">Utilisateur</h2>
            </div>
            <button class="btn" type="button" id="qrClose">Fermer</button>
        </div>
        <div id="qrCode" class="qr-code-box"></div>
        <p class="muted" id="qrDetails"></p>
    </div>
</div>

<script src="../assets/js/qrcode.min.js"></script>
<script>
document.querySelectorAll('.qr-btn').forEach((button) => {
    button.addEventListener('click', () => {
        const name = button.dataset.name || '';
        const email = button.dataset.email || '';
        const phone = button.dataset.phone || '';
        const role = button.dataset.role || '';
        const headline = button.dataset.headline || '';
        const payload = [
            'BEGIN:VCARD',
            'VERSION:3.0',
            'FN:' + name,
            'EMAIL:' + email,
            'TEL:' + phone,
            'TITLE:' + headline,
            'ROLE:' + role,
            'ORG:Workify',
            'END:VCARD'
        ].join('\n');

        document.getElementById('qrTitle').textContent = name;
        document.getElementById('qrDetails').textContent = email + (phone ? ' - ' + phone : '') + (role ? ' - ' + role : '');
        document.getElementById('qrCode').innerHTML = '';
        new QRCode(document.getElementById('qrCode'), {
            text: payload,
            width: 220,
            height: 220,
            colorDark: '#0f172a',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
        document.getElementById('qrModal').classList.add('is-open');
    });
});

document.getElementById('qrClose').addEventListener('click', () => {
    document.getElementById('qrModal').classList.remove('is-open');
});

document.getElementById('qrModal').addEventListener('click', (event) => {
    if (event.target.id === 'qrModal') {
        document.getElementById('qrModal').classList.remove('is-open');
    }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
