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

  <!-- 🌱 Présentation de l’entreprise -->
  <section class="trouve text-center recherche-bg">
    <div class="container">
      <h1 class="display-4">Bienvenue sur EcoRide</h1>
      <p class="lead">La plateforme de covoiturage pensée pour l’environnement 🌍</p>
      <a href="#recherche" class="btn btn-light btn-lg mt-3">Trouver un itinéraire</a>
    </div>
  </section>

  <!-- 🔍 Barre de recherche -->
  <section id="recherche" class="container my-5">
    <h2 class="mb-4 text-success">Recherchez un itinéraire</h2>
    <form id="search-form" class="row g-3">
      <div class="col-md-4">
        <label for="departure" class="form-label">Ville de départ: </label>
        <input type="text" id="depart" class="form-control" placeholder="Ex : Paris" required>
      </div>
      <div class="col-md-4">
        <label for="arrival" class="form-label">Ville d’arrivée :</label>
        <input type="text" id="arrivee" class="form-control" placeholder="Ex : Lyon" required>
      </div>
      <div class="col-md-4">
      <label for="date" class="form-label">Date du trajet: </label>        
        <input type="date" id="date" class="form-control" placeholder="jj/mm/aaaa" required>
      </div>
      <div class="col-12 text-end">
        <button type="submit" class="btn btn-success">Rechercher</button>
      </div>
    </form>
  </section>

  <div id="resultats" class="mt-4"></div>

  <!-- ℹ️ Infos sur la société -->
  <section id="infos-societe" class="container my-5">
    <div class="row align-items-center bg-light p-4 shadow-sm">
      <div class="col-md-6 mb-4 mb-md-0">
        <img src="<?= ASSET_PATH ?>img/equipe.jpg" alt="Équipe EcoRide" class="img-fluid rounded">
      </div>
      <div class="col-md-6">
        <h3 class="text-success">🌿 À propos d’EcoRide</h3>
        <p>
          <strong>EcoRide</strong> est une startup française engagée pour l’environnement. Son objectif : réduire l’impact écologique des déplacements en favorisant le covoiturage. Portée par <strong>José</strong>, directeur technique, l’équipe développe une application web intuitive et responsable.
        </p>
        <p>
          L’ambition d’EcoRide est de devenir la référence du covoiturage éco-responsable, en ciblant uniquement les trajets en voiture. Elle s’adresse aux voyageurs soucieux de leur empreinte carbone et à ceux qui cherchent une solution économique et conviviale.
        </p>
        <ul class="list-unstyled">
          <li><strong>📍 Siège :</strong> Lyon, France</li>
        </ul>
      </div>
    </div>
  </section>

<section id="pourquoi-ecoride" class="container my-5">
  <div class="text-center mb-4">
    <h2 class="text-success">Pourquoi choisir EcoRide ?</h2>
  </div>
  <div class="row text-center">
    <div class="col-md-3 mb-4">
      <div class="p-3 bg-light rounded shadow-sm h-100">
        <i class="material-icons text-success" style="font-size: 48px;">
          <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-leaf-fill" viewBox="0 0 16 16">
            <path d="M1.4 1.7c.217.289.65.84 1.725 1.274 1.093.44 2.885.774 5.834.528 2.02-.168 3.431.51 4.326 1.556C14.161 6.082 14.5 7.41 14.5 8.5q0 .344-.027.734C13.387 8.252 11.877 7.76 10.39 7.5c-2.016-.288-4.188-.445-5.59-2.045-.142-.162-.402-.102-.379.112.108.985 1.104 1.82 1.844 2.308 2.37 1.566 5.772-.118 7.6 3.071.505.8 1.374 2.7 1.75 4.292.07.298-.066.611-.354.715a.7.7 0 0 1-.161.042 1 1 0 0 1-1.08-.794c-.13-.97-.396-1.913-.868-2.77C12.173 13.386 10.565 14 8 14c-1.854 0-3.32-.544-4.45-1.435-1.124-.887-1.889-2.095-2.39-3.383-1-2.562-1-5.536-.65-7.28L.73.806z"/>
          </svg>         
        </i>
        <h5 class="mt-3">Réduisez votre empreinte carbone</h5>
      </div>
    </div>
    <div class="col-md-3 mb-4">
      <div class="p-3 bg-light rounded shadow-sm h-100">
        <i class="material-icons text-success" style="font-size: 48px;">
          <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-piggy-bank-fill" viewBox="0 0 16 16">
          <path d="M7.964 1.527c-2.977 0-5.571 1.704-6.32 4.125h-.55A1 1 0 0 0 .11 6.824l.254 1.46a1.5 1.5 0 0 0 1.478 1.243h.263c.3.513.688.978 1.145 1.382l-.729 2.477a.5.5 0 0 0 .48.641h2a.5.5 0 0 0 .471-.332l.482-1.351c.635.173 1.31.267 2.011.267.707 0 1.388-.095 2.028-.272l.543 1.372a.5.5 0 0 0 .465.316h2a.5.5 0 0 0 .478-.645l-.761-2.506C13.81 9.895 14.5 8.559 14.5 7.069q0-.218-.02-.431c.261-.11.508-.266.705-.444.315.306.815.306.815-.417 0 .223-.5.223-.461-.026a1 1 0 0 0 .09-.255.7.7 0 0 0-.202-.645.58.58 0 0 0-.707-.098.74.74 0 0 0-.375.562c-.024.243.082.48.32.654a2 2 0 0 1-.259.153c-.534-2.664-3.284-4.595-6.442-4.595m7.173 3.876a.6.6 0 0 1-.098.21l-.044-.025c-.146-.09-.157-.175-.152-.223a.24.24 0 0 1 .117-.173c.049-.027.08-.021.113.012a.2.2 0 0 1 .064.199m-8.999-.65a.5.5 0 1 1-.276-.96A7.6 7.6 0 0 1 7.964 3.5c.763 0 1.497.11 2.18.315a.5.5 0 1 1-.287.958A6.6 6.6 0 0 0 7.964 4.5c-.64 0-1.255.09-1.826.254ZM5 6.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0"/>
          </svg>
        </i>
        <h5 class="mt-3">Faites des économies</h5>
      </div>
    </div>
    <div class="col-md-3 mb-4">
      <div class="p-3 bg-light rounded shadow-sm h-100">
        <i class="material-icons text-success" style="font-size: 48px;">
          <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
            <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
          </svg>
        </i>
        <h5 class="mt-3">Rencontrez des gens sympas</h5>
      </div>
    </div>
    <div class="col-md-3 mb-4">
      <div class="p-3 bg-light rounded shadow-sm h-100">
        <i class="material-icons text-success" style="font-size: 48px;">
          <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM6.97 11.03a.75.75 0 0 0 1.07 0l3.992-3.992a.75.75 0 0 0-1.06-1.06L7.5 9.439 6.1 8.03a.75.75 0 1 0-1.06 1.06l1.93 1.94z"/>
          </svg>
        </i>
        <h5 class="mt-3">Trajets fiables & sécurisés</h5>
      </div>
    </div>
  </div>
</section>

<section id="covoiturage-ecologique" class="container my-5">
  <div class="row align-items-center bg-light p-4 rounded shadow-sm">
    <div class="col-md-6 mb-4 mb-md-0">
      <img src="<?= ASSET_PATH ?>img/ecologie.jpg" alt="Ecologie" class="img-fluid rounded">
    </div>
    <div class="col-md-6">
      <h2 class="text-success">Covoiturage écologique</h2>
      <p class="lead">Voyagez autrement, voyagez EcoRide.</p>
      <p>
        Réduisez votre empreinte carbone en partageant vos trajets. Chaque voyage en covoiturage permet d’économiser <strong>du CO₂</strong>.
      </p>
      <p>
        En plus d’être bon pour la planète, c’est aussi bon pour votre portefeuille : nos utilisateurs économisent en moyenne <strong>60 %</strong> sur leurs frais de déplacement.
      </p>
    </div>
  </div>
</section>

<section id="economies" class="container my-5">
  <div class="row align-items-center bg-light p-4 rounded shadow-sm">
    <div class="col-md-6 order-md-2 mb-4 mb-md-0 d-flex justify-content-end">
      <img src="<?= ASSET_PATH ?>img/economie.jpg" alt="Économies sur les trajets" class="img-fluid rounded">
    </div>
    <div class="col-md-6 order-md-1">
      <h2 class="text-success">Économisez sur vos trajets</h2>
      <p class="lead">Voyagez malin avec EcoRide.</p>
      <p>Divisez vos frais de transport par le nombre de passagers.</p>
      <p>Que vous soyez conducteur ou passager, chaque trajet devient plus économique et plus durable.</p>
    </div>
  </div>
</section>


<section id="communaute" class="container my-5">
  <div class="row align-items-center bg-light p-4 rounded shadow-sm">
    <div class="col-md-6 mb-4 mb-md-0">
      <img src="<?= ASSET_PATH ?>img/covoiturage.jpg" alt="Covoiturage" class="img-fluid rounded">
    </div>
    <div class="col-md-6">
      <h2 class="text-success">Rejoignez notre communauté</h2>
      <p class="lead">Des trajets partagés, des liens créés.</p>
      <p>
        Plus de <strong>8 000 utilisateurs actifs</strong> partagent leurs trajets quotidiennement sur EcoRide. 
        Créez des liens, échangez, et voyagez en toute confiance avec des conducteurs vérifiés et des trajets sécurisés.
      </p>
      <a href="inscription.php" class="btn btn-outline-success mt-3">S'inscrire maintenant</a>
    </div>
  </div>
</section>

  <!-- ✅ Pied de page -->
  <?php include '../includes/footer.php'; ?>
  <?php include '../includes/mentions_legales.php'; ?>
 
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
<script>
document.getElementById("recherche").addEventListener("submit", function (e) {
  e.preventDefault();

  const depart = document.getElementById("depart").value;
  const arrivee = document.getElementById("arrivee").value;
  const date = document.getElementById("date").value;

  fetch(`../../backend/controllers/recherche_index.php?depart=${encodeURIComponent(depart)}&arrivee=${encodeURIComponent(arrivee)}&date=${date}`)
    .then(response => response.text())
    .then(data => {
      document.getElementById("resultats").innerHTML = data;
    })
    .catch(error => {
      document.getElementById("resultats").innerHTML = "<p class='text-danger'>Erreur lors de la recherche.</p>";
      console.error(error);
    });
});
</script>

</body>
</html>