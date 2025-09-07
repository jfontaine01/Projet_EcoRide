<?php
session_start();

require_once(__DIR__ . '/../../backend/config/database.php');
$pdo = getPDO();

// Récupération des données du formulaire
$email = trim($_POST['username'] ?? '');  // ← renommé pour correspondre à la base
$password = $_POST['password'] ?? '';

// Vérification de l'utilisateur
$stmt = $pdo->prepare("SELECT utilisateur_id, pseudo, role_id, password FROM utilisateur WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['utilisateur_id'];
    $_SESSION['pseudo'] = $user['pseudo'];
    $_SESSION['email'] = $email;
    $_SESSION['role'] = $user['role_id']; // ← rôle réel depuis la base

    header("Location: ../../frontend/pages/espace_utilisateur.php");
    exit;
} else {
    // Échec de connexion
    header("Location: ../../frontend/pages/login.php?error=Identifiants incorrects");
    exit;
}
?>
