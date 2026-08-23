<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

// SMTP settings live in config/config.php (gitignored). Copy config/config.sample.php to create it.
require __DIR__ . '/config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, 'Method not allowed.', 405);
}

function field(string $key): string
{
    return trim((string)($_POST[$key] ?? ''));
}

function respond(bool $success, string $message, int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

function clean_header_value(string $value): string
{
    return str_replace(["\r", "\n"], '', $value);
}

function smtp_read($socket): string
{
    $response = '';

    while (($line = fgets($socket, 515)) !== false) {
        $response .= $line;

        if (strlen($line) >= 4 && $line[3] === ' ') {
            break;
        }
    }

    return $response;
}

function smtp_command($socket, string $command, array $expectedCodes): string
{
    if ($command !== '') {
        fwrite($socket, $command . "\r\n");
    }

    $response = smtp_read($socket);
    $code = (int)substr($response, 0, 3);

    if (!in_array($code, $expectedCodes, true)) {
        throw new RuntimeException('SMTP command failed: ' . trim($response));
    }

    return $response;
}

function encode_header(string $value): string
{
    return '=?UTF-8?B?' . base64_encode($value) . '?=';
}

function html_escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function site_url(string $path = ''): string
{
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'https';
    $base = $host !== '' ? $scheme . '://' . $host : FALLBACK_SITE_URL;

    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

function nl2br_escaped(string $value): string
{
    return nl2br(html_escape($value), false);
}

function email_shell(string $title, string $introHtml, string $contentHtml): string
{
    $logoUrl = site_url('logo_artispark.jpg');

    return '<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>' . html_escape($title) . '</title>
</head>
<body style="margin:0;padding:0;background:#faf8f5;font-family:Arial,Helvetica,sans-serif;color:#2f302f;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#faf8f5;margin:0;padding:28px 14px;">
    <tr>
      <td align="center">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;border:2px dashed #8ebdb2;">
          <tr>
            <td style="padding:28px 28px 18px;text-align:center;border-bottom:2px dashed #d9a547;">
              <img src="' . html_escape($logoUrl) . '" alt="ArtiSpark" width="96" style="display:block;margin:0 auto 14px;max-width:96px;height:auto;">
              <h1 style="margin:0;font-family:Georgia,Times,serif;font-size:30px;font-weight:400;letter-spacing:4px;text-transform:uppercase;color:#2f302f;">' . html_escape($title) . '</h1>
              <div style="width:72px;height:3px;background:#8ebdb2;margin:16px auto 0;"></div>
            </td>
          </tr>
          <tr>
            <td style="padding:26px 28px 8px;font-size:16px;line-height:1.7;color:#4f5652;">
              ' . $introHtml . '
            </td>
          </tr>
          <tr>
            <td style="padding:10px 28px 30px;">
              ' . $contentHtml . '
            </td>
          </tr>
        </table>
        <p style="max-width:640px;margin:16px auto 0;font-size:12px;line-height:1.5;color:#7a7a7a;text-align:center;">ArtiSpark | Wantirna & Springvale</p>
      </td>
    </tr>
  </table>
</body>
</html>';
}

function detail_box(string $label, string $valueHtml): string
{
    return '<div style="border:1.5px dashed #8ebdb2;padding:14px 16px;margin:0 0 12px;background:#fffdfa;">
        <div style="font-size:12px;letter-spacing:2px;text-transform:uppercase;color:#7c8984;margin-bottom:6px;">' . html_escape($label) . '</div>
        <div style="font-size:16px;line-height:1.6;color:#2f302f;">' . $valueHtml . '</div>
    </div>';
}

function build_admin_email_html(string $name, string $email, string $phone, string $message): string
{
    $intro = '<p style="margin:0;">A new inquiry was submitted through the ArtiSpark website.</p>';
    $content = detail_box('Name', html_escape($name))
        . detail_box('Email', '<a href="mailto:' . html_escape($email) . '" style="color:#6fa99c;text-decoration:none;">' . html_escape($email) . '</a>')
        . detail_box('Phone', html_escape($phone))
        . detail_box('Message', nl2br_escaped($message));

    return email_shell('New Inquiry', $intro, $content);
}

function build_user_email_html(string $name, string $message): string
{
    $intro = '<p style="margin:0 0 12px;">Hi ' . html_escape($name) . ',</p>'
        . '<p style="margin:0;">Thank you for getting in touch with ArtiSpark. We have received your inquiry and will get back to you soon.</p>';
    $content = ''
        . '<div style="margin:16px 0 0;font-size:14px;line-height:1.6;color:#4f5652;">
            Warm regards,<br><strong style="color:#2f302f;">ArtiSpark</strong>
        </div>';

    return email_shell('Thank You', $intro, $content);
}

function build_email_message(string $toEmail, string $toName, string $subject, string $body, string $replyToEmail, string $replyToName): string
{
    $headers = [
        'From: ' . encode_header(SMTP_FROM_NAME) . ' <' . SMTP_FROM_EMAIL . '>',
        'To: ' . encode_header($toName) . ' <' . $toEmail . '>',
        'Reply-To: ' . encode_header($replyToName) . ' <' . $replyToEmail . '>',
        'Subject: ' . encode_header($subject),
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
    ];

    return implode("\r\n", $headers) . "\r\n\r\n" . $body;
}

function smtp_send(string $toEmail, string $toName, string $subject, string $body, string $replyToEmail, string $replyToName): void
{
    if (SMTP_PASSWORD === 'PASTE_GMAIL_APP_PASSWORD_HERE') {
        throw new RuntimeException('SMTP password is not configured.');
    }

    $context = stream_context_create([
        'ssl' => [
            'verify_peer' => true,
            'verify_peer_name' => true,
            'allow_self_signed' => false,
        ],
    ]);

    $socket = stream_socket_client(
        'tcp://' . SMTP_HOST . ':' . SMTP_PORT,
        $errno,
        $errstr,
        30,
        STREAM_CLIENT_CONNECT,
        $context
    );

    if (!$socket) {
        throw new RuntimeException('Could not connect to SMTP server: ' . $errstr);
    }

    stream_set_timeout($socket, 30);

    try {
        smtp_command($socket, '', [220]);
        smtp_command($socket, 'EHLO ' . ($_SERVER['SERVER_NAME'] ?? 'localhost'), [250]);
        smtp_command($socket, 'STARTTLS', [220]);

        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            throw new RuntimeException('Could not start encrypted SMTP connection.');
        }

        smtp_command($socket, 'EHLO ' . ($_SERVER['SERVER_NAME'] ?? 'localhost'), [250]);
        smtp_command($socket, 'AUTH LOGIN', [334]);
        smtp_command($socket, base64_encode(SMTP_USERNAME), [334]);
        smtp_command($socket, base64_encode(SMTP_PASSWORD), [235]);
        smtp_command($socket, 'MAIL FROM:<' . SMTP_FROM_EMAIL . '>', [250]);
        smtp_command($socket, 'RCPT TO:<' . $toEmail . '>', [250, 251]);
        smtp_command($socket, 'DATA', [354]);

        $message = build_email_message($toEmail, $toName, $subject, $body, $replyToEmail, $replyToName);
        fwrite($socket, str_replace("\n.", "\n..", $message) . "\r\n.\r\n");
        smtp_command($socket, '', [250]);
        smtp_command($socket, 'QUIT', [221]);
    } finally {
        fclose($socket);
    }
}

$name = field('name');
$email = field('email');
$phone = field('phone');
$message = field('message');
$honeypot = field('website');

if ($honeypot !== '') {
    respond(true, 'Thank you. Your inquiry has been received.');
}

if ($name === '' || $email === '' || $message === '') {
    respond(false, 'Please complete your name, email, and message.', 422);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(false, 'Please enter a valid email address.', 422);
}

if (strlen($name) > 120 || strlen($email) > 180 || strlen($phone) > 60 || strlen($message) > 5000) {
    respond(false, 'Please shorten your inquiry and try again.', 422);
}

$safeName = clean_header_value($name);
$safeEmail = clean_header_value($email);
$safePhone = $phone !== '' ? $phone : 'Not provided';

$adminSubject = 'New inquiry from ArtiSpark website';
$adminBody = build_admin_email_html($name, $email, $safePhone, $message);

$userSubject = 'Thank you for contacting ArtiSpark';
$userBody = build_user_email_html($name, $message);

try {
    smtp_send(ADMIN_EMAIL, 'ArtiSpark Admin', $adminSubject, $adminBody, $safeEmail, $safeName);
    smtp_send($safeEmail, $safeName, $userSubject, $userBody, SMTP_FROM_EMAIL, SMTP_FROM_NAME);
} catch (Throwable $error) {
    error_log('ArtiSpark contact form error: ' . $error->getMessage());
    respond(false, 'Sorry, we could not send your message right now. Please email hostingartispark@gmail.com directly.', 500);
}

respond(true, 'Thank you. Your inquiry has been sent successfully.');
