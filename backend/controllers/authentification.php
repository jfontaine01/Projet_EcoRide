<?php
// Démarre la session si elle n'est pas déjà lancée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    // Redirige vers la page de connexion si non connecté
    header('Location: login.php');
    exit;
}