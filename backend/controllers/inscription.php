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
$role_id = 3; // Par défaut : utilisateur
$status_id = 1; // Par défaut : compte actif
$photo = '';

// Vérification si l'email existe déjà
$stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateur WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetchColumn() > 0) {
    header("Location: ../../frontend/pages/inscription.php?error=Email déjà utilisé");
    exit;
}

// Hash du mot de passe
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Récupérer le crédit initial depuis la table parametre
$stmt = $pdo->prepare("SELECT valeur FROM parametre WHERE propriete = ?");
$stmt->execute(['credit_initial']);
$creditInitial = (int)($stmt->fetchColumn() ?? 0); // Valeur par défaut si non trouvée

// Insertion dans la base
$stmt = $pdo->prepare("INSERT INTO utilisateur (email, password, pseudo, role_id, status_id, photo, cree_le, maj_le, credit)
VALUES (?, ?, ?, ?, ?, ?,NOW(), NOW(), ?)");

$stmt->execute([$email, $hashedPassword, $pseudo, $role_id, $status_id, $photo, $creditInitial]);

// Démarrage de session
$_SESSION['user_id'] = $pdo->lastInsertId();
$_SESSION['pseudo'] = $pseudo;
$_SESSION['email'] = $email;
$_SESSION['role'] = $role_id;

header("Location: ../../frontend/pages/espace_utilisateur.php");
exit;
?>
