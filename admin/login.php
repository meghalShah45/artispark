<?php
declare(strict_types=1);

define('ARTISPARK_PUBLIC_PAGE', true);
require __DIR__ . '/_bootstrap.php';

const LOGIN_MAX_ATTEMPTS = 5;
const LOGIN_LOCKOUT_SECONDS = 15 * 60;
define('LOGIN_ATTEMPTS_FILE', ARTISPARK_DATA_DIR . '/login_attempts.json');

if (admin_logged_in()) {
    admin_redirect('index.php');
}

function login_attempts_load(): array
{
    if (!is_file(LOGIN_ATTEMPTS_FILE)) {
        return [];
    }
    $data = json_decode((string)file_get_contents(LOGIN_ATTEMPTS_FILE), true);
    return is_array($data) ? $data : [];
}

function login_attempts_save(array $attempts): void
{
    // Drop stale entries so the file never grows unbounded.
    $now = time();
    foreach ($attempts as $ip => $entry) {
        if (($entry['last'] ?? 0) < $now - LOGIN_LOCKOUT_SECONDS * 4) {
            unset($attempts[$ip]);
        }
    }
    file_put_contents(LOGIN_ATTEMPTS_FILE, json_encode($attempts), LOCK_EX);
}

$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $attempts = login_attempts_load();
    $entry = $attempts[$ip] ?? ['count' => 0, 'last' => 0];
    $lockedUntil = ($entry['count'] >= LOGIN_MAX_ATTEMPTS)
        ? $entry['last'] + LOGIN_LOCKOUT_SECONDS
        : 0;

    if ($lockedUntil > time()) {
        $error = 'Too many failed attempts. Try again in ' . (int)ceil(($lockedUntil - time()) / 60) . ' minute(s).';
    } else {
        if ($lockedUntil > 0) {
            // Lockout expired — start counting fresh.
            $entry = ['count' => 0, 'last' => 0];
        }
        $username = post_str('username', 200);
        $password = (string)($_POST['password'] ?? '');

        $userOk = hash_equals(ADMIN_USERNAME, $username);
        $passOk = password_verify($password, ADMIN_PASSWORD_HASH);

        if ($userOk && $passOk) {
            unset($attempts[$ip]);
            login_attempts_save($attempts);
            session_regenerate_id(true);
            $_SESSION['admin_authed'] = true;
            admin_redirect('index.php');
        }

        $entry['count']++;
        $entry['last'] = time();
        $attempts[$ip] = $entry;
        login_attempts_save($attempts);
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Log in · ArtiSpark Admin</title>
    <link rel="icon" type="image/png" href="../favicon.png">
    <link rel="stylesheet" href="admin.css">
</head>
<body class="login-body">
    <div class="login-card">
        <img src="../logo_artispark.jpg" alt="ArtiSpark" class="login-logo">
        <h1>Admin Login</h1>
<?php if ($error !== ''): ?>
        <div class="flash flash-err"><?= e($error) ?></div>
<?php endif; ?>
        <form method="post" action="login.php">
            <?= csrf_field() ?>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" autocomplete="username" required autofocus>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" autocomplete="current-password" required>
            <button type="submit" class="btn">Log in</button>
        </form>
    </div>
</body>
</html>
