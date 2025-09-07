<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
  require_once(__DIR__ . '/../../backend/config/database.php');
  $pdo = getPDO();

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['voiture_id'])) {
    $voiture_id = $_POST['voiture_id'];

    // Vérifie que le véhicule existe
    $check = $pdo->prepare("SELECT * FROM voiture WHERE voiture_id = ?");
    $check->execute([$voiture_id]);

    if ($check->rowCount() > 0) {
      // Supprime le véhicule
      $stmt = $pdo->prepare("DELETE FROM voiture WHERE voiture_id = ?");
      $stmt->execute([$voiture_id]);

      header("Location: ../../frontend/pages/espace_utilisateur.php?delete=success");
      exit;
    } else {
      header("Location: ../../frontend/pages/espace_utilisateur.php?delete=notfound");
      exit;
    }
  } else {
    header("Location: ../../frontend/pages/espace_utilisateur.php?delete=invalid");
    exit;
  }
} catch (PDOException $e) {
  echo "❌ Erreur PDO : " . $e->getMessage();
  exit;
}
?>

