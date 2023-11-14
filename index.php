
<?php
session_start();
define('PAGE','index');

error_reporting(E_ALL);
ini_set('display_errors', 1);
include('./outils/delete.php');
include('./outils/modifier.php');
include('./outils/note.php');
include('./navbar.php');
include('./outils/copie.php');


try {
    $bdd = new PDO('mysql:host=localhost;dbname=pronote;charset=utf8', 'root', '');
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
if (isset($_SESSION['name'])) {
    $name = $_SESSION['name'];
   
} else {
   echo'Vous n\'êtes pas connecté';
}

if (isset($_POST['filter_option'])) {
    $filterOption = $_POST['filter_option'];
    echo $filterOption;
    if ($filterOption === "1") {
        $orderBy = "lastname";
    } elseif ($filterOption === "2") {
        $orderBy = "note ASC"; 
    } elseif ($filterOption === "3") {
        $orderBy = "note DESC"; 
    }
} else {
    $orderBy = "lastname";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Formulaire PHP</title>
</head>
<body>

<button id="toggle-form" class="btn btn-success">Ajouter un élève+</button>
<a href="telecharger_csv.php" class="btn btn-primary" target="_blank">Télécharger CSV</a>
<a href="importer.php" class="btn btn-warning" >Importer</a>
<div class="formulaire">
    <form action="" method="post" enctype="multipart/form-data">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="">Prénom et Nom </span>
            </div>
            <input type="text" class="form-control" placeholder="nom" name="nom">
            <input type="text" class="form-control" placeholder="Prenom" name="prenom">
        </div>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="">Entrez une note :</span>
            </div>
            <input type="number" class="form-control" name="valeur" min="0" max="20">
        </div>
        <div class="input-group-prepend">
            <span class="input-group-text" id="">Insérer une copie </span>
            <input type="file" name="image"/>
        </div>
        <button type="submit" class="btn btn-secondary">Ajouter</button>
    </form>
</div>
<form action=""method="post" >

  <select  class="form-select form-select-lg mb-3 btn-dark" name="filter_option" aria-label=".form-select-lg example" onchange="this.form.submit()">
  <option selected>Filter </option>
  <option value="1">Nom</option>
  <option value="2" >Note croissant</option>
  <option value="3" >Note décroisant</option>
  </select> 


</form>
<?php 
$comparer = $bdd->prepare('SELECT prof_id FROM users_prof where  nom = :nom');
$comparer -> execute(array('nom' => $name));
    $row = $comparer->fetch();
    $prof_id=$row['prof_id']; 


$requeteNote = $bdd->prepare('SELECT note FROM eleve JOIN users_prof ON eleve.prof_id = users_prof.prof_id
WHERE users_prof.prof_id = :prof_id ');
$requeteNote->execute(array('prof_id' => $prof_id));

$notes = 0; 
$lesnotes= array();
while ($row = $requeteNote->fetch()) {
    $note = $row['note'];
    $lesnotes[]=$note;
    $notes += $note; 
}
echo count($lesnotes);
echo $notes;
$moyenne= $notes / count($lesnotes);
?>

<?php
echo '<table class="table table-striped">';
echo '<thead>';
echo '<tr>';
echo '<th scope="col">Id</th>';
echo '<th scope="col">Nom</th>';
echo '<th scope="col">Prénom</th>';

echo '<th scope="col">Note</th>';
echo '<th scope="col">Actions</th>';
echo '<th scope="col">Modifier</th>';
echo '<th scope="col">Insérer copie</th>';
echo '</tr>';
echo '</thead>';
$comparer = $bdd->prepare('SELECT prof_id FROM users_prof where  nom = :nom');
                    $comparer -> execute(array('nom' => $name));
                        $row = $comparer->fetch();
                        $prof_id=$row['prof_id']; 

$requete = $bdd->prepare('SELECT eleve.id, eleve.firstname, eleve.lastname, eleve.note, eleve.nomfichier, eleve.chemin_fichier
FROM eleve
JOIN users_prof ON eleve.prof_id = users_prof.prof_id
WHERE users_prof.prof_id = :prof_id ORDER BY ' . $orderBy);

$requete->execute(array('prof_id' => $prof_id));


while ($row = $requete->fetch()) {
    echo '<tbody>';
    echo '<tr>';
    echo '<td>' . $row['id'] . '</td>'; 
    echo '<td>' . $row['lastname'] . '</td>';
    echo '<td>' . $row['firstname'] . '</td>';
   
    echo '<td>' . $row['note'] . '</td>';
    echo '<td>
        <form action="" method="post">
            <input type="hidden" name="supprimer" value="' . $row['id'] . '">
            <button type="submit" name="delete" class="btn btn-danger">Supprimer</button>
        </form>
    </td>';
    echo '<td>
        <form action="" method="post">
            <input type="hidden" name="modifier" value="' . $row['id'] . '">
            <button type="submit" name="modif" class="btn btn-primary">Modifier</button>
        </form>
    </td>';
    echo '<td><a href="' . $row['chemin_fichier'] . '" value="' . $row['id'] . '" target="_blank"/>' . $row['nomfichier'] . '</a></td>';

    echo '</tr>';
    echo '</tbody>';
}
echo '<tbody>';
echo '<td> Moyenne</td>'; 
echo '<td> </td>'; 
echo '<td></td>'; 
echo '<td> '.$moyenne. '</td>'; 
echo '<td> </td>'; 
echo '<td> </td>'; 
echo '<td> </td>'; 

echo '</tbody>';
echo '</table>';
?>
<?php
include('./footer.php');
?>
<script>
    const formulaire = document.querySelector('.formulaire');
    const toggleButton = document.getElementById('toggle-form');

    toggleButton.addEventListener('click', function() {
        if (formulaire.style.display === 'none' || formulaire.style.display === '') {
    formulaire.style.display = 'block';
    toggleButton.textContent = 'Ajouter un élève-';
} else {
    formulaire.style.display = 'none'; 
    toggleButton.textContent = 'Ajouter un élève+';
}
    });
</script>
</body>
</html>