<?php
require_once(__DIR__ . '/../../backend/config/database.php');
$pdo = getPDO();

$where = [];
$params = [];

if (!empty($_POST['lieudepart'])) {
  $where[] = "lieu_depart = ?";
  $params[] = $_POST['lieudepart'];
}
if (!empty($_POST['lieuarrivee'])) {
  $where[] = "lieu_arrivee = ?";
  $params[] = $_POST['lieuarrivee'];
}
if (!empty($_POST['date'])) {
  $where[] = "DATE(date_depart) = ?";
  $params[] = $_POST['date'];
}
if (isset($_POST['prix']) && is_numeric($_POST['prix'])) {
  $where[] = "prix_personne <= ?";
  $params[] = floatval($_POST['prix']);
}
if (!empty($_POST['ecologique'])) {
    switch ($_POST['ecologique']) {
        case 'electrique':
            $where[] = "v.energie = ?";
            $params[] = "Electrique";
            break;
        case 'hybride':
            $where[] = "v.energie = ?";
            $params[] = "Hybride";
            break;
        case 'thermique':
            $where[] = "v.energie = ?";
            $params[] = "Thermique";
            break;
    }
}

$sql = "SELECT c.*, v.energie FROM covoiturage c 
        JOIN voiture v ON c.voiture_id = v.voiture_id";

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Résultats – EcoRide</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f4fdf4;
      font-family: 'Segoe UI', sans-serif;
    }
    .profile-box {
      background: white;
      border-radius: 8px;
      padding: 30px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    footer {
      background-color: #2e7d32;
      color: white;
    }
  </style>
</head>
<body>
<div class="container my-5">
  <div class="profile-box">  
    <h2 class="mb-4 text-success">Résultats de votre recherche</h2>
    <div class="row g-4">
      <?php foreach ($results as $r): ?>
        <div class="col-md-4">
          <div class="card shadow-sm">
            <img src="<?= ASSET_PATH ?>img/conducteur.jpg" class="card-img-top" alt="Conducteur">
            <div class="card-body">
              <h5 class="card-title"><?= $r['lieu_depart'] ?> → <?= $r['lieu_arrivee'] ?></h5>
              <p class="card-text">
                <strong>Départ :</strong> <?= date('d/m/Y H:i', strtotime($r['date_depart'])) ?><br>
                <strong>Prix :</strong> <?= $r['prix_personne'] ?> €<br>
                <strong>Écologique :</strong> <?= in_array($r['energie'], ['Electrique']) ? '✅ ' . $r['energie'] : '❌ Thermique' ?>
              </p>
              <a href="detail.php?id=<?= $r['covoiturage_id'] ?>" class="btn btn-outline-success w-100">Détail</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>  

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>