<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../config/db.php';

if (!empty($_SESSION['user'])) {
    header('Location: /carolinianpos/index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? AND status = "active" LIMIT 1');
    $stmt->execute([trim($_POST['username'] ?? '')]);
    $user = $stmt->fetch();

    if ($user && password_verify($_POST['password'] ?? '', $user['password_hash'])) {
        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'username' => $user['username'],
            'full_name' => $user['full_name'],
            'role' => $user['role']
        ];
        header('Location: /carolinianpos/index.php');
        exit;
    }
    $error = 'Invalid username or password.';
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Sign in · CarolinianPOS</title><link rel="stylesheet" href="/carolinianpos/assets/css/app.css">
</head>
<body class="login-page">
<section class="login-card">
  <div class="mono-label">UNIVERSITY OF SAN CARLOS</div>
  <h1>CarolinianPOS</h1>
  <p class="lead">Point of sale and inventory operations.</p>
  <?php if ($error): ?><div class="alert danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <form method="post" class="form-stack">
    <label>Username<input name="username" required autocomplete="username"></label>
    <label>Password<input type="password" name="password" required autocomplete="current-password"></label>
    <button class="btn primary" type="submit">Sign in</button>
  </form>
  <div class="demo-note">Demo: admin / password · manager / password · cashier / password</div>
</section>
</body>
</html>
