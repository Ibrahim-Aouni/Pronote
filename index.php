<?php
session_start();
define('PAGE','index');

error_reporting(E_ALL);
ini_set('display_errors', 1);
include('./delete.php');
include('./modifier.php');
include('./note.php');
include('./navbar.php');






try {
    $bdd = new PDO('mysql:host=localhost;dbname=pronote;charset=utf8', 'root', '');
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $error = 1;
    if ($_FILES['image']['size'] <= 3000000) {
        $informationfichier = pathinfo($_FILES['image']['name']);
        $extensionfichier = $informationfichier['extension'];
        $extensionArray = array("jpeg", "jpg", "png", "pdf");
        $adresse = 'upload/' . time() . rand() . rand() . '.' . $extensionfichier;
        if (in_array($extensionfichier, $extensionArray)) {
            move_uploaded_file($_FILES['image']['tmp_name'], $adresse);
            $error = 0;
            echo '';
        }
    }
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <style>
        .formulaire{
            display:none;
        }

    </style>

    <title>Formulaire PHP</title>
</head>
<body>
    
<button id="toggle-form" class="btn btn-success">Ajouter un élève+</button>
<a href="telecharger_csv.php" class="btn btn-primary">Télécharger CSV</a>
<a href="importer.php" class="btn btn-warning">Importer</a>
<div class="formulaire">
    <form action="" method="post">
        <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text" id="">Prenom et Nom </span>
        </div>
        
        <input type="text" class="form-control" placeholder="Nom" name="nom">
        <input type="text" class="form-control" placeholder="prenom" name="prenom">
        </div>
        <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text" id="">Entrez une note :</span>
        </div>
        <input type="number" class="form-control" name="valeur" min="0" max="20">
        
        </div>
        <div class="input-group-prepend">
            <span class="input-group-text" id="">Insérer une copie </span>
        
            <form method="post" action="" enctype="multipart/form-data">
            <input type="file" name="image"/>
            <br/>
        </div>
        <button type="submit" class="btn btn-secondary">Ajouter</button>
    </form>
</div>
<div class="contener">
         <?php
      
            echo'<div id="presentation-picture">
            <img src="'.$adresse.'" id="image"/>
            <br/>
            <input type="text" value="htts://localhost/'.$adresse.'"/>
            </div>';
        
        ?>
</div>

<?php
echo '<table class="table table-striped">';
echo '<thead>';
echo '<tr>';
echo '<th scope="col">Id</th>';
echo '<th scope="col">Prénom</th>';
echo '<th scope="col">Nom</th>';
echo '<th scope="col">Note</th>';
echo '<th scope="col">Actions</th>';
echo '<th scope="col">Modifier</th>';
echo '<th scope="col">Insérer copie</th>';

echo '</tr>';
echo '</thead>';

$requete = $bdd->query('SELECT id, firstname, lastname, note FROM eleve');

while ($row = $requete->fetch()) {
    echo '<tbody>';
    echo '<tr>';
    echo '<td>' . $row['id'] . '</td>';
    echo '<td>' . $row['firstname'] . '</td>';
    echo '<td>' . $row['lastname'] . '</td>';
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
    echo '<td>
    cucou
    </td>';

    echo '</tr>';
    echo '</tbody>';
}

echo '</table>';
?>
<?php
include('./footer.php');
?>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script>
    
    const formulaire= document.querySelector('.formulaire')
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