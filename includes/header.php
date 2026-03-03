<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' — ' : '' ?>ENSAM Market</title>
    
    <!-- Lien vers le CSS (Chemin absolu pour XAMPP) -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    
    <!-- Polices Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="container nav-inner">
        <!-- Logo -->
        <a href="<?= BASE_URL ?>index.php" class="nav-logo">
            ENSAM<span class="nav-logo-dot">●</span>Market
        </a>

        <!-- Liens Desktop -->
        <div class="nav-links">
            <a href="<?= BASE_URL ?>index.php" class="<?= ($activeNav ?? '') === 'home' ? 'active' : '' ?>">Accueil</a>
            <a href="<?= BASE_URL ?>shop.php" class="<?= ($activeNav ?? '') === 'shop' ? 'active' : '' ?>">Catalogue</a>
            <?php if (isLoggedIn()): ?>
                <a href="<?= BASE_URL ?>buyer/orders.php">Mes Commandes</a>
            <?php endif; ?>
        </div>

        <!-- Droite (Panier + User) -->
        <div class="nav-right">
            <?php if (isLoggedIn()): 
                $u = currentUser();
                $cartCount = getCartCount($pdo, $_SESSION['user_id']);
            ?>
                <!-- Mode Badge -->
                <span class="nav-mode-badge <?= $u['mode_actuel'] === 'seller' ? 'nav-mode-seller' : 'nav-mode-buyer' ?>">
                    <?= $u['mode_actuel'] === 'seller' ? 'Vendeur' : 'Acheteur' ?>
                </span>

                <!-- Panier -->
                <a href="<?= BASE_URL ?>buyer/cart.php" class="nav-icon">
                    🛒
                    <?php if ($cartCount > 0): ?>
                        <span class="badge-count"><?= $cartCount ?></span>
                    <?php endif; ?>
                </a>

                <!-- Dropdown User -->
                <div class="nav-dropdown">
                    <div class="nav-avatar">
                        <?= mb_strtoupper(mb_substr($u['prenom'], 0, 1)) ?>
                    </div>
                    <div class="dropdown-menu">
                        <div style="padding: .8rem 1rem; border-bottom:1px solid #333;">
                            <div style="font-weight:600;color:#fff;"><?= e($u['prenom'] . ' ' . $u['nom']) ?></div>
                            <div style="font-size:.75rem;color:#888;"><?= e($u['email']) ?></div>
                        </div>
                        <a href="<?= BASE_URL ?>account/profile.php" class="dropdown-item">Mon Profil</a>
                        <a href="<?= BASE_URL ?>buyer/wishlist.php" class="dropdown-item">Ma Wishlist</a>
                        <a href="<?= BASE_URL ?>account/switch-mode.php" class="dropdown-item">
                            Passer en mode <?= $u['mode_actuel'] === 'seller' ? 'Acheteur' : 'Vendeur' ?>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="<?= BASE_URL ?>auth/logout.php" class="dropdown-item danger">Déconnexion</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?= BASE_URL ?>auth/login.php" class="btn btn-outline btn-sm">Connexion</a>
                <a href="<?= BASE_URL ?>auth/register.php" class="btn btn-primary btn-sm">Inscription</a>
            <?php endif; ?>

            <!-- Mobile Toggle -->
            <button class="hamburger" onclick="document.querySelector('.mobile-nav').style.display = document.querySelector('.mobile-nav').style.display==='flex'?'none':'flex'">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</nav>