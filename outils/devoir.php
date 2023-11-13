<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$matiere = ''; 

if (isset($_SESSION['matiere'])) {
    $matiere = $_SESSION['matiere'];
} else {
    echo 'Vous n\'êtes pas connecté';
}


try {
    $bdd = new PDO('mysql:host=localhost;dbname=pronote;charset=utf8', 'root', '');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}

include('../navbar.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ( isset($_POST['message']) && isset($_POST['titre']) && isset($_POST['date'])) {
        $message = htmlspecialchars($_POST['message']);
        $titre = htmlspecialchars($_POST['titre']);
        $date = $_POST['date'];
        

        $requete = $bdd->prepare('INSERT INTO devoir (dates, titres, messages) VALUES (?, ?, ?)');
        if ($requete->execute([$date, $titre, $message])) {
            echo "Les fichiers ont bien été enregistrés";
        } else {
            echo "Erreur lors de l'enregistrement : " . $requete->errorInfo()[2];
        }
    }
}

?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <title>Formulaire Bootstrap avec Message</title>
</head>
<body>

<div class="container mt-5">
    <form method="post" action="">
        <div class="form-group">
            <h1 ><?php echo  $name .' ' ?> - <?php echo $matiere . '  ' ?></h1>
        </div>
        <div class="form-group">
            <label for="date">Pour le :</label>
            <input type="date" class="form-control" id="date" name="date" value="<?php echo date('Y-m-d'); ?>">
        </div>
        <div class="form-group">
            <label for="titre">Titre :</label>
            <input type="text" class="form-control" id="titre" name="titre" placeholder="Entrez le titre">
        </div>
        <div class="form-group">
            <label for="message">Message :</label>
            <input type="text" class="form-control" id="message" name="message" placeholder="Entrez le message">
        </div>
        <button type="submit" class="btn btn-primary">Envoyer</button>
    </form>
    <div class=" d-flex container">

    <?php

$devoirs = $bdd->prepare('SELECT dates, titres, messages FROM devoir');
$devoirs->execute();
$resultats = $devoirs->fetchAll(PDO::FETCH_ASSOC);

foreach ($resultats as $row) {
    echo '
    <div class="card" style="width: 18rem;">
        <div class="card-body">
            <h4>' .$name.'-'. $matiere.'</h4>
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

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
