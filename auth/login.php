<?php
/**
 * auth/login.php — Connexion étudiant
 */
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn()) { header('Location: /index.php'); exit; }

$error  = '';
$email  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf'] ?? '')) { $error = 'Token invalide, réessaie.'; }
    else {
        $email    = strtolower(trim($_POST['email']    ?? ''));
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            $error = 'Remplis tous les champs.';
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user']    = $user;

                $redirect = $_SESSION['redirect_after_login'] ?? '/index.php';
                unset($_SESSION['redirect_after_login']);
                flash('success', 'Bon retour, ' . $user['prenom'] . ' !');
                header('Location: ' . $redirect);
                exit;
            } else {
                $error = 'Email ou mot de passe incorrect.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Connexion — ENSAM Market</title>
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
    <div class="auth-logo">
      <span class="nav-logo">ENSAM<span class="nav-logo-dot">●</span>Market</span>
    </div>
    <h1 class="auth-title">Connexion</h1>
    <p class="auth-sub">Accède à ton espace étudiant</p>

    <?php if ($error): ?>
    <div class="alert alert-error">⚠ <?= e($error) ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['registered'])): ?>
    <div class="alert alert-success">✓ Compte créé ! Tu peux te connecter.</div>
    <?php endif; ?>

    <form method="post" action="">
      <input type="hidden" name="csrf" value="<?= csrfToken() ?>" />

      <div class="form-group">
        <label class="form-label">Email <span>*</span></label>
        <input class="form-control" type="email" name="email" value="<?= e($email) ?>" placeholder="prenom.nom@ensam.ac.ma" required autofocus />
      </div>

      <div class="form-group">
        <label class="form-label">Mot de passe <span>*</span></label>
        <input class="form-control" type="password" name="password" required />
        <p class="form-hint" style="text-align:right;"><a href="/auth/forgot-password.php" style="color:var(--green-lt);">Mot de passe oublié ?</a></p>
      </div>

      <button type="submit" class="btn btn-primary btn-full btn-lg">Se connecter</button>
    </form>

    <p class="auth-switch">Pas encore de compte ? <a href="/auth/register.php">S'inscrire</a></p>
  </div>
</div>
</body>
</html>
