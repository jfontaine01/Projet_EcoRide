<?php 
require_once(__DIR__ . '/../../backend/controllers/authentification.php');
require_once(__DIR__ . '/../../backend/config/database.php');
$pdo = getPDO();

define('ASSET_PATH', '/Projet_EcoRide/frontend/assets/');

// Covoiturages aujourd’hui
$today = date('Y-m-d');
$stmtToday = $pdo->prepare("SELECT COUNT(*) FROM covoiturage WHERE date_depart = ?");
$stmtToday->execute([$today]);
$covoituragesToday = $stmtToday->fetchColumn();

// Crédits gagnés aujourd’hui (exemple : 2 crédits par trajet)
$creditsToday = $covoituragesToday * 2;

// Total des crédits
$stmtTotal = $pdo->query("SELECT COUNT(*) FROM covoiturage WHERE date_depart >= '1900-01-01'");
$totalCovoiturages = $stmtTotal->fetchColumn();
$totalCredits = $totalCovoiturages * 2;

// Covoiturages par jour (30 derniers jours)
$stmtGraph = $pdo->query("
  SELECT DATE(date_depart) AS jour, COUNT(*) AS total
  FROM covoiturage
  WHERE date_depart >= '1900-01-01'
  GROUP BY jour
  ORDER BY jour DESC
  LIMIT 30
");
$labels = [];
$rides = [];
while ($row = $stmtGraph->fetch()) {
  $labels[] = $row['jour'];
  $rides[] = $row['total'];
}

// Récupération des informmations utilisateurs
$stmtUsers = $pdo->query("
  SELECT u.nom, u.email, r.libelle AS role, s.libelle AS statut, u.status_id, u.role_id, u.cree_le, u.maj_le
  FROM utilisateur u
  LEFT JOIN role r ON u.role_id = r.role_id
  LEFT JOIN status s ON u.status_id = s.status_id
  order by cree_le desc
");
$users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

// Récupération du statut
$stmtStatus = $pdo->query("SELECT status_id, libelle FROM status");
$allStatus = $stmtStatus->fetchAll(PDO::FETCH_ASSOC);

// Récupération du role
$stmtRole = $pdo->query("SELECT role_id, libelle FROM role");
$allRole = $stmtRole->fetchAll(PDO::FETCH_ASSOC);

// MAJ du statut utilisateurs 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
  $email = $_POST['email'];
  $newStatus = $_POST['new_status'];
  $newRole = $_POST['new_role'];
  $stmtUpdate = $pdo->prepare("UPDATE utilisateur SET status_id = ?, role_id = ? WHERE email = ?");
  $stmtUpdate->execute([$newStatus, $newRole, $email]);
  header("Location: admin.php?section=comptes");
  exit;
}

// Récupérer les signalementd
$stmtSignalements = $pdo->query("
SELECT 
  s.signalement_id,
  s.covoiturage_id,
  u1.pseudo AS conducteur,
  u2.pseudo AS passager,
  s.motif,
  s.cree_le,
  s.statut,
  c.date_depart,
  c.date_arrivee,
  c.lieu_depart,
  c.lieu_arrivee
FROM signalements s
LEFT JOIN covoiturage c ON c.covoiturage_id = s.covoiturage_id
LEFT JOIN covoiturage_participants cp ON c.covoiturage_id = cp.covoiturage_id
LEFT JOIN utilisateur u1 ON c.conducteur_id = u1.utilisateur_id
LEFT JOIN utilisateur u2 ON cp.covoiturage_participants_id = u2.utilisateur_id
ORDER BY s.cree_le DESC;
");
$signalements = $stmtSignalements->fetchAll(PDO::FETCH_ASSOC);

// Déclaration des statuts signalements
$statutsSignalement = ['Ouvert', 'En cours', 'Terminé'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_signalement_statut'])) {
  $id = $_POST['signalement_id'];
  $newStatut = $_POST['new_statut'];
  $stmtUpdateSignalement = $pdo->prepare("UPDATE signalements SET statut = ? WHERE signalement_id = ?");
  $stmtUpdateSignalement->execute([$newStatut, $id]);
  header("Location: admin.php");
  exit;
}

// Récupération des avis
$stmtAvis = $pdo->query("
SELECT *, u1.pseudo, u1.nom as nom_passager, u2.nom as nom_valideur, valide_le FROM avis
LEFT JOIN utilisateur u1 ON passager_id = u1.utilisateur_id
LEFT JOIN utilisateur u2 ON valide_id = u2.utilisateur_id
");
$avis = $stmtAvis->fetchAll(PDO::FETCH_ASSOC);

// MAJ Statut des avis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['avis_id'], $_POST['action'])) {
  $avisId = (int) $_POST['avis_id'];
  $action = $_POST['action'];
  $nouveauStatut = ($action === 'valider') ? 'validé' : 'refusé';
  $valideurId = $_SESSION['user_id']; // ou l'ID du modérateur connecté

  $stmt = $pdo->prepare("UPDATE avis SET statut = ?, valide_id = ?, valide_le = NOW() WHERE avis_id = ?");
  $stmt->execute([$nouveauStatut, $valideurId, $avisId]);

  header("Location: admin.php");
  exit;
} 
?>

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

<section>
  <!-- 📋 Contenu principal -->
  <div class="container my-5">

<nav class="navbar navbar-expand-lg navbar-dark bg-success px-4">
  <a class="navbar-brand fw-bold" href="#">Dashboard</a>
  
  <!-- Bouton hamburger pour les petits écrans -->
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive"
    aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <!-- Contenu du menu -->
  <div class="collapse navbar-collapse" id="navbarResponsive">
    <ul class="navbar-nav ms-auto">
      <?php if (isset($_SESSION['role']) && $_SESSION['role'] == '1'): ?>  
        <li class="nav-item"><a class="nav-link fw-bold" href="admin.php?section=stats">Statistiques</a></li>
        <li class="nav-item"><a class="nav-link fw-bold" href="admin.php?section=comptes">Gestions utilisateurs</a></li>
      <?php endif; ?>
      <?php if ((isset($_SESSION['role']) && ($_SESSION['role'] == '1' || $_SESSION['role'] == '2'))): ?>      
        <li class="nav-item"><a class="nav-link fw-bold" href="admin.php?section=signalements">Signalements</a></li>
        <li class="nav-item"><a class="nav-link fw-bold" href="admin.php?section=avis">Avis</a></li>
      <?php endif; ?>
      <?php if (isset($_SESSION['role']) && $_SESSION['role'] == '1'): ?>
        <li class="nav-item"><a class="nav-link fw-bold" href="admin.php?section=logs">Logs activités</a></li>
      <?php endif; ?>
      <li class="nav-item"><a class="nav-link fw-bold" href="espace_utilisateur.php">Mon Espace</a></li>
    </ul>
  </div>
</nav>

    <div class="admin-box">
    <?php if ($_SESSION['role'] == '1' && isset($_GET['section']) && $_GET['section'] == 'stats'): ?>
    <!-- Section visible uniquement par les administrateurs -->       
      <!-- 📊 Statistiques -->
      <h4 class="section-title">Statistiques</h4>
      <div class="row text-center mb-4">
        <div class="col-md-4">
          <div class="card border-success">
            <div class="card-body">
              <h5>Covoiturages aujourd’hui</h5>
              <p class="display-6"><?= $covoituragesToday ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-success">
            <div class="card-body">
              <h5>Crédits gagnés aujourd’hui</h5>
              <p class="display-6"><?= $creditsToday ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-success">
            <div class="card-body">
              <h5>Total des crédits gagnés</h5>
              <p class="display-6"><?= $totalCredits ?></p>
            </div>
          </div>
        </div>
      </div>

      <!-- 📦 Chart.js CDN -->
      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

      <script>
        const labels = <?= json_encode(array_reverse($labels)) ?>;
        const ridesData = <?= json_encode(array_reverse($rides)) ?>;
        const creditsData = ridesData.map(r => r * 2);
      </script>

      <!-- 📊 Graphiques -->
      <div class="row my-5">
        <div class="col-md-6">
          <h5>Covoiturages par jour</h5>
          <canvas id="ridesChart"></canvas>
        </div>
        <div class="col-md-6">
          <h5>Revenus par jour (crédits)</h5>
          <canvas id="creditsChart"></canvas>
        </div>
      </div>

      <script>
        // 📈 Graphique des covoiturages
        new Chart(document.getElementById('ridesChart'), {
          type: 'line',
          data: {
            labels: labels,
            datasets: [{
              label: 'Covoiturages',
              data: ridesData,
              borderColor: '#198754',
              backgroundColor: 'rgba(25,135,84,0.2)',
              fill: true,
              tension: 0.3
            }]
          },
          options: {
            responsive: true,
            scales: {
              y: { beginAtZero: true }
            }
          }
        });

        // 💰 Graphique des crédits
        new Chart(document.getElementById('creditsChart'), {
          type: 'line',
          data: {
            labels: labels,
            datasets: [{
              label: 'Crédits gagnés',
              data: creditsData,
              borderColor: '#ffc107',
              backgroundColor: 'rgba(255,193,7,0.2)',
              fill: true,
              tension: 0.3
            }]
          },
          options: {
            responsive: true,
            scales: {
              y: { beginAtZero: true }
            }
          }
        });
      </script>
    <?php endif; ?>

    <?php if ($_SESSION['role'] == '1' && isset($_GET['section']) && $_GET['section'] == 'comptes'): ?>
      <!-- 👥 Gestion des comptes -->
      <h4 class="section-title">➕ Ajouter un employé</h4>
      <form id="signupForm" action="../../backend/controllers/ajouter_utilisateur.php" method="POST">
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
        <button type="submit" class="btn btn-success">Ajouter</button>
      </form>
      <div class="row text-center mb-4"></div>

      <!-- 👥 Gestion des comptes -->
      <h4 class="section-title">Comptes utilisateurs & employés</h4>
      <div class="row">
        <?php foreach ($users as $user): ?>
          <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
              <div class="card-body">
                <h5 class="card-title d-flex justify-content-between align-items-center">
                  <div>
                    <?= htmlspecialchars($user['nom']) ?>
                    <span class="badge bg-success"><?= htmlspecialchars($user['role']) ?></span>
                  </div>
                  <div class="text-end">
                    <small><small><small class="text-muted d-block">
                      créé le : <?= htmlspecialchars($user['cree_le']) ?>
                    </small></small></small>
                    <small><small><small class="text-muted d-block">
                      maj le : <?= htmlspecialchars($user['maj_le']) ?>
                    </small></small></small>
                  </div>
                </h5>
                <p class="card-text">
                  <strong>Email :</strong> <?= htmlspecialchars($user['email']) ?><br>
                  <strong>Statut :</strong> <?= htmlspecialchars($user['statut']) ?><br>
                  <strong>Rôle :</strong> <?= htmlspecialchars($user['role']) ?>
                </p>
                <form method="POST" class="d-flex flex-wrap gap-2">
                  <input type="hidden" name="email" value="<?= $user['email'] ?>">

                  <!-- Sélection du statut -->
                  <select name="new_status" class="form-select form-select-sm w-auto">
                    <?php foreach ($allStatus as $status): ?>
                      <option value="<?= $status['status_id'] ?>" <?= $status['status_id'] == $user['status_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($status['libelle']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>

                  <!-- Sélection du rôle -->
                  <select name="new_role" class="form-select form-select-sm w-auto">
                    <?php foreach ($allRole as $role): ?>
                      <option value="<?= $role['role_id'] ?>" <?= $role['role_id'] == $user['role_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($role['libelle']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>

                  <button type="submit" name="update_user" class="btn btn-success btn-sm">✅</button>
                </form>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if ((isset($_SESSION['role']) && ($_SESSION['role'] == '1' || $_SESSION['role'] == '2')) && isset($_GET['section']) && $_GET['section'] == 'signalements'): ?>
      <!-- 🚨 Signalements -->
      <h4 class="section-title mt-4">Signalements à traiter</h4>

      <?php foreach ($signalements as $s): ?>
        <div class="card mb-4 border-warning shadow-sm">
          <div class="card-body">
            <h5 class="card-title">
              Signalement #<?= $s['signalement_id'] ?>

              <form method="POST" class="d-flex flex-wrap gap-2 align-items-center">
                <input type="hidden" name="signalement_id" value="<?= $s['signalement_id'] ?>">
                <select name="new_statut" class="form-select form-select-sm w-auto">
                  <?php foreach ($statutsSignalement as $statut): ?>
                    <option value="<?= $statut ?>" <?= $statut === $s['statut'] ? 'selected' : '' ?>>
                      <?= $statut ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <button type="submit" name="update_signalement_statut" class="btn btn-success btn-sm">✅</button>
              </form>

            </h5>
            <p class="card-text">
              <strong>ID trajet :</strong> #<?= $s['covoiturage_id'] ?><br>
              <strong>Conducteur :</strong> <?= htmlspecialchars($s['conducteur']) ?><br>
              <strong>Passager :</strong> <?= htmlspecialchars($s['passager']) ?><br>
              <strong>Motif :</strong> <?= htmlspecialchars($s['motif']) ?><br>
              <strong>Date du trajet :</strong> <?= date('d/m/Y', strtotime($s['date_depart'])) ?> → <?= date('d/m/Y', strtotime($s['date_arrivee'])) ?><br>
              <strong>Lieu :</strong> <?= htmlspecialchars($s['lieu_depart']) ?> → <?= htmlspecialchars($s['lieu_arrivee']) ?><br>
              <strong>Créé le :</strong> <?= date('d/m/Y', strtotime($s['cree_le'])) ?>
            </p>
          </div>
        </div>
      <?php endforeach; ?>

    <?php endif; ?>

    <?php if ((isset($_SESSION['role']) && ($_SESSION['role'] == '1' || $_SESSION['role'] == '2')) && isset($_GET['section']) && $_GET['section'] == 'avis'): ?>
      <!-- 📝 Avis problématiques -->

      <h2 class="mt-5 mb-4">Avis à modérer</h2>
      <div class="row g-3">
        <?php foreach ($avis as $a): ?>
          <?php if ($a['statut'] === 'à modérer'): ?>
            <div class="col-md-6">
              <div class="avis-card bg-warning-subtle p-3 rounded shadow-sm h-100">
                <p class="mb-1"><strong>👤 Utilisateur :</strong> <?= htmlspecialchars($a['nom_passager']) ?></p>
                <p class="mb-1">
                  <strong>⭐ Note :</strong>
                  <?php for ($i = 0; $i < $a['note']; $i++): ?>
                    <span style="color:gold;">★</span>
                  <?php endfor; ?>
                  <?php for ($i = $a['note']; $i < 5; $i++): ?>
                    <span style="color:#ccc;">★</span>
                  <?php endfor; ?>
                  (<?= $a['note'] ?>/5)
                </p>
                <p class="mb-1"><strong>💬 Avis :</strong> <?= htmlspecialchars($a['commentaire']) ?></p>
                <p class="text-muted small"><strong>🕒 A modérer </strong></p>
                  <!-- Boutons d'action -->
                  <form method="post" class="d-flex gap-2 mt-3">
                    <input type="hidden" name="avis_id" value="<?= $a['avis_id'] ?>">
                    <button type="submit" name="action" value="valider" class="btn btn-success btn-sm">✅ Valider</button>
                    <button type="submit" name="action" value="refuser" class="btn btn-danger btn-sm">❌ Refuser</button>
                  </form>
              </div>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
      <div class="mt-5"></div>
      <h2 class="mb-4">Avis validés</h2>
      <div class="row g-3">
        <?php foreach ($avis as $a): ?>
          <?php if ($a['statut'] === 'validé'): ?>
            <div class="col-md-6">
              <div class="avis-card bg-success-subtle p-3 rounded shadow-sm h-100">
                <p class="mb-1"><strong>👤 Utilisateur :</strong> <?= htmlspecialchars($a['nom_passager']) ?></p>
                <p class="mb-1">
                  <strong>⭐ Note :</strong>
                  <?php for ($i = 0; $i < $a['note']; $i++): ?>
                    <span style="color:gold;">★</span>
                  <?php endfor; ?>
                  <?php for ($i = $a['note']; $i < 5; $i++): ?>
                    <span style="color:#ccc;">★</span>
                  <?php endfor; ?>
                  (<?= $a['note'] ?>/5)
                </p>
                <p class="mb-1"><strong>💬 Avis :</strong> <?= htmlspecialchars($a['commentaire']) ?></p>
                <p class="text-muted small"><strong>✅ Validé par :</strong> <?= htmlspecialchars($a['nom_valideur']) ?> le <?= date('d/m/Y', strtotime($a['valide_le'])) ?></p>
              </div>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
      <div class="mt-5"></div>
      <h2 class="mb-4">Avis refusés</h2>
      <div class="row g-3">
        <?php foreach ($avis as $a): ?>
          <?php if ($a['statut'] === 'refusé'): ?>
            <div class="col-md-6">
              <div class="avis-card bg-danger-subtle p-3 rounded shadow-sm h-100">
                <p class="mb-1"><strong>👤 Utilisateur :</strong> <?= htmlspecialchars($a['nom_passager']) ?></p>
                <p class="mb-1">
                  <strong>⭐ Note :</strong>
                  <?php for ($i = 0; $i < $a['note']; $i++): ?>
                    <span style="color:gold;">★</span>
                  <?php endfor; ?>
                  <?php for ($i = $a['note']; $i < 5; $i++): ?>
                    <span style="color:#ccc;">★</span>
                  <?php endfor; ?>
                  (<?= $a['note'] ?>/5)
                </p>
                <p class="mb-1"><strong>💬 Avis :</strong> <?= htmlspecialchars($a['commentaire']) ?></p>
                <p class="text-muted small"><strong>❌ Refusé par :</strong> <?= htmlspecialchars($a['nom_valideur']) ?> le <?= date('d/m/Y', strtotime($a['valide_le'])) ?></p>
              </div>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    
    <?php endif; ?>

    <?php if ($_SESSION['role'] == '1' && isset($_GET['section']) && $_GET['section'] == 'logs'): ?> 
      
      <div class="mt-5"></div>
      <h2 class="mb-4">Logs activités</h2>

        <form method="GET" class="row g-3 mb-4">
          <div class="col-md-4">
            <label for="date_debut" class="form-label">Date début</label>
            <input type="date" name="date_debut" id="date_debut" class="form-control" value="<?= htmlspecialchars($_GET['date_debut'] ?? '') ?>">
          </div>
          <div class="col-md-4">
            <label for="date_fin" class="form-label">Date fin</label>
            <input type="date" name="date_fin" id="date_fin" class="form-control" value="<?= htmlspecialchars($_GET['date_fin'] ?? '') ?>">
          </div>
          <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Filtrer</button>
          </div>
        </form>

      <div class="row g-3">
          <?php include '../../backend/controllers/logs_activite.php';?>
      </div>
    
    <?php endif; ?>  

    </div>
  </div>
</section>  
 
  <!-- ✅ Pied de page -->
  <?php include '../includes/footer.php'; ?>
  <?php include '../includes/mentions_legales.php'; ?>
   
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<span id="user-info" 
      data-pseudo="<?= htmlspecialchars($_SESSION['pseudo'] ?? '') ?>" 
      data-role="<?= htmlspecialchars($_SESSION['role'] ?? '') ?>" 
      style="display:none;"></span>

</body>
</html>