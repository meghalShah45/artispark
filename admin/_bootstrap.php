<?php
declare(strict_types=1);

/**
 * Admin bootstrap: config, session, auth guard, CSRF helpers, page chrome.
 * Every admin page must require this first. Pages that are reachable without
 * login (login.php only) define ARTISPARK_PUBLIC_PAGE before requiring.
 */

require __DIR__ . '/../config/config.php';
require __DIR__ . '/../includes/content.php';

session_name('artispark_admin');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
]);
session_start();

function admin_logged_in(): bool
{
    return !empty($_SESSION['admin_authed']);
}

if (!defined('ARTISPARK_PUBLIC_PAGE') && !admin_logged_in()) {
    header('Location: login.php');
    exit;
}

// --- CSRF ---

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_verify(): void
{
    $sent = (string)($_POST['csrf_token'] ?? '');
    if ($sent === '' || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $sent)) {
        http_response_code(403);
        exit('Invalid or expired form token. Go back, reload the page, and try again.');
    }
}

// --- Small helpers shared by editor pages ---

function admin_redirect(string $script, array $params = []): void
{
    $qs = $params ? '?' . http_build_query($params) : '';
    header('Location: ' . $script . $qs);
    exit;
}

function post_str(string $key, int $maxLen = 20000): string
{
    $value = trim(str_replace("\r\n", "\n", (string)($_POST[$key] ?? '')));
    return mb_substr($value, 0, $maxLen);
}

// --- Page chrome ---

function admin_page_start(string $title, string $active = ''): void
{
    $links = [
        'index.php' => 'Dashboard',
        'edit-hero.php' => 'Hero',
        'edit-about.php' => 'About',
        'edit-services.php' => 'Services',
        'edit-testimonials.php' => 'Testimonials',
        'edit-gallery.php' => 'Gallery',
        'edit-contact.php' => 'Contact Info',
        'edit-seo.php' => 'SEO & Footer',
    ];
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($title) ?> · ArtiSpark Admin</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
<header class="topbar">
    <div class="topbar-brand">
        <img src="../logo_artispark.jpg" alt="" class="topbar-logo">
        <span>ArtiSpark Admin</span>
    </div>
    <nav class="topbar-nav">
        <a href="../index.php" target="_blank" rel="noopener">View site ↗</a>
        <form method="post" action="logout.php" class="inline-form">
            <?= csrf_field() ?>
            <button type="submit" class="btn-link">Log out</button>
        </form>
    </nav>
</header>
<div class="layout">
    <aside class="sidebar">
        <ul>
<?php foreach ($links as $href => $label): ?>
            <li><a href="<?= e($href) ?>"<?= $active === $href ? ' class="active"' : '' ?>><?= e($label) ?></a></li>
<?php endforeach; ?>
        </ul>
    </aside>
    <main class="content">
        <h1><?= e($title) ?></h1>
<?php if (isset($_GET['saved'])): ?>
        <div class="flash flash-ok">Saved. Your changes are live on the website.</div>
<?php endif; ?>
<?php if (isset($_GET['error'])): ?>
        <div class="flash flash-err"><?= e((string)$_GET['error']) ?></div>
<?php endif; ?>
    <?php
}

function admin_page_end(): void
{
    echo "</main>\n</div>\n</body>\n</html>\n";
}
