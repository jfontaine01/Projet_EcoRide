<?php
session_start();
require_once(__DIR__ . '/../../backend/config/database.php');
$pdo = getPDO();

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    echo "ID invalide.";
    exit;
}

// Vérifie que l'utilisateur est le conducteur
$userId = $_SESSION['user_id'] ?? null;
$stmt = $pdo->prepare("SELECT conducteur_id FROM covoiturage WHERE covoiturage_id = ?");
$stmt->execute([$id]);
$conducteurId = $stmt->fetchColumn();

if ($userId === $conducteurId) {
    header("Location: ../frontend/pages/detail.php?id=$id&erreur=non_conducteur");
    exit;
}

// Met à jour le statut
$stmt = $pdo->prepare("UPDATE covoiturage SET statut = 'terminé' WHERE covoiturage_id = ?");
$stmt->execute([$id]);

header("Location: ../../frontend/pages/detail.php?id=$id&statut=termine");
exit;
?>
