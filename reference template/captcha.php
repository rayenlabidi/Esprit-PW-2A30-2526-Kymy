<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../lib/CaptchaService.php';

// Cette page genere l'image du captcha texte a partir du code stocke en session.
$challenge = CaptchaService::getChallenge();

if (($challenge['type'] ?? '') !== 'text') {
    $challenge = CaptchaService::refreshChallenge();
}

$code = $challenge['code'];

$width = 260;
$height = 58;

// Fallback SVG si l'extension GD de PHP n'est pas disponible.
if (!function_exists('imagecreatetruecolor')) {
    header('Content-Type: image/svg+xml');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    echo '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '">';
    echo '<rect width="100%" height="100%" fill="#f8fbff"/>';

    // Lignes et points aleatoires pour rendre la lecture automatique plus difficile.
    for ($i = 0; $i < 8; $i++) {
        echo '<line x1="' . random_int(-20, $width) . '" y1="' . random_int(0, $height) . '" x2="' . random_int(0, $width + 20) . '" y2="' . random_int(0, $height) . '" stroke="' . ($i % 2 === 0 ? '#829db9' : '#3767b5') . '" stroke-width="' . random_int(1, 2) . '" opacity="0.65"/>';
    }
    for ($i = 0; $i < 70; $i++) {
        echo '<circle cx="' . random_int(0, $width) . '" cy="' . random_int(0, $height) . '" r="' . random_int(1, 2) . '" fill="#bccdde" opacity="0.65"/>';
    }

    // Chaque caractere est legerement decale et tourne.
    for ($i = 0; $i < strlen($code); $i++) {
        $x = 20 + ($i * 29) + random_int(-3, 3);
        $y = random_int(36, 46);
        $rotate = random_int(-22, 22);
        echo '<text x="' . $x . '" y="' . $y . '" transform="rotate(' . $rotate . ' ' . $x . ' ' . $y . ')" font-family="Georgia, Arial, sans-serif" font-size="' . random_int(24, 31) . '" font-weight="700" fill="#24364a">' . htmlspecialchars($code[$i], ENT_QUOTES, 'UTF-8') . '</text>';
    }
    echo '</svg>';
    exit;
}

$image = imagecreatetruecolor($width, $height);
$bg = imagecolorallocate($image, 245, 250, 255);
$ink = imagecolorallocate($image, 35, 50, 70);
$softLine = imagecolorallocate($image, 130, 157, 185);
$noise = imagecolorallocate($image, 188, 204, 222);
$accent = imagecolorallocate($image, 55, 103, 181);

// Fond de l'image.
imagefilledrectangle($image, 0, 0, $width, $height, $bg);

// Lignes aleatoires.
for ($i = 0; $i < 6; $i++) {
    imagesetthickness($image, random_int(1, 2));
    imageline(
        $image,
        random_int(-20, $width),
        random_int(0, $height),
        random_int(0, $width + 20),
        random_int(0, $height),
        $i % 2 === 0 ? $softLine : $accent
    );
}

// Bruit pixel par pixel.
for ($i = 0; $i < 180; $i++) {
    imagesetpixel($image, random_int(0, $width - 1), random_int(0, $height - 1), $noise);
}

// Dessin des 8 caracteres du captcha.
for ($i = 0; $i < strlen($code); $i++) {
    $x = 20 + ($i * 29) + random_int(-3, 3);
    $y = random_int(17, 34);
    $font = random_int(4, 5);

    imagestring($image, $font, $x + 1, $y + 1, $code[$i], $noise);
    imagestring($image, $font, $x, $y, $code[$i], $ink);
}

imagefilter($image, IMG_FILTER_SMOOTH, 4);

// Envoi de l'image PNG au navigateur.
header('Content-Type: image/png');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
imagepng($image);
imagedestroy($image);
