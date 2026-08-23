<?php
declare(strict_types=1);

/**
 * Image upload processing: validate, re-encode through GD (strips any embedded
 * payload/EXIF), downscale, and store in assets/images/uploads/.
 */

const IMG_MAX_BYTES = 8 * 1024 * 1024;
const IMG_MAX_SOURCE_DIM = 6000;
const IMG_MAX_OUTPUT_DIM = 1600;
const IMG_JPEG_QUALITY = 82;

/**
 * @param array $file One entry from $_FILES (name/tmp_name/error/size).
 * @return array{ok: bool, path?: string, error?: string} path is site-relative.
 */
function process_uploaded_image(array $file): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return ['ok' => false, 'error' => 'No file was selected.'];
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => 'Upload failed (code ' . (int)$file['error'] . '). Try a smaller image.'];
    }
    if (!is_uploaded_file($file['tmp_name'])) {
        return ['ok' => false, 'error' => 'Invalid upload.'];
    }
    if ($file['size'] > IMG_MAX_BYTES) {
        return ['ok' => false, 'error' => 'Image is too large (max 8 MB).'];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowed = ['image/jpeg' => 'jpeg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($allowed[$mime])) {
        return ['ok' => false, 'error' => 'Only JPG, PNG or WebP images are allowed.'];
    }

    $info = getimagesize($file['tmp_name']);
    if ($info === false) {
        return ['ok' => false, 'error' => 'The file is not a valid image.'];
    }
    [$width, $height] = $info;
    if ($width > IMG_MAX_SOURCE_DIM || $height > IMG_MAX_SOURCE_DIM) {
        return ['ok' => false, 'error' => 'Image dimensions are too large (max ' . IMG_MAX_SOURCE_DIM . 'px per side).'];
    }

    ini_set('memory_limit', '256M');

    $src = match ($allowed[$mime]) {
        'jpeg' => @imagecreatefromjpeg($file['tmp_name']),
        'png' => @imagecreatefrompng($file['tmp_name']),
        'webp' => @imagecreatefromwebp($file['tmp_name']),
    };
    if ($src === false) {
        return ['ok' => false, 'error' => 'Could not read the image file.'];
    }

    $scale = min(1.0, IMG_MAX_OUTPUT_DIM / max($width, $height));
    $newW = max(1, (int)round($width * $scale));
    $newH = max(1, (int)round($height * $scale));

    // PNG/WebP may carry transparency — keep them as PNG; JPEG stays JPEG.
    $hasAlpha = $allowed[$mime] !== 'jpeg';

    $dst = imagecreatetruecolor($newW, $newH);
    if ($hasAlpha) {
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefill($dst, 0, 0, $transparent);
    }
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $width, $height);
    imagedestroy($src);

    $base = pathinfo($file['name'] ?? 'image', PATHINFO_FILENAME);
    $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $base) ?? 'image', '-'));
    $slug = $slug !== '' ? mb_substr($slug, 0, 40) : 'image';
    $ext = $hasAlpha ? 'png' : 'jpg';
    $filename = $slug . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
    $destPath = ARTISPARK_UPLOADS_DIR . '/' . $filename;

    $saved = $hasAlpha
        ? imagepng($dst, $destPath, 8)
        : imagejpeg($dst, $destPath, IMG_JPEG_QUALITY);
    imagedestroy($dst);

    if (!$saved) {
        return ['ok' => false, 'error' => 'Could not save the processed image.'];
    }

    return ['ok' => true, 'path' => 'assets/images/uploads/' . $filename];
}

/**
 * Delete an image file, but only if it lives inside the uploads directory.
 * Original site images (banner/, services/, ...) are never touched.
 */
function delete_uploaded_image(string $relativePath): void
{
    if (strpos($relativePath, 'assets/images/uploads/') !== 0) {
        return;
    }
    $real = realpath(ARTISPARK_ROOT . '/' . $relativePath);
    if ($real !== false && strpos($real, ARTISPARK_UPLOADS_DIR . '/') === 0 && is_file($real)) {
        @unlink($real);
    }
}
