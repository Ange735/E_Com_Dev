  <!-- Mobile Nav Menu -->
  <div class="mobile-nav">
      <a href="<?= BASE_URL ?>index.php">Accueil</a>
      <a href="<?= BASE_URL ?>shop.php">Catalogue</a>
      <?php if (isLoggedIn()): ?>
          <a href="<?= BASE_URL ?>buyer/orders.php">Mes Commandes</a>
          <a href="<?= BASE_URL ?>account/profile.php">Mon Profil</a>
          <a href="<?= BASE_URL ?>auth/logout.php" style="color:#ff5c5c;">Déconnexion</a>
      <?php else: ?>
          <a href="<?= BASE_URL ?>auth/login.php">Connexion</a>
      <?php endif; ?>
  </div>

  <!-- Footer -->
  <footer class="footer">
      <div class="container">
          <div class="footer-grid">
              <div class="footer-col">
                  <div class="nav-logo footer-logo">ENSAM<span>Market</span><span class="nav-logo-dot">●</span></div>
                  <p style="margin-top:1rem;color:var(--muted);line-height:1.6;">
                      La plateforme d'échange dédiée aux étudiants de l'ENSAM. Achetez, vendez, échangez en toute confiance.
                  </p>
              </div>
              <div class="footer-col">
                  <h4>Liens rapides</h4>
                  <ul>
                      <li><a href="<?= BASE_URL ?>index.php">Accueil</a></li>
                      <li><a href="<?= BASE_URL ?>shop.php">Catalogue</a></li>
                      <li><a href="<?= BASE_URL ?>auth/register.php">Inscription</a></li>
                  </ul>
              </div>
              <div class="footer-col">
                  <h4>Support</h4>
                  <ul>
                      <li><a href="#">FAQ</a></li>
                      <li><a href="#">Règles de la communauté</a></li>
                      <li><a href="#">Contact BDE</a></li>
                  </ul>
              </div>
          </div>
          <div class="footer-bottom">
              <div>&copy; <?= date('Y') ?> ENSAM Market. Projet étudiant.</div>
              <div>Fait avec ❤️ par Mehdi</div>
          </div>
      </div>
  </footer>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- Contrôles Quantité (Panier & Fiche Produit) ---
    document.body.addEventListener('click', function(e) {
        const qtyControl = e.target.closest('.qty-control');
        if (!qtyControl) return;

        const input = qtyControl.querySelector('.qty-input');
        const isMinus = e.target.classList.contains('qty-minus');
        const isPlus = e.target.classList.contains('qty-plus');

        if (input && (isMinus || isPlus)) {
            let val = parseInt(input.value, 10);
            const min = parseInt(input.min, 10);
            const max = parseInt(input.max, 10);
            if (isMinus && val > min) val--;
            if (isPlus && val < max) val++;
            input.value = val;
        }
    });

    // --- Wishlist Toggle ---
    document.body.addEventListener('click', function(e) {
        const wishBtn = e.target.closest('[data-wish]');
        if (!wishBtn) return;

        e.preventDefault();
        const productId = wishBtn.dataset.wish;

        fetch('<?= BASE_URL ?>api/wishlist-toggle.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ product_id: productId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                if (data.action === 'login_required') {
                    window.location.href = '<?= BASE_URL ?>auth/login.php?redirect=' + window.location.pathname;
                } else {
                    alert(data.error);
                }
                return;
            }

            if (data.success) {
                document.querySelectorAll(`[data-wish="${productId}"]`).forEach(btn => {
                    if (data.action === 'added') {
                        btn.classList.add('active');
                        btn.innerHTML = '♥';
                        if (btn.classList.contains('btn')) { // Product page button
                            btn.innerHTML = '❤️ Dans votre wishlist';
                            btn.style.borderColor = '#ff5c5c';
                            btn.style.color = '#ff5c5c';
                        }
                    } else { // removed
                        if (window.location.pathname.includes('/buyer/wishlist.php')) {
                            btn.closest('.product-card')?.remove();
                        } else {
                            btn.classList.remove('active');
                            btn.innerHTML = '♡';
                            if (btn.classList.contains('btn')) { // Product page button
                                btn.innerHTML = '🤍 Ajouter à la wishlist';
                                btn.style.borderColor = '';
                                btn.style.color = '';
                            }
                        }
                    }
                });
            }
        })
        .catch(err => console.error('Wishlist error:', err));
    });
});
</script>
</body>
</html>