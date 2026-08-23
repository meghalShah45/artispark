<?php
declare(strict_types=1);

/**
 * ArtiSpark site configuration — SAMPLE.
 *
 * Copy this file to config.php (same directory) and fill in the real values.
 * config.php is gitignored and must NEVER be committed.
 */

// --- Gmail SMTP (used by contact.php) ---
const SMTP_HOST = 'smtp.gmail.com';
const SMTP_PORT = 587;
const SMTP_USERNAME = 'your-gmail-address@gmail.com';
const SMTP_PASSWORD = 'PASTE_GMAIL_APP_PASSWORD_HERE'; // 16-char Gmail App Password
const SMTP_FROM_EMAIL = 'your-gmail-address@gmail.com';
const SMTP_FROM_NAME = 'ArtiSpark';
const ADMIN_EMAIL = 'info@artispark.com.au';
const FALLBACK_SITE_URL = 'https://artispark.com.au';

// --- Admin panel login (used by admin/) ---
// Generate the hash by running:
//   php -r "echo password_hash('your-chosen-password', PASSWORD_DEFAULT), PHP_EOL;"
const ADMIN_USERNAME = 'admin';
const ADMIN_PASSWORD_HASH = 'PASTE_PASSWORD_HASH_HERE';
