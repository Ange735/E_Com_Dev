<?php
/**
 * auth/forgot-password.php
 */
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$sent  = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email invalide.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user) {
            $token = generateToken();
            $pdo->prepare("UPDATE users SET token = ? WHERE id = ?")->execute([$token, $user['id']]);
            // TODO: envoyer l'email avec le lien /auth/reset-password.php?token=$token
            // mail($email, 'Réinitialisation mot de passe — ENSAM Market', "Lien : https://yoursite.com/auth/reset-password.php?token=$token");
        }
        $sent = true; // Toujours afficher "email envoyé" pour éviter l'énumération
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Mot de passe oublié — ENSAM Market</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <style>
:root {
    --green: #1a7a4a;
    --green-lt: #2fc47c;
    --white: #ffffff;
    --text: #cccccc;
    --muted: #888888;
    --deep: #121212;
    --light: #f5f5f5;
    --font-head: 'Poppins', sans-serif;
    --font-body: 'Inter', sans-serif;
}
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: var(--font-body); background-color: var(--deep); color: var(--text); line-height: 1.6; }
a { text-decoration: none; color: inherit; }
.btn { display: inline-block; padding: 0.65rem 1.3rem; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; cursor: pointer; text-align: center; }
.btn-primary { background-color: var(--green); color: var(--white); }
.btn-primary:hover { background-color: var(--green-lt); }
.btn-full { width: 100%; }
.btn-lg { font-size: 1rem; padding: 0.8rem 1.5rem; }
.nav-logo { font-family: var(--font-head); font-weight: 700; font-size: 1.3rem; color: var(--white); }
.nav-logo-dot { color: var(--green-lt); margin: 0 0.2rem; }

.auth-wrap { display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 2rem; }
.auth-card { background: #1a1a1a; border-radius: 12px; padding: 3rem 2rem; max-width: 400px; width: 100%; box-shadow: 0 4px 15px rgba(0,0,0,0.5); }
.auth-logo { text-align: center; margin-bottom: 2rem; }
.auth-title { font-size: 2rem; font-weight: 800; color: var(--white); text-align: center; margin-bottom: 0.5rem; }
.auth-sub { text-align: center; color: var(--muted); font-size: 0.9rem; margin-bottom: 2rem; }
.form-group { margin-bottom: 1.5rem; }
.form-label { display: block; font-size: 0.85rem; font-weight: 600; color: var(--white); margin-bottom: 0.5rem; }
.form-label span { color: #ff5c5c; }
.form-control { width: 100%; background: #0a0a0a; border: 1px solid #333; border-radius: 6px; padding: 0.6rem 0.8rem; color: var(--white); font-family: inherit; }
.form-control:focus { outline: none; border-color: var(--green-lt); box-shadow: 0 0 5px rgba(46,196,124,0.4); }
.form-hint { font-size: 0.75rem; color: var(--muted); margin-top: 0.3rem; }
.alert { padding: 0.8rem; border-radius: 6px; margin-bottom: 1.5rem; font-size: 0.9rem; }
.alert-error { background: rgba(255, 92, 92, 0.15); border: 1px solid #ff5c5c; color: #ff5c5c; }
.alert-success { background: rgba(47, 196, 124, 0.15); border: 1px solid var(--green-lt); color: var(--green-lt); }
.auth-switch { text-align: center; margin-top: 1.5rem; font-size: 0.9rem; color: var(--muted); }
.auth-switch a { color: var(--green-lt); font-weight: 600; }
.auth-switch a:hover { color: var(--white); }
  </style>
</head>
<body>
<div class="auth-wrap">
  <div class="auth-card">
    <div class="auth-logo"><span class="nav-logo">ENSAM<span class="nav-logo-dot">●</span>Market</span></div>
    <h1 class="auth-title">Mot de passe oublié</h1>
    <p class="auth-sub">Saisis ton email pour recevoir un lien de réinitialisation.</p>

    <?php if ($sent): ?>
    <div class="alert alert-success">✓ Si cet email existe, un lien t'a été envoyé.</div>
    <?php else: ?>

    <?php if ($error): ?><div class="alert alert-error">⚠ <?= e($error) ?></div><?php endif; ?>

    <form method="post">
      <div class="form-group">
        <label class="form-label">Email</label>
        <input class="form-control" type="email" name="email" placeholder="ton@email.com" required autofocus />
      </div>
      <button type="submit" class="btn btn-primary btn-full">Envoyer le lien</button>
    </form>
    <?php endif; ?>

    <p class="auth-switch"><a href="/auth/login.php">← Retour à la connexion</a></p>
  </div>
</div>
</body>
</html>
