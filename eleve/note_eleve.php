<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

try {
    $bdd = new PDO('mysql:host=localhost;dbname=pronote;charset=utf8', 'root', '');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}
include('./navbar_eleve.php');

if (isset($_SESSION['nom'])) {
    $nom= $_SESSION['nom'];
} else {
    echo 'Vous n\'êtes pas connecté';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    
    <?php

    $eleve = $bdd->prepare('SELECT eleve_id FROM users_eleve WHERE nom = :nom');
    $eleve->execute(array('nom' => $nom));
    $raw = $eleve->fetch();
    $eleve_id = $raw['eleve_id'];
$devoirs = $bdd->prepare('SELECT dates, titres, messages FROM devoir where eleve_id =:eleve_id');
$devoirs->execute(array('eleve_id' => $eleve_id));
$resultats = $devoirs->fetchAll(PDO::FETCH_ASSOC);

foreach ($resultats as $row) {
    echo '
    <div class="card" style="width: 18rem;">
        <div class="card-body">
            
            <h5 class="card-title">' . $row['titres'] . '</h5>
            <h6 class="card-subtitle mb-2 text-muted">' . $row['dates'] . '</h6>
            <p class="card-text">' . $row['messages'] . '</p>
            <a href="" class="card-link">Card link</a>
            <a href="#" class="card-link">Another link</a>
        </div>
    </div>';
}
?>
    
    </div>

    <div class="alert alert-success mt-3" role="alert">
        Votre formulaire a été soumis avec succès!
    </div>
</div>
</head>
<body>
    
</body>
</html>

