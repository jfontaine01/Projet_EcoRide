<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>EcoRide - Covoiturage écologique</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f4fdf4;
      font-family: 'Segoe UI', sans-serif;
    }

    .login-box {
      max-width: 400px;
      margin: 80px auto;
      padding: 30px;
      background: white;
      border-radius: 8px;
      box-shadow: 0 0 10px #2e7d32;
    }
    .login-box h2 {
      margin-bottom: 25px;
      text-align: center;
    }    
  </style>
</head>
<body>

<!-- 🧭 Menu de navigation -->
<script src="script/navbar.js"></script>

  <!-- 📋 Contenu principal -->
  <div class="container my-5">
    <div class="box">

      <span class="text-white">Connecté en tant que : <strong>admin@ecoride.fr</strong></span>
      <!-- 📊 Statistiques -->
      <h3 class="section-title">EcoRide – Panel Administrateur</h3>
      <h4 class="section-title">Statistiques</h4>
      <div class="row text-center mb-4">
        <div class="col-md-4">
          <div class="card border-success">
            <div class="card-body">
              <h5>Covoiturages aujourd’hui</h5>
              <p class="display-6">12</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-success">
            <div class="card-body">
              <h5>Crédits gagnés aujourd’hui</h5>
              <p class="display-6">24</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-success">
            <div class="card-body">
              <h5>Total des crédits gagnés</h5>
              <p class="display-6">1 240</p>
            </div>
          </div>
        </div>
      </div>

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

      <!-- 📦 Chart.js CDN -->
      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

      <script>
        // 🗓 Générer les 30 derniers jours
        const labels = Array.from({length: 30}, (_, i) => {
          const d = new Date();
          d.setDate(d.getDate() - (29 - i));
          return d.toISOString().split('T')[0];
        });

        // 📊 Données simulées (à remplacer par PHP/MySQL)
        const ridesData = [2, 3, 1, 4, 5, 2, 3, 6, 4, 5, 3, 2, 1, 4, 6, 5, 3, 2, 4, 5, 6, 3, 2, 1, 4, 5, 6, 3, 2, 4];
        const creditsData = ridesData.map(r => r * 2); // Exemple : 2 crédits par covoiturage

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

      <!-- 👥 Gestion des comptes -->
      <h4 class="section-title">Comptes utilisateurs & employés</h4>
      <table class="table table-bordered">
        <thead class="table-success">
          <tr>
            <th>Nom</th>
            <th>Email</th>
            <th>Type</th>
            <th>Statut</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Jean Dupont</td>
            <td>jean@ecoride.fr</td>
            <td>Utilisateur</td>
            <td>Actif</td>
            <td><button class="btn btn-outline-danger btn-sm">Suspendre</button></td>
          </tr>
          <tr>
            <td>Claire Martin</td>
            <td>claire@ecoride.fr</td>
            <td>Employé</td>
            <td>Actif</td>
            <td><button class="btn btn-outline-danger btn-sm">Suspendre</button></td>
          </tr>
        </tbody>
      </table>

      <!-- 🚨 Signalements -->
      <h4 class="section-title mt-4">Signalements à traiter</h4>
      <table class="table table-bordered">
        <thead class="table-warning">
          <tr>
            <th>ID trajet</th>
            <th>Conducteur</th>
            <th>Passager</th>
            <th>Motif</th>
            <th>Date</th>
            <th>Statut</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>#102</td>
            <td>GreenDriver42</td>
            <td>EcoVoyageur</td>
            <td>Retard + conduite brusque</td>
            <td>15/08/2025</td>
            <td><span class="badge bg-danger">Ouvert</span></td>
          </tr>
        </tbody>
      </table>

      <!-- 📝 Avis problématiques -->
      <h4 class="section-title mt-4">Avis à modérer</h4>
      <ul class="list-group">
        <li class="list-group-item">
          <strong>Utilisateur :</strong> EcoVoyageur<br>
          <strong>Avis :</strong> Le chauffeur était agressif verbalement.<br>
          <strong>Soumis le :</strong> 15/08/2025<br>
          <button class="btn btn-outline-success btn-sm mt-2">Valider</button>
          <button class="btn btn-outline-danger btn-sm mt-2">Refuser</button>
        </li>
      </ul>

    </div>
  </div>

  <!-- ✅ Pied de page -->
  <script src="script/footer.js"></script>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>