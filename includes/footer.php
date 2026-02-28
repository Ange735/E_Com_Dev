<?php /** includes/footer.php */ ?>
</main>

<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      <div>
        <div class="nav-logo" style="margin-bottom:1rem;">ENSAM<span style="color:var(--green)">●</span>Market</div>
        <p style="font-size:.83rem;color:var(--muted);max-width:240px;line-height:1.6;">La marketplace des étudiants de l'École Nationale Supérieure des Arts et Métiers.</p>
      </div>
      <div class="footer-col">
        <h4>Catalogue</h4>
        <ul>
          <li><a href="/shop.php?cat=livres">Livres & Cours</a></li>
          <li><a href="/shop.php?cat=electronique">Électronique</a></li>
          <li><a href="/shop.php?cat=vetements">Vêtements</a></li>
          <li><a href="/shop.php?cat=services">Services</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Mon Compte</h4>
        <ul>
          <li><a href="/auth/login.php">Connexion</a></li>
          <li><a href="/auth/register.php">S'inscrire</a></li>
          <li><a href="/buyer/orders.php">Mes commandes</a></li>
          <li><a href="/seller/dashboard.php">Espace vendeur</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Aide</h4>
        <ul>
          <li><a href="#">FAQ</a></li>
          <li><a href="#">Règles d'utilisation</a></li>
          <li><a href="#">Signaler un problème</a></li>
          <li><a href="#">Contact</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>&copy; <?= date('Y') ?> ENSAM Market — Tous droits réservés</span>
      <span>Fait avec ❤️ par et pour les étudiants ENSAM</span>
    </div>
  </div>
</footer>

<script src="/assets/js/main.js" defer></script>
</body>
</html>
