<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

try {
    require_once(__DIR__ . '/../../backend/config/database.php');
    $pdo = getPDO();

    // Récupération des données du formulaire
    $lieu_depart = $_POST['lieu_depart'] ?? '';
    $lieu_arrivee = $_POST['lieu_arrivee'] ?? '';
    $date_depart = $_POST['date_depart'] ?? '';
    $heure_depart = $_POST['heure_depart'] ?? '';
    $nb_place = $_POST['nb_place'] ?? '';
    $voiture_id = $_POST['voiture_id'] ?? '';

    // Validation minimale
    if (empty($lieu_depart) || empty($lieu_arrivee) || empty($date_depart) || empty($heure_depart) || empty($nb_place) || empty($voiture_id)) {
        throw new Exception("Tous les champs sont requis.");
    }

    // Insertion du trajet
    $stmt = $pdo->prepare("INSERT INTO covoiturage (conducteur_id, lieu_depart, lieu_arrivee, date_depart, heure_depart, nb_place, voiture_id, statut)
                           VALUES (?, ?, ?, ?, ?, ?, ?, 'ouvert')");
    $stmt->execute([
        $_SESSION['user_id'],
        $lieu_depart,
        $lieu_arrivee,
        $date_depart,
        $heure_depart,
        $nb_place,
        $voiture_id
    ]);

    // Redirection avec succès
    header("Location: ../../frontend/pages/espace_utilisateur.php?update=success");
    exit;

} catch (Exception $e) {
    echo "<h2>Erreur lors de l'ajout du trajet :</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<a href='../../frontend/pages/espace_utilisateur.php'>Retour</a>";
}
?>
