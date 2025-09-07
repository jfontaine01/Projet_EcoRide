<?php

require_once(__DIR__ . '/../../backend/config/database.php');
$pdo = getPDO();

$depart = $_GET['depart'] ?? '';
$arrivee = $_GET['arrivee'] ?? '';
$date = $_GET['date'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM covoiturage WHERE lieu_depart LIKE ? AND lieu_arrivee LIKE ? AND date_depart = ?");
$stmt->execute(["%$depart%", "%$arrivee%", $date ]);

$results = $stmt->fetchAll();

if (count($results) === 0) {
  echo "<p>Aucun trajet trouvé.</p>";
} else {
  foreach ($results as $row) {
    $date = new DateTime($row['date_depart']);
    $formattedDate = $date->format('d/m/Y');

    echo "<div class='card mb-2'><div class='card-body'>";
    echo "<div class='d-flex justify-content-between align-items-center'>";
    echo "<h5 class='card-title mb-0'>{$row['lieu_depart']} → {$row['lieu_arrivee']}</h5>";
    echo "<span class='text-end fw-bold'>{$row['prix_personne']} €</span>";
    echo "</div>";
    echo "<p class='card-text mt-2'>Date : {$formattedDate} | Places : {$row['nb_place']}</p>";
    echo "</div></div>";
  }
}
