<?php
session_start();

try {
    require_once(__DIR__ . '/../../backend/config/database.php');
    $pdo = getPDO();
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupération des données du formulaire
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$pseudo = trim($_POST['pseudo'] ?? '');
$role_id = 2; // Par défaut : employé
$status_id = 1; // Par défaut : compte actif
$photo = '';

// Vérification si l'email existe déjà
$stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateur WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetchColumn() > 0) {
    header("Location: ../../frontend/pages/admin.php?error=Email déjà utilisé");
    exit;
}

// Hash du mot de passe
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insertion dans la base
$stmt = $pdo->prepare("INSERT INTO utilisateur (email, password, pseudo, role_id, status_id, photo, cree_le, maj_le)
VALUES (?, ?, ?, ?, ?, ?,NOW(), NOW())");

$stmt->execute([$email, $hashedPassword, $pseudo, $role_id, $status_id, $photo ]);

// Démarrage de session
//$_SESSION['user_id'] = $pdo->lastInsertId();
//$_SESSION['pseudo'] = $pseudo;
//$_SESSION['email'] = $email;
//$_SESSION['role'] = $role_id;

header("Location: ../../frontend/pages/admin.php?section=comptes");
exit;
?>
