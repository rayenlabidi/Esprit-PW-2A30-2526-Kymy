<?php
require_once __DIR__ . '/../../controller/CaptchaGuard.php';

$captchaScope = isset($captchaScope) ? $captchaScope : 'default';
$captcha = isset($captcha) && is_array($captcha) ? $captcha : CaptchaGuard::challenge($captchaScope);
$captchaPublic = CaptchaGuard::publicChallenge($captcha);
?>

<div class="captcha-box workify-captcha" data-captcha-scope="<?= htmlspecialchars($captchaScope, ENT_QUOTES); ?>" data-captcha-refresh-url="CaptchaC.php">
    <div class="captcha-head">
        <div>
            <span class="captcha-label">Verification</span>
            <strong data-captcha-prompt><?= htmlspecialchars($captchaPublic['prompt'], ENT_QUOTES); ?></strong>
            <p>Choisissez l image demandee pour continuer.</p>
        </div>
        <div class="captcha-tools">
            <button class="captcha-icon-btn" type="button" data-captcha-audio title="Ecouter le captcha" aria-label="Ecouter le captcha">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 9v6h4l5 4V5L8 9H4zm12.5 3a4.5 4.5 0 0 0-2-3.7v7.4a4.5 4.5 0 0 0 2-3.7zm-2-8.6v2.2a7.5 7.5 0 0 1 0 12.8v2.2a9.5 9.5 0 0 0 0-17.2z"/></svg>
            </button>
            <button class="captcha-icon-btn" type="button" data-captcha-refresh title="Nouveau captcha" aria-label="Nouveau captcha">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M17.7 6.3A8 8 0 1 0 20 12h-2a6 6 0 1 1-1.8-4.3L13 11h8V3l-3.3 3.3z"/></svg>
            </button>
        </div>
    </div>

    <input type="hidden" name="captcha_id" value="<?= htmlspecialchars($captchaPublic['id'], ENT_QUOTES); ?>">
    <input type="hidden" name="captcha_answer" value="">

    <div class="captcha-options" role="radiogroup" aria-label="<?= htmlspecialchars($captchaPublic['prompt'], ENT_QUOTES); ?>">
        <?php foreach ($captchaPublic['choices'] as $choice) { ?>
            <button class="captcha-choice" type="button" data-captcha-choice="<?= htmlspecialchars($choice['key'], ENT_QUOTES); ?>" aria-pressed="false">
                <span class="captcha-visual" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="<?= htmlspecialchars($choice['icon'], ENT_QUOTES); ?>"/></svg>
                </span>
                <span><?= htmlspecialchars($choice['label'], ENT_QUOTES); ?></span>
            </button>
        <?php } ?>
    </div>
</div>
