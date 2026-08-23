<?php
declare(strict_types=1);

/**
 * ArtiSpark site configuration — LIVE VALUES. Gitignored, never commit.
 *
 * NOTE: The Gmail App Password below was previously committed to git and
 * must be rotated at https://myaccount.google.com/apppasswords — revoke the
 * old one, generate a new one, and paste it here.
 */

// --- Gmail SMTP (used by contact.php) ---
const SMTP_HOST = 'smtp.gmail.com';
const SMTP_PORT = 587;
const SMTP_USERNAME = 'hostingartispark@gmail.com';
const SMTP_PASSWORD = 'ssdg zlzt zpsi wzxf'; // ROTATE THIS — it leaked into git history
const SMTP_FROM_EMAIL = 'hostingartispark@gmail.com';
const SMTP_FROM_NAME = 'ArtiSpark';
const ADMIN_EMAIL = 'info@artispark.com.au';
const FALLBACK_SITE_URL = 'https://artispark.com.au';

// --- Admin panel login (used by admin/) ---
// Regenerate with: php -r "echo password_hash('your-password', PASSWORD_DEFAULT), PHP_EOL;"
const ADMIN_USERNAME = 'admin';
// Temporary password: 4e20ec072696 — change it (regenerate hash with the command above)
const ADMIN_PASSWORD_HASH = '$2y$12$4OYy8sYDU9MrGEDU/cK/i.aIzfeYvN5I650cIIjK5Y1fwR9yrIGYG';
