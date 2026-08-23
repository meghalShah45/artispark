<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
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

$adminEmail = 'info@artispark.com.au';
$fromEmail = 'info@artispark.com.au';
$siteName = 'ArtiSpark';
$safeName = clean_header_value($name);
$safeEmail = clean_header_value($email);

$adminSubject = 'New inquiry from ArtiSpark website';
$adminBody = "A new inquiry was submitted on the ArtiSpark website.\n\n"
    . "Name: {$name}\n"
    . "Email: {$email}\n"
    . "Phone: " . ($phone !== '' ? $phone : 'Not provided') . "\n\n"
    . "Message:\n{$message}\n";

$userSubject = 'Thank you for contacting ArtiSpark';
$userBody = "Hi {$name},\n\n"
    . "Thank you for getting in touch with ArtiSpark. We have received your inquiry and will get back to you soon.\n\n"
    . "Your message:\n{$message}\n\n"
    . "Warm regards,\n"
    . "ArtiSpark";

$baseHeaders = [
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'From: ' . $siteName . ' <' . $fromEmail . '>',
];

$adminHeaders = array_merge($baseHeaders, [
    'Reply-To: ' . $safeName . ' <' . $safeEmail . '>',
]);

$userHeaders = array_merge($baseHeaders, [
    'Reply-To: ' . $siteName . ' <' . $fromEmail . '>',
]);

$adminSent = mail($adminEmail, $adminSubject, $adminBody, implode("\r\n", $adminHeaders));
$userSent = mail($safeEmail, $userSubject, $userBody, implode("\r\n", $userHeaders));

if (!$adminSent || !$userSent) {
    respond(false, 'Sorry, we could not send your message right now. Please email info@artispark.com.au directly.', 500);
}

respond(true, 'Thank you. Your inquiry has been sent successfully.');
