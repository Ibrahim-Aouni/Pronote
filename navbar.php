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

if (isset($_SESSION['email'])) {
  $email = $_SESSION['email'];
} else {
 echo'Vous n\'êtes pas connecté';
}




$requete= $bdd->prepare('SELECT nom , email FROM users_prof WHERE email = :email');
$requete ->execute(['email' => $email]);
$row = $requete->fetch();
if ($row) {
  $name = $row['nom'];
} else {
  echo 'Aucun utilisateur trouvé avec cet email.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <title>Document</title>
    <style>
      body{
        background-color: #18534F ;
        color:  #226D68;
      }
      nav{
        padding-bottom:100px;
      }
      
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg " style="background-color: #226D68;color: white">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarText" style="color: white">
    <ul class="navbar-nav mr-auto">
    <li class="nav-item">
    <?php echo '<a class="nav-link"style="color:#D6955B ;"   href="../information_personnelle.php">' . $name . '</a>'; ?>
        
      </li>
      <li class="nav-item">
        <a class="nav-link"style="color:#D6955B ;"  href="../index.php">Home</a>
      </li>
      <li class="nav-item">
        <a class="nav-link"style="color:#D6955B ;"   href="../outils/devoir.php">Devoir</a>
      </li>
     
      <li class="nav-item">
        <a class="nav-link" style="color:#D6955B ;"  href="../classe.php">Classe</a>
      </li>
    </ul>
    <span class="navbar-text ml-auto">
      <?php echo '<span class="mr-3">' . $name . '</span>'; ?>
    </span>
  </div>
</nav>



<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>

</body>
</html>