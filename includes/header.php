<?php
/**
 * includes/header.php
 * Variables attendues avant l'include :
 *   $pageTitle  (string)
 *   $activeNav  (string) — 'home' | 'shop' | 'account' | ...
 */
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/functions.php';

$pageTitle ??= 'ENSAM Market';
$activeNav ??= '';
$user       = currentUser();
$cartCount  = 0;
if ($user) {
    require_once __DIR__ . '/db.php';
    $cartCount = getCartCount($pdo, $user['id']);
}
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= e($pageTitle) ?> — ENSAM Market</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="stylesheet" href="/assets/css/style.css" />
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎓</text></svg>" />
</head>
<body>

<nav class="navbar" id="navbar">
  <div class="container">
    <div class="nav-inner">
      <!-- Logo -->
      <a href="index.php" class="nav-logo">
        <span>ENSAM</span><span class="nav-logo-dot">●</span><span>Market</span>
      </a>

      <!-- Links -->
      <div class="nav-links">
        <a href="index.php"   class="<?= $activeNav==='home'  ? 'active':'' ?>">Accueil</a>
        <a href="shop.php"    class="<?= $activeNav==='shop'  ? 'active':'' ?>">Catalogue</a>
        <?php if ($user && $user['mode_actuel'] === 'seller'): ?>
        <a href="seller/dashboard.php" class="<?= $activeNav==='seller' ? 'active':'' ?>">Mon Espace Vendeur</a>
        <?php endif; ?>
        <?php if ($user && $user['role'] === 'admin'): ?>
        <a href="admin/index.php" class="<?= $activeNav==='admin' ? 'active':'' ?>">Admin</a>
        <?php endif; ?>
      </div>

      <!-- Right actions -->
      <div class="nav-right">
        <?php if ($user): ?>
          <!-- Mode badge -->
          <span class="nav-mode-badge nav-mode-<?= $user['mode_actuel'] ?>">
            <?= $user['mode_actuel'] === 'seller' ? '🏪 Vendeur' : '🛍 Acheteur' ?>
          </span>

          <!-- Cart -->
          <a href="buyer/cart.php" class="nav-icon" title="Panier">
            🛒
            <?php if ($cartCount > 0): ?>
            <span class="badge-count"><?= $cartCount ?></span>
            <?php endif; ?>
          </a>

          <!-- User dropdown -->
          <div class="nav-dropdown">
            <div class="nav-avatar">
              <?php if ($user['avatar']): ?>
                <img src="<?= e($user['avatar']) ?>" alt="avatar" />
              <?php else: ?>
                <?= mb_strtoupper(mb_substr($user['prenom'], 0, 1)) ?>
              <?php endif; ?>
            </div>
            <div class="dropdown-menu">
              <div style="padding:.6rem .8rem 0;">
                <div style="font-weight:600;color:var(--white);font-size:.88rem;"><?= e($user['prenom'].' '.$user['nom']) ?></div>
                <div style="font-size:.72rem;color:var(--muted);"><?= e($user['filiere'] ?? '') ?> · <?= e($user['promo'] ?? '') ?></div>
              </div>
              <div class="dropdown-divider"></div>
              <a href="account/profile.php"          class="dropdown-item">👤 Mon Profil</a>
              <a href="buyer/orders.php"              class="dropdown-item">📦 Mes Commandes</a>
              <a href="buyer/wishlist.php"            class="dropdown-item">❤️ Wishlist</a>
              <a href="account/switch-mode.php"       class="dropdown-item">🔄 Changer de mode</a>
              <?php if ($user['mode_actuel'] === 'seller'): ?>
              <a href="seller/dashboard.php"          class="dropdown-item">🏪 Dashboard Vendeur</a>
              <?php endif; ?>
              <div class="dropdown-divider"></div>
              <a href="auth/logout.php"               class="dropdown-item danger">🚪 Déconnexion</a>
            </div>
          </div>

        <?php else: ?>
          <a href="auth/login.php"    class="btn btn-outline btn-sm">Connexion</a>
          <a href="auth/register.php" class="btn btn-primary btn-sm">S'inscrire</a>
        <?php endif; ?>

        <!-- Hamburger -->
        <button class="hamburger" id="hamburger">
          <span></span><span></span><span></span>
        </button>
      </div>
    </div>
  </div>
</nav>

<!-- Mobile nav -->
<div class="mobile-nav" id="mobile-nav">
  <a href="index.php">🏠 Accueil</a>
  <a href="shop.php">🛍 Catalogue</a>
  <?php if ($user): ?>
  <a href="buyer/cart.php">🛒 Panier (<?= $cartCount ?>)</a>
  <a href="buyer/orders.php">📦 Mes commandes</a>
  <a href="account/profile.php">👤 Mon profil</a>
  <a href="account/switch-mode.php">🔄 Changer de mode</a>
  <?php if ($user['mode_actuel'] === 'seller'): ?>
  <a href="seller/dashboard.php">🏪 Espace Vendeur</a>
  <?php endif; ?>
  <a href="auth/logout.php">🚪 Déconnexion</a>
  <?php else: ?>
  <a href="auth/login.php">🔑 Connexion</a>
  <a href="auth/register.php">✏️ S'inscrire</a>
  <?php endif; ?>
</div>

<!-- Flash message -->
<?php if ($flash): ?>
<div style="position:fixed;top:calc(var(--nav-h)+.8rem);left:50%;transform:translateX(-50%);z-index:9999;min-width:300px;max-width:500px;">
  <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['msg']) ?></div>
</div>
<script>setTimeout(()=>document.querySelector('.alert')?.remove(), 4000);</script>
<?php endif; ?>

<main>
