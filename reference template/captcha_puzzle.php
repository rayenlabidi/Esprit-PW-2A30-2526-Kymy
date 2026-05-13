<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../lib/CaptchaService.php';

// Cette page genere le dessin SVG du captcha puzzle.
$challenge = CaptchaService::getChallenge();

if (($challenge['type'] ?? '') !== 'puzzle') {
    $challenge = CaptchaService::refreshChallenge();
}

$width = 320;
$height = 120;
$pieceSize = 38;
$targetPercent = (int)$challenge['target'];
// Conversion de la position en pourcentage vers une position X en pixels.
$targetX = (int)round(($width - $pieceSize) * ($targetPercent / 100));
$targetY = 42;
$colors = ['#2f6f7e', '#f2b84b', '#cf5c36', '#6f7fc9', '#4f9d69'];

header('Content-Type: image/svg+xml');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

echo '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '">';
echo '<defs><linearGradient id="g" x1="0" x2="1" y1="0" y2="1"><stop stop-color="#eef8ff"/><stop offset="1" stop-color="#d8edf5"/></linearGradient></defs>';
echo '<rect width="100%" height="100%" rx="8" fill="url(#g)"/>';

// Cercles colores en arriere-plan pour donner un visuel moins facile a analyser.
for ($i = 0; $i < 24; $i++) {
    $x = random_int(0, $width);
    $y = random_int(0, $height);
    $r = random_int(10, 26);
    $color = $colors[$i % count($colors)];
    echo '<circle cx="' . $x . '" cy="' . $y . '" r="' . $r . '" fill="' . $color . '" opacity="0.16"/>';
}

// Courbes aleatoires pour ajouter du bruit visuel.
for ($i = 0; $i < 9; $i++) {
    echo '<path d="M' . random_int(-20, $width) . ' ' . random_int(10, $height - 10) . ' C ' . random_int(40, 120) . ' ' . random_int(0, $height) . ', ' . random_int(160, 250) . ' ' . random_int(0, $height) . ', ' . random_int(260, $width + 20) . ' ' . random_int(10, $height - 10) . '" fill="none" stroke="#244260" stroke-width="1" opacity="0.13"/>';
}

// Faux emplacements pour rendre le puzzle plus difficile.
for ($i = 0; $i < 4; $i++) {
    $fakeX = random_int(22, $width - $pieceSize - 22);
    if (abs($fakeX - $targetX) < 46) {
        $fakeX = $fakeX + ($fakeX < $targetX ? -72 : 72);
        $fakeX = max(22, min($width - $pieceSize - 22, $fakeX));
    }
    $fakeY = random_int(36, 62);
    echo '<rect x="' . $fakeX . '" y="' . $fakeY . '" width="' . $pieceSize . '" height="' . $pieceSize . '" rx="8" fill="none" stroke="#244260" stroke-width="2" stroke-dasharray="3 6" opacity="0.22"/>';
}

// Le vrai emplacement: l'utilisateur doit aligner la piece dessus.
echo '<rect x="' . $targetX . '" y="' . $targetY . '" width="' . $pieceSize . '" height="' . $pieceSize . '" rx="8" fill="#f8fbff" stroke="#244260" stroke-width="3" stroke-dasharray="4 5" opacity="0.84"/>';
echo '<circle cx="' . ($targetX + 12) . '" cy="' . ($targetY + 9) . '" r="4" fill="#244260" opacity="0.22"/>';
echo '<circle cx="' . ($targetX + 27) . '" cy="' . ($targetY + 29) . '" r="5" fill="#244260" opacity="0.18"/>';
echo '<text x="18" y="25" font-family="Arial, sans-serif" font-size="12" font-weight="700" fill="#244260" opacity="0.7">Glissez la piece dans le bon emplacement</text>';
echo '</svg>';
