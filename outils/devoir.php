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
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
  } else {
   echo'Vous n\'êtes pas connecté';
  }

try {
    $bdd = new PDO('mysql:host=localhost;dbname=pronote;charset=utf8', 'root', '');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}

include('../navbar.php');


   


?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <title>Formulaire Bootstrap avec Message</title>
    <style>
        body{
            background-color: #ECF8F6 ;

        color:  #226D68;
      }
    </style>
</head>
<body>

<?php 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
if (isset($_POST['destinataire']) && isset($_POST['message']) && isset($_POST['titre']) && isset($_POST['date'])) 

        $message = htmlspecialchars($_POST['message']);
        $titre = htmlspecialchars($_POST['titre']);
        $date = $_POST['date'];
        $destinataire = $_POST['destinataire'];
   

    
    $comparer = $bdd->prepare('SELECT prof_id, email, nom FROM users_prof WHERE email = :nom');
    $comparer->execute(array('nom' => $email));
    $raw = $comparer->fetch();
    $name= $raw['nom'];
    $prof_id = $raw['prof_id']; 

    
    $requeteClasses = $bdd->prepare('SELECT mail_classe , classe_id FROM classe ');
    $requeteClasses->execute();

    $classes = array(); 
    while ($row = $requeteClasses->fetch()) {
        $mail_classe = $row['mail_classe'];
        if (!in_array($mail_classe, $classes)) {
            $classes[] = $mail_classe; 
        }
    }

   
    $requeteInsertion = $bdd->prepare('INSERT INTO devoir (dates, titres, messages, classe_id, prof_id, nom_prof, eleve_id) VALUES (?, ?, ?, ?, ?, ?, ?)');

    if (!empty($classes)) {
        $destinataireIsClass = in_array($destinataire, $classes);
    
        if ($destinataireIsClass) {
            $requeteEleves = $bdd->prepare('SELECT classe_id, mail_classe FROM classe WHERE mail_classe = :classe_id');
            $requeteEleves->execute(['classe_id' => $destinataire]);
            $row = $requeteEleves->fetch();
            $classe_id = $row['classe_id'];
            $requeteInsertion->execute([$date, $titre, $message, $classe_id, $prof_id, $name, NULL]);
        } else {
            $requeteEleves = $bdd->prepare('SELECT eleve_id FROM users_eleve WHERE email = :email');
            $requeteEleves->execute(['email' => $destinataire]);
            $row = $requeteEleves->fetch();
            $eleve_id = $row['eleve_id'];
            $requeteInsertion->execute([$date, $titre, $message, NULL, $prof_id, $name, $eleve_id]);
        }
    }
    
    }
   


?>

<div class="container mt-5">
    <form method="post" action="">
        <div class="form-group">
            <h1 ><?php echo  $name .' ' ?> - <?php echo $matiere . '  ' ?></h1>
        </div>
        <div class="form-group">
            <label for="date">Envoyer à:</label>
        <select  class="form-select" aria-label="Disabled select example" name="filter_classe" aria-label=".form-select-lg example"   >    
  <option selected>Envoyer à: </option>
  
  <option value="1">1ere C</option>
  <option value="2" > 2nd B</option>
  <option value="3" >Terminal S</option>
  <input type="text" name="destinataire">
  <?php
  
  
  
  ?>
  </select> 

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
