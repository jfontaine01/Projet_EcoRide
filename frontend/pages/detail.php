<?php
session_start();

define('ASSET_PATH', '/Projet_EcoRide/frontend/assets/');

// Connexion à la base
$pdo = new PDO("mysql:host=localhost;dbname=ecoride_db;charset=utf8", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Récupération de l'ID du covoiturage
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    echo "<p>Identifiant invalide.</p>";
    exit;
}

$isConnected = isset($_SESSION['user_id']);
$alreadyJoined = false;

if ($isConnected) {
    $userId = $_SESSION['user_id'];

    // Vérifie si l'utilisateur a déjà participé
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM covoiturage_participants WHERE covoiturage_id = ? AND covoiturage_participants_id = ?");
    $stmt->execute([$id, $userId]);
    $alreadyJoined = $stmt->fetchColumn() > 0;
}


// Requête pour récupérer les détails du covoiturage
$stmt = $pdo->prepare("
  SELECT c.*,c.conducteur_id, v.modele, v.energie, u.nom AS conducteur_nom, u.prenom AS conducteur_prenom,
         t.fumeur, t.animaux, t.autres
  FROM covoiturage c
  JOIN voiture v ON c.voiture_id = v.voiture_id
  JOIN utilisateur u ON c.conducteur_id = u.utilisateur_id
  LEFT JOIN preferences_trajet t ON c.covoiturage_id = t.covoiturage_id
  WHERE c.covoiturage_id = ?
");

$stmt->execute([$id]);
$covoiturage = $stmt->fetch();

if (!$covoiturage) {
  echo "<p>Covoiturage introuvable.</p>";
  exit;
}

// Requête pour récupérer les avis
$stmtAvis = $pdo->prepare("SELECT * FROM avis WHERE covoiturage_id = ?");
$stmtAvis->execute([$id]);
$avisList = $stmtAvis->fetchAll();

// Requête pour récupérer les avis
$stmtMoyenne = $pdo->prepare("
SELECT AVG(note) AS moyenne
FROM avis
WHERE covoiturage_id = :covoiturage_id 
");
$stmtMoyenne->execute([
  ':covoiturage_id' => $id
]);

$moyenne = $stmtMoyenne->fetchColumn();
$noteMoyenne = $moyenne !== null ? round($moyenne, 1) : null;

$trajetId = $_GET['id'] ?? null;

// Récupération des infos du trajet
$stmt = $pdo->prepare("SELECT lieu_depart, lieu_arrivee FROM covoiturage WHERE covoiturage_id = ?");
$stmt->execute([$trajetId]);
$trajet = $stmt->fetch();

// Fonction de géocodage avec User-Agent
function getCoordinates($ville) {
    $url = "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($ville);
    $opts = [
        "http" => [
            "header" => "User-Agent: EcoRideApp/1.0\r\n"
        ]
    ];
    $context = stream_context_create($opts);
    $json = file_get_contents($url, false, $context);
    $data = json_decode($json, true);
    if (!empty($data)) {
        return [$data[0]['lat'], $data[0]['lon']];
    }
    return [null, null];
}

// Coordonnées GPS
$coordDepart = getCoordinates($trajet['lieu_depart']);
$coordArrivee = getCoordinates($trajet['lieu_arrivee']);

?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Détail du trajet – EcoRide</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <link href="<?= ASSET_PATH ?>css/style.css" rel="stylesheet">
</head>
<body>

<!-- 🧭 Menu de navigation -->
<?php include '../includes/navbar.php';?>


  <!-- 🧾 Détails du trajet -->
<div class="container my-5">
  <div class="profile-box">
    <h2 class="mb-3">Trajet <?= htmlspecialchars($covoiturage['lieu_depart']) ?> → <?= htmlspecialchars($covoiturage['lieu_arrivee']) ?></h2>
      <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
          <?php if ($covoiturage['statut'] === 'planifié'): ?>
            <a href="../../backend/controllers/demarrer_trajet.php?id=<?= $covoiturage['covoiturage_id'] ?>" class="btn btn-success">Démarrer</a>
          <?php elseif ($covoiturage['statut'] === 'démarré'): ?>
            <a href="../../backend/controllers/terminer_trajet.php?id=<?= $covoiturage['covoiturage_id'] ?>" class="btn btn-danger">Arrivée à destination</a>
          <?php endif; ?>
        </div>
        <div>
          <span class="badge bg-info text-dark">
            Statut : <?= htmlspecialchars($covoiturage['statut'] ?? 'ouvert') ?>
          </span>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <ul class="list-group">
            <li class="list-group-item"><strong>Conducteur :</strong> <?= htmlspecialchars($covoiturage['conducteur_nom']) ?></li>
            <li class="list-group-item"><strong>Date :</strong> <?= htmlspecialchars($covoiturage['date_depart']) ?></li>
            <li class="list-group-item"><strong>Heure de départ :</strong> <?= htmlspecialchars($covoiturage['heure_depart']) ?></li>
            <li class="list-group-item"><strong>Places disponibles :</strong> <?= htmlspecialchars($covoiturage['nb_place']) ?></li>
            <li class="list-group-item"><strong>Prix :</strong> <?= htmlspecialchars($covoiturage['prix_personne']) ?> €</li>
            <li class="list-group-item"><strong>Véhicule :</strong> <?= htmlspecialchars($covoiturage['modele']) ?> – <?= htmlspecialchars($covoiturage['energie']) ?></li>
            <li class="list-group-item"><strong>Crédit requis :</strong> 18 €</li>
          </ul>
        </div>
        <div class="col-md-6">
          <div id="map"></div>
        </div>
        <div class="col-md-6">
          <?php
          $fumeur = $covoiturage['fumeur'] ?? null;
          $animaux = $covoiturage['animaux'] ?? null;
          $autres = $covoiturage['autres'] ?? '';

          if (!is_null($fumeur) || !is_null($animaux) || trim($autres) !== ''):
          ?>
            <h5 class="mt-4">Préférences du trajet</h5>
            <ul class="list-group">
              <?php if (!is_null($fumeur)): ?>
                <li class="list-group-item">Fumeur : <?= htmlspecialchars($fumeur) ?></li>
              <?php endif; ?>
              <?php if (!is_null($animaux)): ?>
                <li class="list-group-item">Animaux : <?= htmlspecialchars($animaux) ?></li>
              <?php endif; ?>
              <?php if (trim($autres) !== ''): ?>
                <li class="list-group-item">Autres : <?= htmlspecialchars($autres) ?></li>
              <?php endif; ?>
            </ul>
          <?php else: ?>
            <h5 class="mt-4">Préférences du trajet</h5>
            <p>Aucune préférence n’a été renseignée pour ce trajet.</p>
          <?php endif; ?>      
        </div>
        <div class="mt-4">
          <h4>Participer à ce covoiturage</h4>
          <?php if (!$isConnected): ?>
            <p class="text-warning">🔒 Vous devez être connecté pour participer.</p>
            <a href="login.php" class="btn btn-primary">Se connecter / S’inscrire</a>
          <?php elseif ($alreadyJoined): ?>
            <p class="text-info">✅ Vous avez déjà participé à ce covoiturage.</p>
          <?php else: ?>
            <form action="../../backend/participer.php" method="post">
              <input type="hidden" name="covoiturage_id" value="<?= $id ?>">
              <button type="submit" name="participer" class="btn btn-success">Participer</button>
            </form>
          <?php endif; ?>
        </div>
        <div class="mt-4">
          <h4>Votre avis nous intéresse</h4>
          <button class="btn btn-success" onclick="toggleAvisForm()">Laisser un avis</button>
        </div>
        <div id="avis-form" style="display: none;" class="mt-3">
          <?php if (isset($_SESSION['user_id'])): ?>
            <form action="../../backend/avis.php" method="post">
              <input type="hidden" name="covoiturage_id" value="<?= htmlspecialchars($id) ?>">

              <div class="mb-3">
                <label for="note" class="form-label">Note (1 à 5)</label>
                <select name="note" id="note" class="form-select" required>
                  <option value="">Choisir une note</option>
                  <option value="1">⭐ 1</option>
                  <option value="2">⭐⭐ 2</option>
                  <option value="3">⭐⭐⭐ 3</option>
                  <option value="4">⭐⭐⭐⭐ 4</option>
                  <option value="5">⭐⭐⭐⭐⭐ 5</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="commentaire" class="form-label">Commentaire</label>
                <textarea name="commentaire" id="commentaire" class="form-control" rows="4" required></textarea>
              </div>
              <button type="submit" class="btn btn-primary">Envoyer l’avis</button>
            </form>
          <?php else: ?>
            <p class="text-warning">🔒 Connectez-vous pour laisser un avis.</p>
            <a href="login.php" class="btn btn-outline-primary">Se connecter</a>
          <?php endif; ?>
        </div>

        <div class="container my-5">
          <div class="profile-box">
            <div class="col-md-6">
              <h5 class="mt-4">Avis sur le conducteur</h5>  
              <h5>Note moyenne : ⭐ <?= round($noteMoyenne, 1) ?> / 5</h5>
              <h6>Avis des passagers :</h6>

                <?php if (!empty($avisList)): ?>
                  <ul class="list-group">
                    <?php foreach ($avisList as $avis): ?>
                      <li class="list-group-item">
                        <strong>⭐ <?= htmlspecialchars($avis['note']) ?></strong> — <?= htmlspecialchars($avis['commentaire']) ?><br>
                        <small>Posté le <?= htmlspecialchars($avis['cree_le']) ?> — par utilisateur_id <?= htmlspecialchars($avis['passager_id']) ?> — statut : <?= htmlspecialchars($avis['statut']) ?></small>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                <?php else: ?>
                  <p>Aucun avis n’a encore été laissé pour ce covoiturage.</p>
                <?php endif; ?>
            </div>    
          </div>
        </div>
      </div>
    </div>
  </div>
<div>



  <!-- 🗺️ Leaflet JS -->
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    const depart = [<?= $coordDepart[0] ?>, <?= $coordDepart[1] ?>];
    const arrivee = [<?= $coordArrivee[0] ?>, <?= $coordArrivee[1] ?>];

    const map = L.map('map').setView(depart, 6);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    L.marker(depart).addTo(map).bindPopup('Départ : <?= htmlspecialchars($trajet['lieu_depart']) ?>').openPopup();
    L.marker(arrivee).addTo(map).bindPopup('Arrivée : <?= htmlspecialchars($trajet['lieu_arrivee']) ?>');

    L.polyline([depart, arrivee], { color: 'green' }).addTo(map);

    // Centrage automatique entre les deux points
    map.fitBounds([depart, arrivee]);
  </script>

<script>
function toggleAvisForm() {
  const form = document.getElementById('avis-form');
  form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
}
</script>

  <!-- ✅ Pied de page -->
  <?php include '../includes/footer.php'; ?>
  <?php include '../includes/mentions_legales.php'; ?>
 
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>