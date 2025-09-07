<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

var_dump($_SESSION['user_id']);

require_once(__DIR__ . '/../../backend/config/database.php');
$pdo = getPDO();

// Récupération des données
$pseudo = $_POST['pseudo'] ?? '';
$email = $_POST['email'] ?? '';
$nom = $_POST['nom'] ?? '';
$prenom = $_POST['prenom'] ?? '';
$telephone = $_POST['telephone'] ?? '';
$adresse = $_POST['adresse'] ?? '';
$date_naissance = $_POST['date_naissance'] ?? '';
$conducteur = $_POST['conducteur'] ?? '';
$passager = $_POST['passager'] ?? '';

$fumeur = isset($_POST['fumeur']) ? $_POST['fumeur'] : '';
$animaux = isset($_POST['animaux']) ? $_POST['animaux'] : '';
$autres =  isset($_POST['autres']) ? $_POST['autres'] : '';

// Gestion de la photo
$photo = null;
if (!empty($_FILES['photo']['name'])) {
    $photo = 'uploads/' . basename($_FILES['photo']['name']);
    move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
}

// Requête SQL update profil
$sqlprofil = "UPDATE utilisateur SET pseudo = ?, email = ?, nom = ?, prenom = ?, telephone = ?, adresse = ?, date_naissance = ?, conducteur = ?, passager = ?";
$paramsprofil = [$pseudo, $email, $nom, $prenom, $telephone, $adresse, $date_naissance, $conducteur, $passager];

if ($photo) {
    $sqlprofil .= ", photo = ?";
    $paramsprofil[] = $photo;
}

$sqlprofil .= " WHERE utilisateur_id = ?";
$paramsprofil[] = $_SESSION['user_id'];

$profil = $pdo->prepare($sqlprofil);
$profil->execute($paramsprofil);


// Requête SQL update préférences conducteur
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT COUNT(*) FROM preferences_conducteur WHERE utilisateur_id = ?");
$stmt->execute([$user_id]);
$exists = $stmt->fetchColumn();

if ($exists) {
    // UPDATE
    $update = $pdo->prepare("UPDATE preferences_conducteur SET fumeur = ?, animaux = ?, autres = ? WHERE utilisateur_id = ?");
    $update->execute([$fumeur, $animaux, $autres, $user_id]);
} else {
    // INSERT
    $insert = $pdo->prepare("INSERT INTO preferences_conducteur (utilisateur_id, fumeur, animaux, autres) VALUES (?, ?, ?, ?)");
    $insert->execute([$user_id, $fumeur, $animaux, $autres]);
}




header("Location: ../../frontend/pages/espace_utilisateur.php?update=success");
exit;
?>
