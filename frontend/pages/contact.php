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

<section id="contact" class="container my-5">
<div class="container my-5">
  <div class="profile-box">
    <h2>Contactez-nous</h2>
    <p>Nous répondons sous 24h (jours ouvrés).</p>

    <form action="traitement_contact.php" method="POST">
      <!-- Nom complet -->
      <div class="mb-3">
        <label for="nom" class="form-label">Nom complet</label>
        <input type="text" class="form-control" id="nom" name="nom" required>
      </div>

      <!-- Email -->
      <div class="mb-3">
        <label for="email" class="form-label">Adresse email</label>
        <input type="email" class="form-control" id="email" name="email" required>
      </div>

      <!-- Sujet -->
      <div class="mb-3">
        <label for="sujet" class="form-label">Sujet</label>
        <select class="form-select" id="sujet" name="sujet" required>
          <option value="">-- Sélectionnez un sujet --</option>
          <option value="support">Support technique</option>
          <option value="compte">Problème de compte</option>
          <option value="avis">Donner un avis</option>
          <option value="autre">Autre demande</option>
        </select>
      </div>

      <!-- Message -->
      <div class="mb-3">
        <label for="message" class="form-label">Message</label>
        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
      </div>

      <!-- Bouton d'envoi -->
      <button type="submit" class="btn btn-success">Envoyer</button>
    </form>

    <!-- Informations de contact -->
    <div class="mt-4">
      <h5>📍 Adresse de contact</h5>
      <p>
        EcoRide<br>
        12 rue des ÉcoMobilités<br>
        69000 Lyon, France<br>
        ✉️ contact@ecoride.fr<br>
        📞 +33 1 23 45 67 89
      </p>
      <p><strong>⏱️ Délai de réponse :</strong> sous 24h (jours ouvrés)</p>
    </div>
  </div>
</div>  
</section>
  <!-- ✅ Pied de page -->
  <?php include '../includes/footer.php'; ?>
  <?php include '../includes/mentions_legales.php'; ?>
 
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
</body>
</html>