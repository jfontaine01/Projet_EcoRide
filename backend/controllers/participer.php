<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/public/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['participer'])) {
    $userId = $_SESSION['user_id'];
    $covoiturageId = $_POST['covoiturage_id'];

    try {
        require_once(__DIR__ . '/../../backend/config/database.php');
        $pdo = getPDO();

        // Vérifie si déjà inscrit
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM covoiturage_participants WHERE covoiturage_id = ? AND covoiturage_participants_id = ?");
        $stmt->execute([$covoiturageId, $userId]);
        $alreadyJoined = $stmt->fetchColumn() > 0;

        if (!$alreadyJoined) {
            $stmt = $pdo->prepare("INSERT INTO covoiturage_participants (covoiturage_id, covoiturage_participants_id) VALUES (?, ?)");
            $stmt->execute([$covoiturageId, $userId]);
        }

        header("Location: ../../frontend/pages/detail.php?id=" . $covoiturageId);
        exit;

    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>
