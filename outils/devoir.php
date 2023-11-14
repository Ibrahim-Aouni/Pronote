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
if (isset($_SESSION['name'])) {
    $name = $_SESSION['name'];
   
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


$nom_classe = "terminal S";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['filter_classe'])) {
        $filterOption = $_POST['filter_classe'];
        if ($filterOption === "1") {
            $class= "1ere C";
        } elseif ($filterOption === "2") {
            $class= "2nd B";
        } elseif ($filterOption === "3") {
            $class= "terminal S";
        } else {
            $class= "1ere B";
        }
       
    }
    if ( isset($_POST['message']) && isset($_POST['titre']) && isset($_POST['date'])) {
        $message = htmlspecialchars($_POST['message']);
        $titre = htmlspecialchars($_POST['titre']);
        $date = $_POST['date'];
        
        $comparer = $bdd->prepare('SELECT prof_id, nom FROM users_prof where  nom = :nom');
        $comparer -> execute(array('nom' => $name));
            $raw = $comparer->fetch();
            $prof_id=$raw['prof_id']; 
            $name = $raw['nom'];

        $popo= 10;
        $requete = $bdd->prepare('INSERT INTO devoir (dates, titres, messages, prof_id, nom_prof, nom_classe) VALUES (?, ?, ?, ?, ?,?)');
        if ($requete->execute([$date, $titre, $message, $prof_id, $name, $class])) {
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

<?php 
if (isset($_POST['destinataire'])) {
    $destinataire = $_POST['destinataire'];
   

    
    $comparer = $bdd->prepare('SELECT prof_id FROM users_prof WHERE nom = :nom');
    $comparer->execute(array('nom' => $name));
    $raw = $comparer->fetch();
    $prof_id = $raw['prof_id']; 

    
    $requeteClasses = $bdd->prepare('SELECT nom_classe FROM classe WHERE prof_id = :prof_id');
    $requeteClasses->execute(array('prof_id' => $prof_id));

    $classes = array(); 
    while ($row = $requeteClasses->fetch()) {
        $nom_classe = $row['nom_classe'];
        if (!in_array($nom_classe, $classes)) {
            $classes[] = $nom_classe; 
        }
    }

   
    $requeteInsertion = $bdd->prepare('INSERT INTO devoir (dates, titres, messages, prof_id, nom_prof, nom_classe, eleve_id) VALUES (?, ?, ?, ?, ?, ?, ?)');

 
    for ($i = 0; $i < count($classes); $i++) {
        echo $classes[$i]; 

        if ($destinataire == $classes[$i]) {
            $requeteEleves = $bdd->prepare('SELECT eleve_id FROM classe WHERE nom_classe = :nom_classe');
            $requeteEleves->execute([':nom_classe' => $classes[$i]]);

            $eleve_ids = []; 
            while ($row = $requeteEleves->fetch()) {
                $eleve_ids[] = $row['eleve_id']; 
            }

            foreach ($eleve_ids as $eleve_id) {
                $requeteInsertion->execute([$date, $titre, $message, $prof_id, $name, $classes[$i], $eleve_id]);
            }
        }else{
            
            $requeteEleves = $bdd->prepare('SELECT eleve_id FROM users_eleve WHERE email = :email');
            $requeteEleves->execute(['email'=>$destinataire  ]);
            $row = $requeteEleves->fetch();
            $eleve_id= $row['eleve_id']; 
            $requeteInsertion->execute([$date, $titre, $message, $prof_id, $name, NULL, $eleve_id]);

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
