<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('./navbar.php');
include('./outils/delete.php');

try {
    $bdd = new PDO('mysql:host=localhost;dbname=pronote;charset=utf8', 'root', '');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
if (isset($_SESSION['classe'])) {
    $classe = $_SESSION['classe'];
   
} else {
   echo'Vous n\'êtes pas connecté';
}



if (isset($_SESSION['name'])) {
    $name = $_SESSION['name'];
} else {
    echo 'Vous n\'êtes pas connecté';
}
$class='';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['filter_option'])) {
        $filterOption = $_POST['filter_option'];
        if ($filterOption === "1") {
            $_SESSION['classe'] = "1ere C";
        } elseif ($filterOption === "2") {
            $_SESSION['classe'] = "2nd B";
        } elseif ($filterOption === "3") {
            $_SESSION['classe'] = "terminal S";
        } else {
            $_SESSION['classe'] = "1ere B";
        }
        echo "Classe définie dans POST : " . $_SESSION['classe'];
        $classe = $_SESSION['classe'];
    }
    $class="";
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


if (isset($_POST['nom']) && isset($_POST['prenom'])) {
    $nom = strip_tags(htmlspecialchars($_POST['nom']));
    $prenom = strip_tags(htmlspecialchars($_POST['prenom']));

    $prof = $bdd->prepare('SELECT prof_id FROM users_prof WHERE nom = :nom');
    $prof->execute(array('nom' => $name));
    $raw = $prof->fetch();
    $prof_id = $raw['prof_id'];

   
    echo $classe;

    $comparer = $bdd->prepare('SELECT eleve_id, nom, prenom FROM users_eleve WHERE nom = :nom AND prenom = :prenom');
    $comparer->execute(array('nom' => $nom, 'prenom' => $prenom));
    $row = $comparer->fetch();
    $eleve_id = $row['eleve_id'];
    $nom= $row['nom'];
    $prenom =$row['prenom'];

    $sql = $bdd->prepare('SELECT email FROM users_eleve WHERE eleve_id = :eleve_id');
    $sql->execute(['eleve_id'=> $eleve_id  ]);
    $raw = $sql->fetch();
    $email= $raw['email'];

    $requete = $bdd->prepare('INSERT INTO classe (eleve_id, prof_id, nom_classe,nom_eleve, prenom_eleve,nom_prof, email_eleve ) VALUES (?,?,?,?,?,?,?)');
    if ($requete->execute([$eleve_id, $prof_id, $classe, $nom, $prenom, $name, $email])) {
        echo "Enregistrement en base de données réussi.";
    } else {
        echo "Erreur lors de l'enregistrement en base de données.";
    }
}
}

unset($_SESSION['classe']);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Formulaire PHP</title>
</head>
<body>

<div class="formulaire">
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
  <option value="1">1ere C</option>
  <option value="2" > 2nd B</option>
  <option value="3" >Terminal S</option>
  </select> 

</form>

<?php 


$nom_classe = "terminal S";
$requete = $bdd->prepare('SELECT eleve_id FROM classe WHERE nom_classe = :nom_classe');
$requete->execute([':nom_classe' => $nom_classe]);

$eleve_ids = []; 

while ($row = $requete->fetch()) {
    $eleve_ids[] = $row['eleve_id']; 
}


foreach ($eleve_ids as $eleve_id) {
    echo $eleve_id . '<br>'; 
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


$requete = $bdd->prepare('SELECT classe.classe_id, classe.nom_classe, classe.annee, classe.eleve_id, classe.nom_eleve, classe.prenom_eleve, classe.email_eleve
FROM classe 

where classe.nom_classe =:nom  
 ');

$requete->execute([':nom' => $class]);


while ($row = $requete->fetch()) {
    echo '<tbody>';
    echo '<tr>';
    echo '<td>' . $row['classe_id'] . '</td>'; 
    echo '<td>' . $row['nom_classe'] . '</td>';
    echo '<td>' . $row['nom_eleve'].'</td>';
    echo '<td>' . $row['prenom_eleve'].'</td>';
    echo '<td>' . $row['email_eleve'].'</td>';
   
 
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
    echo '</tbody>';
}

echo '</table>';
?>
<?php
include('./footer.php');
?>


</body>
</html>