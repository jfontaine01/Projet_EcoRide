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
    $note = $_POST['note'] ?? '';
    $commentaire = $_POST['commentaire'] ?? '';
    $statut = 'à modérer';
    $avis_id = '';
    $covoiturage_id = '';
    $conducteur_id = '';
    $pasager_id = '';

    // Validation minimale
    if (empty($note) || empty($commentaire) || empty($statut)) {
        throw new Exception("Tous les champs sont requis.");
    }

    // Insertion dr l'avis
    $stmt = $pdo->prepare("INSERT INTO avis (note, commentaire, statut, avis_id, covoiturage_id, conducteur_id, passager_id, cree_le, valide_le, valid_id )
                           VALUES (?, ?, ?, ?, ?, ?, ?, NOW, NULL, NULL)");
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
    header("Location: ../../frontend/pages/detail.php?update=success");
    exit;

} catch (Exception $e) {
    echo "<h2>Erreur lors de l'ajout d'un avis :</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<a href='../../frontend/pages/detail.php'>Retour</a>";
}
?>
