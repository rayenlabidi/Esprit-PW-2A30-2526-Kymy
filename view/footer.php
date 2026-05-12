<?php
// Footer commun: il ferme les conteneurs ouverts par header.php.
$workifyPhone = $workifyPhone ?? '+216 22822870';
$workifyEmail = $workifyEmail ?? 'contact@workify.tn';
?>
<?php if (!empty($showSidebar)): ?>
                </div>
            </div>
        </main>
    </div>
</div>
<?php else: ?>
        </div>
    </div>
</div>
<?php endif; ?>

<footer class="workify-footer mt-5 mb-4">
    <style>
        .workify-footer {
            width: min(1140px, calc(100% - 32px));
            margin-left: auto;
            margin-right: auto;
            padding: 24px;
            border: 1px solid rgba(148, 163, 184, 0.22);
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.78);
            color: #64748b;
            box-shadow: 0 18px 35px rgba(15, 23, 42, 0.06);
        }
        .workify-footer-grid {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
        }
        .workify-footer-brand {
            color: #0f172a;
            font-size: 1.05rem;
            font-weight: 850;
        }
        .workify-footer-links {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }
        .workify-footer a {
            color: #1d4ed8;
            font-weight: 750;
            text-decoration: none;
        }
        .workify-footer a:hover {
            text-decoration: underline;
        }
    </style>
    <div class="workify-footer-grid">
        <div>
            <div class="workify-footer-brand"><i class="fa-solid fa-layer-group me-1"></i>Workify</div>
            <small>&copy; <?= date('Y') ?> Workify. Tous droits réservés.</small>
        </div>
        <div class="workify-footer-links">
            <a href="tel:+21622822870"><i class="fa-solid fa-phone me-1"></i><?= htmlspecialchars($workifyPhone) ?></a>
            <a href="mailto:<?= htmlspecialchars($workifyEmail) ?>"><i class="fa-solid fa-envelope me-1"></i><?= htmlspecialchars($workifyEmail) ?></a>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
