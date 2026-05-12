<?php
// Vue publique: formulaire de connexion avec captcha avance.
include_once '../controller/AuthController.php';
$pageData = (new AuthController())->prepareLoginPage();
$error = $pageData['error'];
$captcha = $pageData['captcha'];
$captchaType = $captcha['type'] ?? 'text';

include_once 'header.php';
?>

<style>
    .captcha-panel {
        border: 1px solid #c8d6ea;
        border-radius: 12px;
        background: linear-gradient(180deg, #eef8ff, #d9f0f8);
        overflow: hidden;
    }

    .captcha-head {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 8px 12px;
        font-size: 0.88rem;
        font-weight: 800;
        color: #244260;
        background: rgba(114, 204, 224, 0.38);
        border-bottom: 1px solid rgba(86, 157, 178, 0.35);
    }

    .captcha-body {
        padding: 10px;
    }

    .captcha-image-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 8px;
        align-items: stretch;
    }

    .captcha-image {
        width: 100%;
        min-height: 58px;
        max-height: 58px;
        border: 1px solid #9ab1c6;
        border-radius: 6px;
        background: #f8fbff;
        object-fit: cover;
    }

    .captcha-actions {
        display: grid;
        gap: 7px;
    }

    .captcha-icon-btn {
        width: 38px;
        height: 28px;
        border: 1px solid #9ab1c6;
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.85);
        color: #44647f;
    }

    .captcha-icon-btn:hover {
        background: #fff;
        color: #1d4ed8;
    }

    .captcha-count {
        margin-top: 7px;
        text-align: center;
        color: #4c6176;
        font-size: 0.84rem;
        font-weight: 650;
    }

    .captcha-answer-row {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr);
        gap: 8px;
        align-items: center;
        margin-top: 10px;
    }

    .captcha-answer-row label {
        margin: 0;
        color: #35485c;
        font-weight: 800;
    }

    .robot-check {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        gap: 14px;
        align-items: center;
        min-height: 86px;
        margin-top: 12px;
        margin-bottom: 0;
        padding: 14px;
        border: 1px solid #d6d6d6;
        border-radius: 3px;
        background: #f9f9f9;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
    }

    .robot-check-input {
        width: 28px;
        height: 28px;
        margin: 0;
        border: 2px solid #7b7b7b;
        accent-color: #1a73e8;
        cursor: pointer;
    }

    .robot-check-text {
        color: #111;
        font-size: 1.28rem;
        line-height: 1.2;
    }

    .robot-brand {
        display: grid;
        justify-items: center;
        gap: 2px;
        min-width: 78px;
        color: #555;
        font-size: 0.72rem;
        line-height: 1.1;
        text-align: center;
    }

    .robot-brand-icon {
        display: grid;
        place-items: center;
        width: 38px;
        height: 38px;
        border-radius: 4px;
        color: #fff;
        background: linear-gradient(135deg, #4b8ff5, #1a5edb);
        box-shadow: inset -9px -9px 0 rgba(255, 255, 255, 0.2);
    }

    .robot-brand-links {
        color: #777;
        font-size: 0.68rem;
    }

    .captcha-puzzle-stage {
        position: relative;
        min-height: 120px;
        border: 1px solid #9ab1c6;
        border-radius: 8px;
        background: #f8fbff;
        overflow: hidden;
    }

    .captcha-puzzle-stage img {
        display: block;
        width: 100%;
        height: 120px;
        object-fit: cover;
    }

    .captcha-puzzle-piece {
        position: absolute;
        top: 42px;
        left: calc((100% - 38px) * var(--piece-left, 12) / 100);
        width: 38px;
        height: 38px;
        border: 2px solid #244260;
        border-radius: 8px;
        background:
            radial-gradient(circle at 30% 24%, rgba(255,255,255,0.85) 0 4px, transparent 5px),
            radial-gradient(circle at 72% 72%, rgba(255,255,255,0.55) 0 5px, transparent 6px),
            linear-gradient(135deg, #2f6f7e, #f2b84b);
        box-shadow: 0 8px 18px rgba(36, 66, 96, 0.28);
        transition: left 0.08s ease-out;
    }

    .captcha-slider {
        width: 100%;
        accent-color: #2563eb;
    }

    @media (max-width: 420px) {
        .robot-check {
            grid-template-columns: auto minmax(0, 1fr);
        }

        .robot-brand {
            grid-column: 1 / -1;
            justify-self: end;
        }

        .robot-check-text {
            font-size: 1.08rem;
        }

        .captcha-answer-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <h2 class="text-center mb-4 text-primary fw-bold">Connexion</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation me-2"></i><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label">Adresse Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                    <input type="email" class="form-control" name="email" placeholder="Entrez votre email" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Mot de passe</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" class="form-control" name="password" placeholder="Entrez votre mot de passe" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Verification anti-robot</label>
                <div class="captcha-panel">
                    <div class="captcha-head">
                        <span><?= $captchaType === 'puzzle' ? 'Complete the puzzle' : 'Match the characters in the picture' ?></span>
                        <span>Help</span>
                    </div>
                    <div class="captcha-body">
                        <div class="small text-muted mb-2">
                            <?= $captchaType === 'puzzle' ? 'To continue, slide the piece into the empty shape.' : 'To continue, type the characters you see in the picture.' ?>
                        </div>
                        <?php if ($captchaType === 'puzzle'): ?>
                            <div class="captcha-image-row">
                                <div class="captcha-puzzle-stage" id="captchaPuzzleStage" style="--piece-left: <?= (int)($captcha['start'] ?? 12) ?>;">
                                    <img src="captcha_puzzle.php?v=<?= time() ?>" alt="Captcha puzzle">
                                    <span class="captcha-puzzle-piece" aria-hidden="true"></span>
                                </div>
                                <div class="captcha-actions">
                                    <button type="button" id="refreshCaptcha" class="captcha-icon-btn" title="Changer le captcha" aria-label="Changer le captcha">
                                        <i class="fa-solid fa-rotate-right"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="captcha-count">Faites glisser la piece jusqu'a l'emplacement vide.</div>
                            <div class="captcha-answer-row">
                                <label for="captchaPuzzle">Position:</label>
                                <input
                                    type="range"
                                    id="captchaPuzzle"
                                    class="captcha-slider"
                                    name="captcha_puzzle_answer"
                                    min="0"
                                    max="100"
                                    value="<?= (int)($captcha['start'] ?? 12) ?>"
                                    required
                                >
                            </div>
                        <?php else: ?>
                            <div class="captcha-image-row">
                                <img
                                    id="captchaImage"
                                    class="captcha-image"
                                    src="captcha.php?v=<?= time() ?>"
                                    alt="Captcha anti-robot"
                                    width="260"
                                    height="58"
                                >
                                <div class="captcha-actions">
                                    <button type="button" id="captchaAudio" class="captcha-icon-btn" title="Ecouter le captcha" aria-label="Ecouter le captcha">
                                        <i class="fa-solid fa-volume-high"></i>
                                    </button>
                                    <button type="button" id="refreshCaptcha" class="captcha-icon-btn" title="Changer le captcha" aria-label="Changer le captcha">
                                        <i class="fa-solid fa-rotate-right"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="captcha-count">The picture contains 8 case-sensitive characters.</div>
                            <div class="captcha-answer-row">
                                <label for="captchaAnswer">Characters:</label>
                                <input
                                    type="text"
                                    id="captchaAnswer"
                                    class="form-control"
                                    name="captcha_answer"
                                    placeholder="Entrez les caracteres"
                                    maxlength="8"
                                    autocomplete="off"
                                    autocapitalize="off"
                                    required
                                >
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <label class="robot-check" for="captchaRobotCheck">
                    <input
                        type="checkbox"
                        id="captchaRobotCheck"
                        class="robot-check-input"
                        name="captcha_robot_check"
                        value="1"
                        required
                    >
                    <span class="robot-check-text">I'm not a robot</span>
                    <span class="robot-brand" aria-hidden="true">
                        <span class="robot-brand-icon"><i class="fa-solid fa-arrows-rotate"></i></span>
                        <span>reCAPTCHA</span>
                        <span class="robot-brand-links">Privacy - Terms</span>
                    </span>
                </label>
                <div class="form-text">
                    <?php if ($captchaType === 'puzzle'): ?>
                        Alignez la piece avec le trou avant de vous connecter.
                    <?php else: ?>
                        Recopiez exactement les 8 caracteres de l'image, avec majuscules et minuscules.
                    <?php endif; ?>
                </div>
            </div>

            <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-3">
                <button type="submit" class="btn btn-primary flex-grow-1 py-2 fw-bold">Se connecter a Workify</button>
                <a href="forgot_password.php" class="fw-bold text-primary text-center text-sm-nowrap">
                    Mot de passe oublie
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    // Bouton de renouvellement: demande au controleur de creer un nouveau captcha.
    document.getElementById('refreshCaptcha')?.addEventListener('click', () => {
        window.location.href = 'login.php?captcha_refresh=1';
    });

    const puzzle = document.getElementById('captchaPuzzle');
    const puzzleStage = document.getElementById('captchaPuzzleStage');

    // Deplace visuellement la piece du puzzle quand le slider bouge.
    puzzle?.addEventListener('input', () => {
        puzzleStage?.style.setProperty('--piece-left', puzzle.value);
    });

    // Recupere le texte audio du captcha et le lit avec la synthese vocale du navigateur.
    document.getElementById('captchaAudio')?.addEventListener('click', async () => {
        if (!('speechSynthesis' in window)) {
            alert('La lecture audio n est pas disponible dans ce navigateur.');
            return;
        }

        try {
            const response = await fetch('captcha_audio.php?v=' + Date.now(), {
                cache: 'no-store',
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();

            if (!data.speech) {
                alert('Captcha audio indisponible. Actualisez le captcha.');
                return;
            }

            window.speechSynthesis.cancel();
            const utterance = new SpeechSynthesisUtterance(data.speech);
            utterance.lang = 'en-US';
            utterance.rate = 0.72;
            utterance.pitch = 1;
            window.speechSynthesis.speak(utterance);
        } catch (error) {
            alert('Impossible de lire le captcha audio.');
        }
    });
</script>

<?php include_once 'footer.php'; ?>
