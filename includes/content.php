<?php
declare(strict_types=1);

/**
 * Content store helpers: flat-file JSON with atomic writes and a one-step backup.
 */

define('ARTISPARK_ROOT', dirname(__DIR__));
define('ARTISPARK_DATA_DIR', ARTISPARK_ROOT . '/data');
define('ARTISPARK_CONTENT_FILE', ARTISPARK_DATA_DIR . '/content.json');
define('ARTISPARK_CONTENT_BACKUP', ARTISPARK_DATA_DIR . '/content.backup.json');
define('ARTISPARK_UPLOADS_DIR', ARTISPARK_ROOT . '/assets/images/uploads');

function load_content(): array
{
    foreach ([ARTISPARK_CONTENT_FILE, ARTISPARK_CONTENT_BACKUP] as $file) {
        if (!is_file($file)) {
            continue;
        }
        $json = file_get_contents($file);
        if ($json === false) {
            continue;
        }
        $data = json_decode($json, true);
        if (is_array($data)) {
            if ($file === ARTISPARK_CONTENT_BACKUP) {
                error_log('ArtiSpark: content.json unreadable/corrupt, served content.backup.json');
            }
            return $data;
        }
    }

    error_log('ArtiSpark: no readable content file found');
    return [];
}

function save_content(array $content): bool
{
    $json = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        return false;
    }

    $lock = fopen(ARTISPARK_DATA_DIR . '/content.lock', 'c');
    if ($lock === false || !flock($lock, LOCK_EX)) {
        if ($lock !== false) {
            fclose($lock);
        }
        return false;
    }

    try {
        // Keep the previous version as a backup only if it parses.
        if (is_file(ARTISPARK_CONTENT_FILE)) {
            $current = file_get_contents(ARTISPARK_CONTENT_FILE);
            if ($current !== false && json_decode($current, true) !== null) {
                file_put_contents(ARTISPARK_CONTENT_BACKUP, $current);
            }
        }

        $tmp = ARTISPARK_CONTENT_FILE . '.tmp';
        if (file_put_contents($tmp, $json) === false) {
            return false;
        }
        return rename($tmp, ARTISPARK_CONTENT_FILE);
    } finally {
        flock($lock, LOCK_UN);
        fclose($lock);
    }
}

function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Escape text and render it as paragraphs: blank lines split <p> blocks,
 * single newlines become <br>.
 */
function nl2p(?string $text, string $class = ''): string
{
    $text = trim(str_replace("\r\n", "\n", (string)$text));
    if ($text === '') {
        return '';
    }
    $attr = $class !== '' ? ' class="' . e($class) . '"' : '';
    $paragraphs = preg_split('/\n{2,}/', $text) ?: [];
    $html = '';
    foreach ($paragraphs as $p) {
        $html .= '<p' . $attr . '>' . nl2br(e(trim($p)), false) . '</p>' . "\n";
    }
    return $html;
}

function content_new_id(string $prefix): string
{
    return $prefix . '_' . bin2hex(random_bytes(4));
}

/** A section is shown unless its "visible" flag is explicitly false. */
function section_visible(array $c, string $section): bool
{
    return !array_key_exists('visible', $c[$section] ?? []) || !empty($c[$section]['visible']);
}
