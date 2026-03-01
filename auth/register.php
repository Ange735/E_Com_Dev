<?php
/**
 * auth/register.php — Inscription étudiant ENSAM
 */
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn()) { header('Location: /index.php'); exit; }

$errors = [];
$values = ['nom'=>'','prenom'=>'','email'=>'','filiere'=>'','promo'=>''];

$filieres = ['GI'=>'Génie Industriel','GMP'=>'Génie Mécanique et Productique','GE'=>'Génie Électrique','GC'=>'Génie Civil','GM'=>'Génie des Matériaux','GCH'=>'Génie Chimique'];
$promos   = ['2024','2025','2026','2027','2028'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf'] ?? '')) { $errors[] = 'Token invalide.'; }

    $values['nom']     = sanitize($_POST['nom']     ?? '');
    $values['prenom']  = sanitize($_POST['prenom']  ?? '');
    $values['email']   = strtolower(trim($_POST['email'] ?? ''));
    $values['filiere'] = sanitize($_POST['filiere'] ?? '');
    $values['promo']   = sanitize($_POST['promo']   ?? '');
    $password          = $_POST['password']          ?? '';
    $confirm           = $_POST['confirm']           ?? '';

    // Validations
    if (strlen($values['nom'])    < 2) $errors[] = 'Nom invalide.';
    if (strlen($values['prenom']) < 2) $errors[] = 'Prénom invalide.';
    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL))    $errors[] = 'Email invalide.';
    // Optionnel : forcer l'email ENSAM
    // if (!str_ends_with($values['email'], '@ensam.ac.ma'))       $errors[] = 'Utilisez votre email ENSAM (@ensam.ac.ma).';
    if (strlen($password) < 8)    $errors[] = 'Mot de passe : minimum 8 caractères.';
    if ($password !== $confirm)   $errors[] = 'Les mots de passe ne correspondent pas.';
    if (!isset($filieres[$values['filiere']])) $errors[] = 'Filière invalide.';

    if (empty($errors)) {
        // Vérifier email unique
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$values['email']]);
        if ($stmt->fetch()) {
            $errors[] = 'Cet email est déjà utilisé.';
        } else {
            $hash  = password_hash($password, PASSWORD_BCRYPT, ['cost'=>12]);
            $token = generateToken();

            $stmt = $pdo->prepare("INSERT INTO users (nom,prenom,email,password_hash,filiere,promo,token) VALUES (?,?,?,?,?,?,?)");
            $stmt->execute([$values['nom'],$values['prenom'],$values['email'],$hash,$values['filiere'],$values['promo'],$token]);

            $newId = (int)$pdo->lastInsertId();

            // Auto-login
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$newId]);
            $user = $stmt->fetch();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user']    = $user;

            flash('success', 'Bienvenue ' . $user['prenom'] . ' ! Votre compte a été créé.');
            header('Location: /index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" /><meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Inscription — ENSAM Market</title>
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
.auth-card { background: #1a1a1a; border-radius: 12px; padding: 3rem 2rem; max-width: 500px; width: 100%; box-shadow: 0 4px 15px rgba(0,0,0,0.5); }
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
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
@media (max-width: 576px) { .form-row { grid-template-columns: 1fr; } }
  </style>
</head>
<body>
<div class="auth-wrap">
  <div class="auth-card">
    <div class="auth-logo">
      <span class="nav-logo">ENSAM<span class="nav-logo-dot">●</span>Market</span>
    </div>
    <h1 class="auth-title">Créer un compte</h1>
    <p class="auth-sub">Rejoins la communauté étudiante ENSAM</p>

    <?php foreach ($errors as $e): ?>
    <div class="alert alert-error">⚠ <?= e($e) ?></div>
    <?php endforeach; ?>

    <form method="post" action="">
      <input type="hidden" name="csrf" value="<?= csrfToken() ?>" />

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.8rem;">
        <div class="form-group">
          <label class="form-label">Prénom <span>*</span></label>
          <input class="form-control" type="text" name="prenom" value="<?= e($values['prenom']) ?>" required autofocus />
        </div>
        <div class="form-group">
          <label class="form-label">Nom <span>*</span></label>
          <input class="form-control" type="text" name="nom" value="<?= e($values['nom']) ?>" required />
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Email <span>*</span></label>
        <input class="form-control" type="email" name="email" value="<?= e($values['email']) ?>" placeholder="prenom.nom@ensam.ac.ma" required />
        <p class="form-hint">De préférence ton email ENSAM</p>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.8rem;">
        <div class="form-group">
          <label class="form-label">Filière <span>*</span></label>
          <select class="form-control" name="filiere" required>
            <option value="">— Choisir —</option>
            <?php foreach ($filieres as $k => $v): ?>
            <option value="<?= e($k) ?>" <?= $values['filiere']===$k ? 'selected':'' ?>><?= e($k) ?> — <?= e($v) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Promotion <span>*</span></label>
          <select class="form-control" name="promo">
            <option value="">— Année —</option>
            <?php foreach ($promos as $p): ?>
            <option value="<?= $p ?>" <?= $values['promo']===$p ? 'selected':'' ?>><?= $p ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Mot de passe <span>*</span></label>
        <input class="form-control" type="password" name="password" placeholder="Minimum 8 caractères" required />
      </div>

      <div class="form-group">
        <label class="form-label">Confirmer le mot de passe <span>*</span></label>
        <input class="form-control" type="password" name="confirm" required />
      </div>

      <button type="submit" class="btn btn-primary btn-full btn-lg">Créer mon compte 🎓</button>
    </form>

    <p class="auth-switch">Déjà inscrit ? <a href="/auth/login.php">Se connecter</a></p>
  </div>
</div>
</body>
</html>
