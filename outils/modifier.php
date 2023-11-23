<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('note.php');
try {
    $bdd = new PDO('mysql:host=localhost;dbname=pronote;charset=utf8', 'root', '');
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}




$idModifier = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Connexion à la base de données (à ajuster selon votre configuration)

    // Vérification de la présence de la clé 'modifier' dans les données POST
    if (isset($_POST['modifier'])) {
        // Récupération de l'ID de l'examen à modifier
        $id_examen = $_POST['modifier'];

        // Récupération des nouvelles valeurs de la note et de l'appréciation
        $new_note = isset($_POST['mark_' . $id_examen]) ? $_POST['mark_' . $id_examen] : null;
        $new_appreciation = isset($_POST['appreciate_' . $id_examen]) ? $_POST['appreciate_' . $id_examen] : null;

        // Requête SQL pour mettre à jour la note et l'appréciation de l'examen spécifié
        $modifier = $bdd->prepare('UPDATE examen SET appreciation = :appreciation, note = :note WHERE id_examen = :id');
        $modifier->execute(array(
            'appreciation' => $new_appreciation,
            'note' => $new_note,
            'id' => $id_examen
        ));

        // Vérification du succès de la mise à jour
        if ($modifier->rowCount() > 0) {
            echo "Mise à jour réussie pour l'examen avec l'ID : $id_examen";
        } else {
            echo "Aucune modification effectuée pour l'examen avec l'ID : $id_examen";
        }
    }
}
?>
