<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

try {
    $bdd = new PDO('mysql:host=localhost;dbname=pronote;charset=utf8', 'root', '');
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}


if (isset($_POST['supprimer'])) {
    $idSuppression = $_POST['supprimer'];

    $suppression = $bdd->prepare('DELETE FROM eleve WHERE id = :id');
    $suppression->execute(array('id' => $idSuppression));

    echo "Ligne supprimée avec succès.";
}

?>