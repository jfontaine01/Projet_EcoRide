<?php define('ASSET_PATH', '/Projet_EcoRide/frontend/assets/');?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>EcoRide - Covoiturage écologique</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= ASSET_PATH ?>css/style.css" rel="stylesheet">
</head>
<body>

<!-- 🧭 Menu de navigation -->
<?php include '../includes/navbar.php';?>

<div class="container my-5">
  <div class="profile-box">
    <h2 class="mb-4">Créer un compte </h2>
    <h6 class="mb-4">Rejoignez notre communauté écoresponsable </h6>

    <?php if (isset($_GET['error'])): ?>
        <div class="error">
            <?php
                $messages = explode(' | ', $_GET['error']);
                foreach ($messages as $msg) {
                    echo "❌ " . htmlspecialchars($msg) . "<br>";
                }
            ?>
        </div>
    <?php endif; ?>

    <form id="signupForm" action="../../backend/controllers/inscription.php" method="POST">
      <!-- Pseudo -->
      <div class="mb-3">
        <label for="pseudo" class="form-label">Pseudo</label>
        <input type="text" class="form-control" id="pseudo" name="pseudo" required value="<?= htmlspecialchars($pseudo ?? '') ?>">
      </div>

      <!-- Email -->
      <div class="mb-3">
        <label for="email" class="form-label">Adresse email</label>
        <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($email ?? '') ?>">
      </div>

      <!-- Mot de passe -->
      <div class="mb-3">
        <label for="password" class="form-label">Mot de passe</label>
        <input type="password" class="form-control" id="password" name="password" minlength="8" required>
        <div class="form-text">Minimum 8 caractères</div>
      </div>

      <!-- Confirmation mot de passe -->
      <div class="mb-3">
        <label for="confirmPassword" class="form-label">Confirmer le mot de passe</label>
        <input type="password" class="form-control" id="confirmPassword" required>
      </div>

      <!-- Bouton -->
      <button type="submit" class="btn btn-success">S’inscrire</button>
    </form>

    <!-- Lien vers la connexion -->
    <p class="mt-3">Déjà inscrit ? <a href="login.php">Connectez-vous ici</a></p>
  </div>
</div>  
 
  <!-- JS de validation -->
  <script>
    document.getElementById('signupForm').addEventListener('submit', function (e) {
      const pwd = document.getElementById('password').value;
      const confirm = document.getElementById('confirmPassword').value;

      if (pwd !== confirm) {
        e.preventDefault();
        alert("Les mots de passe ne correspondent pas.");
      }
    });
  </script>

  <!-- ✅ Pied de page -->
  <?php include '../includes/footer.php'; ?>
  <?php include '../includes/mentions_legales.php'; ?>
 
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>  

</body>
</html>