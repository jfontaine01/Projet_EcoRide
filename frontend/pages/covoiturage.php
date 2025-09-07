<?php

require_once(__DIR__ . '/../../backend/config/database.php');
$pdo = getPDO();

define('ASSET_PATH', '/Projet_EcoRide/frontend/assets/');

// Connexion à la base
//$pdo = new PDO("mysql:host=localhost;dbname=ecoride_db;charset=utf8", "root", "");
//$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Récupération des valeurs uniques
$lieudepart = $pdo->query("SELECT DISTINCT lieu_depart FROM covoiturage ORDER BY lieu_depart")->fetchAll(PDO::FETCH_COLUMN);
$lieuarrivee = $pdo->query("SELECT DISTINCT lieu_arrivee FROM covoiturage ORDER BY lieu_arrivee")->fetchAll(PDO::FETCH_COLUMN);
$date = $pdo->query("SELECT DISTINCT DATE(date_depart) FROM covoiturage ORDER BY date_depart")->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>EcoRide – Recherche de covoiturage</title>
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
    <h4>Affiner votre recherche</h4>
    <form action="" method="POST" class="row g-3">
      <div class="col-md-3">
        <label>Ville de départ</label>
        <select name="lieudepart" class="form-select">
          <option value="">Toutes</option>
          <?php foreach ($lieudepart as $dep): ?>
            <option value="<?= $dep ?>"><?= $dep ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label>Ville d'arrivée</label>
        <select name="lieuarrivee" class="form-select">
          <option value="">Toutes</option>
          <?php foreach ($lieuarrivee as $arr): ?>
            <option value="<?= $arr ?>"><?= $arr ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label>Date</label>
        <select name="date" class="form-select">
          <option value="">Toutes</option>
          <?php foreach ($date as $d): ?>
            <option value="<?= $d ?>"><?= date('d/m/Y', strtotime($d)) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label>Prix maximum (€)</label>
        <input type="number" name="prix" class="form-control" min="0" required>
      </div>
      <div class="col-md-3">
        <label>Écologique</label>
        <select name="ecologique" class="form-select">
          <option value="">Tous</option>
          <option value="electrique">✅ Electrique</option>
          <option value="hybride">✅ Hybride</option>
          <option value="thermique">❌ Thermique</option>
        </select>
      </div>
      <div class="col-md-3 d-flex align-items-end">
        <button type="submit" class="btn btn-success w-100">Filtrer</button>
      </div>
    </form>
    <?php include('../../backend/controllers/recherche.php'); ?>
  </div>
</div>

  <!-- ✅ Pied de page -->
  <?php include '../includes/footer.php'; ?>
  <?php include '../includes/mentions_legales.php'; ?>
 
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
</body>
</html>
