<?php 
require_once(__DIR__ . '/../../backend/controllers/authentification.php');
require_once(__DIR__ . '/../../backend/config/database.php');
$pdo = getPDO();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

define('ASSET_PATH', '/Projet_EcoRide/frontend/assets/');

$stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE utilisateur_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$roles = $pdo->query("SELECT * FROM role")->fetchAll(PDO::FETCH_ASSOC);
$statut = $pdo->query("SELECT * FROM status")->fetchAll(PDO::FETCH_ASSOC);

$utilisateurId = $_GET['id'] ?? null;

$marque = $pdo->query("SELECT marque_id, libelle FROM marque ORDER BY libelle ASC");
$marques = $marque->fetchAll();

$trajetsEncours = $pdo->prepare("
  SELECT c.*,c.conducteur_id, v.modele, v.energie, u.nom AS conducteur_nom, u.prenom AS conducteur_prenom,
         t.fumeur, t.animaux, t.autres
  FROM covoiturage c
  JOIN voiture v ON c.voiture_id = v.voiture_id
  JOIN utilisateur u ON c.conducteur_id = u.utilisateur_id
  LEFT JOIN preferences_trajet t ON c.covoiturage_id = t.covoiturage_id
  WHERE c.conducteur_id = ? AND c.statut IN ('ouvert', 'en cours', 'planifié')
  ORDER BY c.date_depart ASC
");
$trajetsEncours->execute([$_SESSION['user_id']]);

$trajetsTermine = $pdo->prepare("
  SELECT c.*,c.conducteur_id, v.modele, v.energie, u.nom AS conducteur_nom, u.prenom AS conducteur_prenom,
         t.fumeur, t.animaux, t.autres
  FROM covoiturage c
  JOIN voiture v ON c.voiture_id = v.voiture_id
  JOIN utilisateur u ON c.conducteur_id = u.utilisateur_id
  LEFT JOIN preferences_trajet t ON c.covoiturage_id = t.covoiturage_id
  WHERE c.conducteur_id = ? AND c.statut = 'terminé'
");
$trajetsTermine->execute([$_SESSION['user_id']]);
$trajetsTermines = $trajetsTermine->fetchAll();

$voiture = $pdo->prepare("SELECT v.*, m.* FROM voiture v LEFT JOIN marque m ON v.marque_id = m.marque_id WHERE utilisateur_id = ?");
$voiture->execute([$_SESSION['user_id']]);
$voitures = $voiture->fetchAll();

$preference_conducteur = $pdo->prepare("SELECT pref.*, u.* FROM preferences_conducteur pref LEFT JOIN utilisateur u ON pref.utilisateur_id = u.utilisateur_id WHERE pref.utilisateur_id = ?");
$preference_conducteur->execute([$_SESSION['user_id']]);
$preferences_conducteur = $preference_conducteur->fetch();

$fumeur_pref = '';
$animaux_pref = '';
$autres_pref = '';

if ($preferences_conducteur && is_array($preferences_conducteur)) {
    $fumeur_pref = $preferences_conducteur['fumeur'] ?? '';
    $animaux_pref = $preferences_conducteur['animaux'] ?? '';
    $autres_pref = $preferences_conducteur['autres'] ?? '';    
}
//echo '<pre>';
//print_r($_SESSION);
//print_r($user);
//echo '</pre>';

if (!file_exists(__DIR__ . '/../../backend/controllers/authentification.php')) {
    die("Fichier d'authentification introuvable ❌");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>EcoRide - Espace utilisateur</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= ASSET_PATH ?>css/style.css" rel="stylesheet">

  <span id="user-info" 
      data-pseudo="<?= htmlspecialchars($_SESSION['pseudo'] ?? '') ?>" 
      data-email="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" 
      style="display:none;">
  </span>
</head>
<body>

<!-- 🧭 Menu de navigation -->
<?php include '../includes/navbar.php';?>

<script>
  const userPseudo = <?= isset($_SESSION['pseudo']) ? json_encode($_SESSION['pseudo']) : 'null' ?>;
  const userEmail = <?= isset($_SESSION['email']) ? json_encode($_SESSION['email']) : 'null' ?>;
</script>

<!-- Bienvenue -->  
<div class="container my-5">
  <div class="profile-box">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="mb-0">
        Bienvenue, <?= htmlspecialchars($user['pseudo']) ?> !
        <span class="badge bg-success ms-2" style="font-size: 1rem;">
          💰 Crédit : <?= $user['credit'] ?> €
        </span>
      </h2>
    </div>
    <div class="mb-3">
      <?php if (isset($_SESSION['role']) && $_SESSION['role'] == '1'): ?>  
        <a class="btn btn-primary w-100" href="admin.php?section=stats">📊 Dashborad</a>
      <?php endif; ?>
      <?php if (isset($_SESSION['role']) && $_SESSION['role'] == '2'): ?>  
        <a class="btn btn-primary w-100" href="admin.php?section=signalements">📊 Dashborad</a>
      <?php endif; ?>

    </div>

   

    <div class="container my-4">
      <div class="row g-3 justify-content-center">
        <!-- Boutons -->
        <div class="row g-3 justify-content-center">
          <div class="col-6 col-md-auto">
            <button class="btn btn-outline-success w-100" onclick="showSection('profil')">👤 Profil</button>
          </div>
          <?php if (isset($user['conducteur']) && $user['conducteur'] === 'O'): ?>
          <div class="col-6 col-md-auto">
            <button class="btn btn-outline-success w-100" onclick="showSection('listevehicules')">🚗 Liste véhicules</button>
          </div>
          <?php endif; ?>
          <div class="col-6 col-md-auto">
            <button class="btn btn-outline-success w-100" onclick="showSection('encours')">Trajets en cours</button>
          </div>
          <div class="col-6 col-md-auto">
            <button class="btn btn-outline-success w-100" onclick="showSection('termines')">Trajets terminés</button>
          </div>
          <?php if (isset($user['conducteur']) && $user['conducteur'] === 'O'): ?>
          <div class="col-12 col-md-auto">
            <button class="btn btn-success w-100" onclick="showSection('proposer')">➕ Proposer un trajet</button>
          </div>
          <?php endif; ?>          
        </div>
      </div>
    </div>  

    <?php if (isset($user)): ?>
    <!-- Sections de contenu -->
    <div id="profil" class="content-section mt-4" style="display:block;">
      <h4>Profil</h4>
        <!-- ✅ Message de confirmation -->
        <?php if (isset($_GET['update'])): ?>
          <?php if ($_GET['update'] === 'success'): ?>
            <div class="alert alert-success">✅ Profil modifié avec succès.</div>
          <?php elseif ($_GET['update'] === 'none'): ?>
            <div class="alert alert-warning">⚠️ Aucune modification détectée.</div>
          <?php elseif ($_GET['update'] === 'error'): ?>
            <div class="alert alert-danger">❌ Erreur dans le formulaire de mise à jour.</div>
          <?php endif; ?>
        <?php endif; ?>      
        <!-- 👤 Profil -->
          <form action="../../backend/controllers/update_utilisateur.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="user_id" value="<?= $user['utilisateur_id'] ?>">
            <div class="mb-3"><label for="pseudo" class="form-label">Pseudo :</label>
              <input type="text" class="form-control" id="pseudo" name="pseudo" value="<?= htmlspecialchars($user['pseudo']) ?>" required>
            </div>
            <div class="mb-3"><label for="email" class="form-label">Email :</label>
              <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="mb-3"><label for="nom" class="form-label">Nom :</label>
              <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($user['nom']) ?>">
            </div>
            <div class="mb-3"><label for="prenom" class="form-label">Prénom :</label>
              <input type="text" class="form-control" id="prenom" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>">
            </div>
            <div class="mb-3"><label for="telephone" class="form-label">Téléphone :</label>
              <input type="text" class="form-control" id="telephone" name="telephone" value="<?= htmlspecialchars($user['telephone']) ?>">
            </div>
            <div class="mb-3"><label for="adresse" class="form-label">Adresse :</label>
              <input type="text" class="form-control" id="adresse" name="adresse" value="<?= htmlspecialchars($user['adresse']) ?>">
            </div>
            <div class="mb-3"><label for="date_naissance" class="form-label">Date de naissance :</label>
              <input type="date" class="form-control" id="date_naissance" name="date_naissance" value="<?= htmlspecialchars($user['date_naissance']) ?>">
            </div>
            <div class="mb-3">
              <label for="role_id" class="form-label">Role :</label>
              <input type="hidden" id="status_id" name="status_id" value="3">
              <input type="text" class="form-control" value="Utilisateur" disabled>
            </div>
            <div class="mb-3">
              <label for="status_id" class="form-label">Statut :</label>
              <input type="hidden" id="status_id" name="status_id" value="1">
              <input type="text" class="form-control" value="compte actif" disabled>
            </div>
            <div class="mb-3"><label for="photo" class="form-label">Photo de profil</label>
              <input type="file" class="form-control" id="photo" name="photo">
            </div>
            <div class="mb-3">
              <label class="form-label">Passager :</label>
              <div class="form-check form-check-inline">
                <input type="radio" class="form-check-input" name="passager" value="O" <?= $user['passager'] === 'O' ? 'checked' : '' ?>> Oui
              </div>
              <div class="form-check form-check-inline">
                <input type="radio" class="form-check-input" name="passager" value="N" <?= $user['passager'] === 'N' ? 'checked' : '' ?>> Non
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Conducteur :</label>
              <div class="form-check form-check-inline">
                <input type="radio" class="form-check-input" name="conducteur" id="conducteur_O" value="O" <?= $user['conducteur'] === 'O' ? 'checked' : '' ?>> Oui
              </div>
              <div class="form-check form-check-inline">
                <input type="radio" class="form-check-input" name="conducteur" id="conducteur_N" value="N" <?= $user['conducteur'] === 'N' ? 'checked' : '' ?>> Non
              </div>
            </div>

            <h4>Vos préférences Conducteurs</h4>
            <!-- 👤 Profil préférences conducteurs -->
            <div class="mb-3"> 
              <label class="form-label">Accepptez-vous les Fumeurs ? :</label>
              <div class="form-check form-check-inline">
                <input type="radio" class="form-check-input" name="fumeur" value="O" <?= $fumeur_pref === 'O' ? 'checked' : '' ?>> Oui
              </div>
              <div class="form-check form-check-inline">
                <input type="radio" class="form-check-input" name="fumeur" value="N" <?= $fumeur_pref === 'N' ? 'checked' : '' ?>> Non
              </div>
              <div class="form-check form-check-inline">            
                <input type="radio" name="fumeur" value=""
                  <?= !isset($preferences_conducteur['fumeur']) || $preferences_conducteur['fumeur'] === '' ? 'checked' : '' ?>> Indiferrent<br>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Accepptez-vous les animaux ? :</label>
              <div class="form-check form-check-inline">
                <input type="radio" class="form-check-input" name="animaux" value="O" <?= $animaux_pref === 'O' ? 'checked' : '' ?>> Oui
              </div>
              <div class="form-check form-check-inline">
                <input type="radio" class="form-check-input" name="animaux" value="N" <?= $animaux_pref === 'N' ? 'checked' : '' ?>> Non
              </div>
              <div class="form-check form-check-inline">            
                <input type="radio" name="animaux" value=""
                  <?= !isset($preferences_conducteur['animaux']) || $preferences_conducteur['animaux'] === '' ? 'checked' : '' ?>> Indiferrent<br>
              </div>
            </div>
            <div class="mb-3"><label for="autres" class="form-label">Autres</label>
              <input type="text" class="form-control" id="autres" name="autres" value="<?= htmlspecialchars($autres_pref) ?>">
            </div>

            <!-- Bouton principal de mise à jour du profil -->
            <div class="mb-3 mt-4 text-end">
              <button type="submit" class="btn btn-success">💾 Enregistrer le profil</button>
            </div>
          </form>
    </div>
    <?php endif; ?>

    <?php if (isset($user['conducteur']) && $user['conducteur'] === 'O'): ?>
    <div id="listevehicules" class="content-section mt-4" style="display:block;">
      <h4>🚗 Vos véhicules</h4>

      <!-- ✅ Message de confirmation -->
      <?php if (isset($_GET['update'])): ?>
        <?php if ($_GET['update'] === 'success'): ?>
          <div class="alert alert-success">✅ Véhicule modifié avec succès.</div>
        <?php elseif ($_GET['update'] === 'none'): ?>
          <div class="alert alert-warning">⚠️ Aucune modification détectée.</div>
        <?php elseif ($_GET['update'] === 'error'): ?>
          <div class="alert alert-danger">❌ Erreur dans le formulaire de mise à jour.</div>
        <?php endif; ?>
      <?php endif; ?>

      <!-- ✅ Liste des véhicules -->
      <?php foreach ($voitures as $voiture): ?>
        <div class="card mb-3">
          <div class="card-body">
            <form method="post" action="../../backend/controllers/update_voiture.php">
              <input type="hidden" name="voiture_id" value="<?= $voiture['voiture_id'] ?>">

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Marque</label>
                  <select name="marque_id" class="form-control" required>
                    <?php foreach ($marques as $marque): ?>
                      <option value="<?= $marque['marque_id'] ?>" <?= $marque['marque_id'] == $voiture['marque_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($marque['libelle']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Modèle</label>
                  <input type="text" class="form-control" name="modele" value="<?= htmlspecialchars($voiture['modele']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Immatriculation</label>
                  <input type="text" class="form-control" name="immatriculation" value="<?= htmlspecialchars($voiture['immatriculation']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Date de première immatriculation</label>
                  <input type="date" class="form-control" name="date_premiere_immatriculation" value="<?= htmlspecialchars($voiture['date_premiere_immatriculation']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Nombre de places disponibles</label>
                  <input type="text" class="form-control" name="nb_places_dispo" value="<?= htmlspecialchars($voiture['nb_places_dispo']) ?>" required>
                </div>            
                <div class="col-md-6 mb-3">
                  <label class="form-label">Couleur</label>
                  <input type="text" class="form-control" name="couleur" value="<?= htmlspecialchars($voiture['couleur']) ?>">
                </div>
              </div>

              <div class="d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-primary">💾 Modifier</button>
              </div>
            </form>

            <form method="post" action="../../backend/controllers/supprimer_voiture.php" onsubmit="return confirm('Supprimer ce véhicule ?')" class="mt-2 text-end">
              <input type="hidden" name="voiture_id" value="<?= $voiture['voiture_id'] ?>">
              <button type="submit" class="btn btn-danger">🗑️ Supprimer</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>

      <!-- ✅ Formulaire d’ajout voiture -->
      <h4 class="mt-4">➕ Ajouter un véhicule</h4>
        <form method="post" action="../../backend/controllers/ajouter_voiture.php" class="card p-3">
          <input type="hidden" name="utilisateur_id" value="<?= $user['utilisateur_id'] ?>">

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Marque</label>
              <select name="marque_id" class="form-control" required>
                <option value="">-- Sélectionner une marque --</option>
                <?php foreach ($marques as $marque): ?>
                  <option value="<?= $marque['marque_id'] ?>"><?= htmlspecialchars($marque['libelle']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Modèle</label>
              <input type="text" name="modele" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Immatriculation</label>
              <input type="text" name="immatriculation" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Date de première immatriculation</label>
              <input type="date" class="form-control" name="date_premiere_immatriculation" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Nombre de places disponibles</label>
              <input type="text" class="form-control" name="nb_places_dispo" required>
            </div>       
            <div class="col-md-6 mb-3">
              <label class="form-label">Couleur</label>
              <input type="text" name="couleur" class="form-control">
            </div>
          </div>

          <div class="text-end">
            <button type="submit" class="btn btn-success">💾 Enregistrer</button>
          </div>
        </form>
    </div>
    <?php endif; ?>
  
    <div id="encours" class="content-section mt-4" style="display:none;">
      <h4>Trajets ouverts, en cours ou planifiés</h4>
        <!-- Ouverts -->
          <?php if (!empty($trajetsEncours)): ?>
            <?php foreach ($trajetsEncours as $trajet): ?>
              <div class="card mb-3 border-secondary">
                <div class="card-body">
                  <h5><?= htmlspecialchars($trajet['lieu_depart']) ?> → <?= htmlspecialchars($trajet['lieu_arrivee']) ?></h5>
                  <p>
                    Statut : <strong><?= htmlspecialchars($trajet['statut']) ?></strong><br>
                    Départ : <?= htmlspecialchars($trajet['date_depart']) ?><br>
                    Véhicule : <?= htmlspecialchars($trajet['modele']) ?> (<?= htmlspecialchars($trajet['energie']) ?>)
                  </p>
                  <a href="detail.php?id=<?= $trajet['covoiturage_id'] ?>" class="btn btn-success">Voir le trajet</a>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="alert alert-warning mt-3">
              🚫 Vous n’avez encore aucun trajet ouvert, planifié ou en cours.<br>
              Pourquoi ne pas proposer un trajet dans la section ci-dessus ?
            </div>
          <?php endif; ?>
    </div>

    <div id="termines" class="content-section mt-4" style="display:none;">
      <h4>Trajets terminés</h4>
      <!-- Terminés -->
          <?php if (!empty($trajetsTermines)): ?>
            <?php foreach ($trajetsTermines as $trajet): ?>
              <div class="card mb-3 border-secondary">
                <div class="card-body">
                  <h5><?= htmlspecialchars($trajet['lieu_depart']) ?> → <?= htmlspecialchars($trajet['lieu_arrivee']) ?></h5>
                  <p>
                    Statut : <strong><?= htmlspecialchars($trajet['statut']) ?></strong><br>                  
                    Départ : <?= htmlspecialchars($trajet['date_depart']) ?><br>
                    Véhicule : <?= htmlspecialchars($trajet['modele']) ?> (<?= htmlspecialchars($trajet['energie']) ?>)
                  </p>
                  <a href="detail.php?id=<?= $trajet['covoiturage_id'] ?>" class="btn btn-success">Revoir le trajet</a>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="alert alert-warning mt-3">
              🚫 Vous n’avez encore aucun trajet terminé.<br>
              Pourquoi ne pas proposer un trajet dans la section ci-dessus ?
            </div>
          <?php endif; ?>
    </div>

    <div id="proposer" class="content-section mt-4" style="display:none;">
      <h4>Proposer un trajet</h4>
        <!-- ➕ Proposer -->
          <form action="../../backend/controllers/ajouter_trajet.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="lieu_depart" class="form-label">Lieu de départ</label>
              <input type="text" class="form-control" id="lieu_depart" name="lieu_depart" required>
            </div>
            <div class="mb-3">
              <label for="lieu_arrivee" class="form-label">Lieu d’arrivée</label>
              <input type="text" class="form-control" id="lieu_arrivee" name="lieu_arrivee" required>
            </div>
            <div class="mb-3">
              <label for="date_depart" class="form-label">Date de départ</label>
              <input type="date" class="form-control" id="date_depart" name="date_depart" required>
            </div>
            <div class="mb-3">
              <label for="heure_depart" class="form-label">Heure de départ</label>
              <input type="time" class="form-control" id="heure_depart" name="heure_depart" required>
            </div>
            <div class="mb-3">
              <label for="nb_place" class="form-label">Places disponibles</label>
              <input type="number" class="form-control" id="nb_place" name="nb_place" min="1" required>
            </div>
            <div class="mb-3">
              <label for="voiture_id" class="form-label">Choisir une voiture</label>
              <select class="form-select" id="voiture_id" name="voiture_id" required>
                <option value="">-- Sélectionnez --</option>
                <?php foreach ($voitures as $voiture): ?>
                  <option value="<?= $voiture['voiture_id'] ?>">
                    <?= htmlspecialchars($voiture['libelle']) ?> <?= htmlspecialchars($voiture['modele']) ?> (<?= htmlspecialchars($voiture['immatriculation']) ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <button type="submit" class="btn btn-success">🚀 Publier le trajet</button>
          </form>
    </div>

    <div>

    </div>

  </div>
</div>


<script>
  window.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    if (params.get('profil') === '1') {
      showSection('profil-section');
    }
  });

  function showSection(id) {
    document.querySelectorAll('.content-section').forEach(section => {
      section.style.display = 'none';
    });

    const selected = document.getElementById(id);
    if (selected) {
      selected.style.display = 'block';
      window.scrollTo({
        top: selected.offsetTop - 80,
        behavior: 'smooth'
      });
    }
  }
</script>

<script>
  window.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const profilSection = document.getElementById('profil-section');

    // Affiche la section profil si l'utilisateur est connecté ou si ?profil=1 est présent
    if (profilSection && (params.get('profil') === '1' || <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>)) {
      profilSection.style.display = 'block';
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