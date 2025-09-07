<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/../../backend/config/database.php');
$pdo = getPDO();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (
    isset($_POST['utilisateur_id']) &&
    isset($_POST['marque_id']) &&
    isset($_POST['modele']) &&
    isset($_POST['immatriculation']) &&
    isset($_POST['couleur'])
  ) {
    $stmt = $pdo->prepare("INSERT INTO voiture (utilisateur_id, marque_id, modele, immatriculation, couleur) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
      $_POST['utilisateur_id'],
      $_POST['marque_id'],
      $_POST['modele'],
      $_POST['immatriculation'],
      $_POST['couleur']
    ]);

    if ($stmt->rowCount() > 0) {
        header("Location: ../../frontend/pages/espace_utilisateur.php?create=success");
        exit;
    }
  }
} 
?>
