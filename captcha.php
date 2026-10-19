<?php
declare(strict_types=1);

/**
 * CAPTCHA image for the contact form. Draws a 5-character code, stores its
 * hash in the visitor's session; contact.php verifies it one-time.
 */

session_name('artispark_site');
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'path' => '/']);
session_start();

// Unambiguous characters only (no 0/O, 1/I/L, 5/S, 8/B).
$alphabet = 'ACDEFHJKMNPQRTUVWXY34679';
$code = '';
for ($i = 0; $i < 5; $i++) {
    $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
}
$_SESSION['captcha_hash'] = password_hash(strtolower($code), PASSWORD_DEFAULT);
$_SESSION['captcha_time'] = time();

$width = 200;
$height = 56;
$im = imagecreatetruecolor($width, $height);

$bg = imagecolorallocate($im, 249, 249, 249);
imagefilledrectangle($im, 0, 0, $width, $height, $bg);

// Noise: light arcs and dots in muted brand tones
$noiseColors = [
    imagecolorallocate($im, 222, 162, 39),
    imagecolorallocate($im, 127, 179, 168),
    imagecolorallocate($im, 200, 195, 185),
];
for ($i = 0; $i < 6; $i++) {
    imagearc(
        $im,
        random_int(0, $width), random_int(0, $height),
        random_int(40, $width), random_int(20, $height),
        random_int(0, 360), random_int(0, 360),
        $noiseColors[array_rand($noiseColors)]
    );
}
for ($i = 0; $i < 120; $i++) {
    imagesetpixel($im, random_int(0, $width - 1), random_int(0, $height - 1), $noiseColors[array_rand($noiseColors)]);
}

// Characters: per-character jitter in angle, size and baseline
$font = __DIR__ . '/assets/fonts/RecklessNeue-Bold.ttf';
$ink = imagecolorallocate($im, 72, 72, 72);
$x = 14;
for ($i = 0; $i < 5; $i++) {
    $size = random_int(22, 27);
    $angle = random_int(-18, 18);
    $y = random_int(38, 46);
    imagettftext($im, $size, $angle, $x, $y, $ink, $font, $code[$i]);
    $x += random_int(28, 31);
}

header('Content-Type: image/png');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
imagepng($im);
imagedestroy($im);
