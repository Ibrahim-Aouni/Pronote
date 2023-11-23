<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);


try {
    $bdd = new PDO('mysql:host=localhost;dbname=pronote;charset=utf8', 'root', '');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}


include('./navbar.php');
include('./outils/delete.php');

if (isset($_SESSION['email'])) {
$email = $_SESSION['email'];
   echo $email;
} else {
   echo'Vous n\'êtes pas connecté';
}

if (isset($_SESSION['classe'])) {
    $classe = $_SESSION['classe'];
   
} else {
   echo'Vous n\'êtes pas connecté';
}



$classe_id='';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    $classe_id="";
    if (isset($_POST['filter_classe'])) {
        $filterOption = $_POST['filter_classe'];
        if ($filterOption === "1") {
            $classe_id= 1;
        } elseif ($filterOption === "2") {
            $classe_id= 2;
        } elseif ($filterOption === "3") {
            $classe_id= 4;
        } else {
            $classe_id= 1;
        }
       
    }

}


unset($_SESSION['classe']);
?>
<?php

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Formulaire PHP</title>
</head>
<body>

<div class="formulaire">
    <h1></h1>
    <p></p>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="">Prénom et Nom </span>
            </div>
            <input type="text" class="form-control" placeholder="nom" name="nom">
            <input type="text" class="form-control" placeholder="prenom" name="prenom">
        </div>
        <div class="input-group">
            <div class="input-group-prepend">
 <select  class="form-select form-select-lg mb-3 " name="filter_option" aria-label=".form-select-lg example">
  <option selected>Filter </option>
  <option value="1">1ere C</option>
  <option value="2" > 2nd B</option>
  <option value="3" >Terminal C</option>
  </select> 

        </div>
        </div>
       
        <button type="submit" class="btn btn-secondary">Ajouter</button>
    </form>
</div>
<form action="" method="post" >
<select  class="form-select form-select-lg mb-3 " name="filter_classe" aria-label=".form-select-lg example" onchange="this.form.submit()">
  <option selected>Filter </option>
  <option value="1">2nd A</option>
  <option value="2" > 2nd B</option>
  <option value="3" >2nd C</option>
  </select> 

</form>

<?php 


 $requete = $bdd->prepare('SELECT eleve_id FROM users_eleve WHERE classe_id = :classe_id');
 $requete->execute(['classe_id' => $classe_id]);
 $eleve_ids = []; 

 while ($row = $requete->fetch()) {
     $eleve_ids[] = $row['eleve_id']; 
 }


 foreach ($eleve_ids as $eleve_id) {
    $requete->execute([
        'nom' => $row['nom'],
        'prenom' => $row['prenom'],
        'appreciation' => $row['appreciation'],
        'matiere' => $row['matiere'],
    ]);
}

?>
<?php
echo '<table class="table table-striped">';
echo '<thead>';
echo '<tr>';
echo '<th scope="col">Id</th>';
echo '<th scope="col">Nom de la Classe </th>';
echo '<th scope="col">Nom de l\' élève</th>';
echo '<th scope="col">Prenom de l\' élève</th>';
echo '<th scope="col">Email</th>';

echo '<th scope="col">Actions</th>';
echo '<th scope="col">Modifier</th>';

echo '</tr>';
echo '</thead>';




$requete = $bdd->prepare('SELECT users_eleve.nom, users_eleve.prenom, users_eleve.email, classe.classe_id , classe.nom_classe
    FROM users_eleve 
    JOIN classe ON users_eleve.classe_id = classe.classe_id 
    WHERE users_eleve.classe_id = :classe_id');

$requete->execute([':classe_id' => $classe_id]);

echo '<tbody>';

while ($row = $requete->fetch()) {
    echo '<tr>';
    echo '<td>' . $row['classe_id'] . '</td>'; 
    echo '<td>' . $row['nom_classe'] . '</td>';
    echo '<td>' . $row['nom'] . '</td>';
    echo '<td>' . $row['prenom'] . '</td>';
    echo '<td>' . $row['email'] . '</td>';
   
    echo '<td>
        <form action="" method="post">
            <input type="hidden" name="supprimer" value="' . $row['classe_id'] . '">
            <button type="submit" name="delete" class="btn btn-danger">Supprimer</button>
        </form>
    </td>';
    echo '<td>
        <form action="" method="post">
            <input type="hidden" name="modifier" value="' . $row['classe_id'] . '">
            <button type="submit" name="modif" class="btn btn-primary">Modifier</button>
        </form>
    </td>';
    echo '</tr>';
}

echo '</tbody>';


echo '</table>';
?>
<?php
include('./footer.php');
?>


</body>
</html>