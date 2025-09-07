<?php
$isConnected = isset($_SESSION['user_id']);
$pseudo = $_SESSION['pseudo'] ?? 'Utilisateur';
$role = $_SESSION['role'] ?? 0;
?>

<nav class="navbar navbar-expand-md navbar-dark bg-success px-4">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">EcoRide</a>

    <!-- Menu classique visible à partir de md -->
    <div class="collapse navbar-collapse" id="mainMenu">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link text-white" href="index.php">🏠 Accueil</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="covoiturage.php">🚗 Covoiturages</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="contact.php">📞 Contact</a></li>
        <?php if (!$isConnected): ?>
          <li class="nav-item"><a class="nav-link text-white" href="login.php">🔐 Se connecter / S’inscrire</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link text-white" href="espace_utilisateur.php">👤 <?= htmlspecialchars($pseudo) ?></a></li>
          <li class="nav-item"><a class="nav-link text-white" href="logout.php">🚪 Déconnexion</a></li>
        <?php endif; ?>
      </ul>
    </div>
    <!-- Bouton offcanvas pour mobile -->
    <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sideMenu" aria-controls="sideMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
  </div>
</nav>

<!-- Menu offcanvas à droite -->
<div class="offcanvas offcanvas-end bg-success text-white" tabindex="-1" id="sideMenu" aria-labelledby="sideMenuLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="sideMenuLabel">Menu</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Fermer"></button>
  </div>
  <div class="offcanvas-body">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link text-white" href="index.php">🏠 Accueil</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="covoiturage.php">🚗 Covoiturages</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="contact.php">📞 Contact</a></li>
      <?php if (!$isConnected): ?>
        <li class="nav-item"><a class="nav-link text-white" href="login.php">🔐 Se connecter / S’inscrire</a></li>
      <?php else: ?>
        <li class="nav-item"><a class="nav-link text-white" href="espace_utilisateur.php">👤 <?= htmlspecialchars($pseudo) ?></a></li>
        <li class="nav-item"><a class="nav-link text-white" href="logout.php">🚪 Déconnexion</a></li>
      <?php endif; ?>
    </ul>
  </div>
</div>
