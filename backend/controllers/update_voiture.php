<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
  require_once(__DIR__ . '/../../backend/config/database.php');
  $pdo = getPDO();

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
      isset($_POST['voiture_id']) &&
      isset($_POST['marque_id']) &&
      isset($_POST['modele']) &&
      isset($_POST['immatriculation']) &&
      isset($_POST['couleur'])
    ) {
      $stmt = $pdo->prepare("UPDATE voiture SET marque_id = ?, modele = ?, immatriculation = ?, couleur = ? WHERE voiture_id = ?");
      $stmt->execute([
        $_POST['marque_id'],
        $_POST['modele'],
        $_POST['immatriculation'],
        $_POST['couleur'],
        $_POST['voiture_id']
      ]);

      if ($stmt->rowCount() > 0) {
        header("Location: ../../frontend/pages/espace_utilisateur.php?update=success");
      } else {
        header("Location: ../../frontend/pages/espace_utilisateur.php?update=none");
      }
      exit;
    } else {
      echo "❌ Données manquantes.";
      exit;
    }
  }
} catch (PDOException $e) {
  echo "❌ Erreur : " . $e->getMessage();
}
?>
