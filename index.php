<?php
session_start();
define('PAGE','index');

error_reporting(E_ALL);
ini_set('display_errors', 1);
include('./outils/importer.php');
include('./outils/note.php');
include('./navbar.php');
include('./outils/modifier.php');


try {
    $bdd = new PDO('mysql:host=localhost;dbname=pronote;charset=utf8', 'root', '');
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
if ($_SESSION['email']) {
    $email = $_SESSION['email'];
   
} else {
   echo'';
}

if(isset($_POST['titre_examen'])) {
    $titre_examen= htmlspecialchars($_POST['titre_examen']);
} 
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Formulaire PHP</title>

    <style>
          html, body {
            height: 100%;
            margin: 0;
            
        }

        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40; /* couleur noir */
            padding-top: 50px; /* espace pour les boutons */
            color: white; /* couleur du texte */
        }

        .sidebar ul {
            list-style: none;
            padding-left: 0;
        }

        .sidebar ul li a {
            display: block;
            padding: 10px 15px;
            color: white;
            text-decoration: none;
        }

        .sidebar ul li a:hover {
            background-color: #495057; /* couleur hover */
        }

        .content {
            margin-left: 250px; /* ajustement du contenu principal */
            padding: 20px; /* espace autour du contenu */
        }
        .container {
            display: flex;
        }
        .show-table {
            width: 100%;
            border: 1px solid #ccc;
            padding: 10px;
            cursor: pointer;
        }
        .class-section {
            width: 33%;
            border: 1px solid #ccc;
            padding: 10px;
            cursor: pointer;
        }
        .exam-folder {
            display: none;
        }
        .table-display {
            display: none;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        .hidden-table {
        display: none;
        }
    </style>
</head>
<body>

     


<a href="../outils/telecharger_csv.php" class="btn btn-primary" target="_blank">Télécharger CSV</a>
<a href="../outils/importer.php" class="btn btn-warning" >Importer</a>

        <button type="submit" class="btn btn-secondary">Ajouter</button>
    </form>
</div>
<button onclick="showInput()" class="btn btn-primary">Afficher Input</button>
<div id="inputSection" style="display: none;">
    <input type="text" id="inputField" class="form-control" placeholder="Saisir quelque chose">
    <button onclick="createTable()" class="btn btn-success">Créer Tableau</button>
</div>
<?php

 $requeteProf = $bdd->prepare('SELECT prof_id,email FROM users_prof WHERE email= :email');
 $requeteProf ->execute(['email' => $email]);
 $rew = $requeteProf->fetch();
 $prof_id = $rew['prof_id'];

$requete = $bdd->prepare('SELECT prof_anglais, nom_classe FROM classe WHERE prof_anglais = :prof_id');
$requete->execute(['prof_id' => $prof_id]);

$nom_classes = [];
while ($row = $requete->fetch()) {
    $nom_classes[] = $row['nom_classe'];
}
?>



<?php 
$comparer = $bdd->prepare('SELECT prof_id FROM users_prof where  nom = :nom');
$comparer -> execute(array('nom' => $name));
    $row = $comparer->fetch();
    $prof_id=$row['prof_id']; 

?>


<?php 
$date = '';
$email = $_SESSION['email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['titre_examen']) ) {
        $titre_examen = htmlspecialchars($_POST['titre_examen']);
        

    }

    foreach ($_POST as $key => $value) {
        if (strpos($key, 'note_') === 0 && isset($_POST['intitule_' . substr($key, 5)], $_POST['appreciation_' . substr($key, 5)])&& isset($_POST['date'])) {
            $eleve_id = substr($key, 5);
            $appreciation = htmlspecialchars($_POST['appreciation_' . $eleve_id], ENT_QUOTES);
            $intitule = htmlspecialchars($_POST['intitule_' . $eleve_id], ENT_QUOTES);
            $note = floatval($_POST['note_' . $eleve_id]);
            $date = $_POST['date'];
           

            $requeteProf = $bdd->prepare('SELECT matiere FROM users_prof WHERE email = :email');
            $requeteProf->execute(['email' => $email]);
            $row = $requeteProf->fetch();
            $matiere = $row['matiere'];

            
            $requete = $bdd->prepare('INSERT INTO examen (date_passation,note, intitule, appreciation, matiere, eleve_id) VALUES (?,?, ?, ?, ?, ?)');
            if ($requete->execute([$date,$note, $intitule, $appreciation, $matiere, $eleve_id])) {
                echo "Enregistrement en base de données réussi pour l'élève $eleve_id.<br>";
            } else {
                echo "Erreur lors de l'enregistrement en base de données pour l'élève $eleve_id.<br>";
            }
        }
    }
}  


?>
<div class="container">
    <?php
    foreach ($nom_classes as $nom_classe) {
        echo '<div class="class-section"  style="background-color: #ECF8F6 ;
        " data-classe="' . $nom_classe . '">' . $nom_classe . '</div>';
        
    }
    ?>
</div>

<div class="container">
    <?php
    foreach ($nom_classes as $nom_classe) {
       
      

        echo '<div class="exam-folder" id="exams_' . $nom_classe . '" style="display: none;">'; 
        echo '<button type="button" class="exam-header btn btn-bn btn-danger" data-table="exams_' . $nom_classe . '">Créer</button>';
        $AllExamen = $bdd->prepare('SELECT DISTINCT examen.intitule AS examen_intitule
    FROM examen
    INNER JOIN users_eleve ON users_eleve.eleve_id = examen.eleve_id  
    INNER JOIN classe ON users_eleve.classe_id = classe.classe_id 
    WHERE classe.nom_classe = :nom_classe');

$AllExamen->bindParam(':nom_classe', $nom_classe, PDO::PARAM_STR);
$AllExamen->execute();

$intitules = [];
while ($row = $AllExamen->fetch()) {
    if (!in_array($row['examen_intitule'], $intitules)) {
        $intitules[] = $row['examen_intitule'];
    }
}

foreach ($intitules as $intitule) {
    echo "<p class='show-table'  style='background-color: #ECF8F6 ;
    ' data-intitule='".$intitule."' id='intitule_".$intitule."' data-table='exams_$nom_classe'>$intitule</p><br>";

    $table = $bdd->prepare('SELECT * FROM examen
                JOIN users_eleve ON examen.eleve_id = users_eleve.eleve_id
                WHERE intitule = :intitule');
    $table->execute(array('intitule' => $intitule));

    $moyennes = []; 
    echo '<form method="post" action="index.php">';
    echo '<table class="hidden-table" style="background-color: #ECF8F6 ;
    margin:0" data-intitule="' . $intitule . '">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Nom</th>';
    echo '<th>Prénom</th>';
    echo '<th>Intitulé</th>';
    echo '<th>Note</th>';
    echo '<th>Appréciation</th>';
    echo '<th>Chemin du Fichier</th>';
    echo '<th>Modifier</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    while ($rew = $table->fetch()) {
        echo '<tr>';
        echo '<td>' . $rew['nom'] . '</td>';
        echo '<td>' . $rew['prenom'] . '</td>';
        echo '<td>' . $rew['intitule'] . '</td>';
        echo '<td><input name="mark_' . $rew['id_examen'] . '" placeholder="' . $rew['note'] . '"></td>';
        echo '<td><input name="appreciate_' . $rew['id_examen'] . '" placeholder="' . $rew['appreciation'] . '"></td>';
        echo '<td><button type="submit" name="modifier" value="' . $rew['id_examen'] . '">Modifier</button></td>';
        echo '</tr>';
        $moyennes[] = $rew['note']; 
    }
 
    echo '</tbody>';
    echo '<tfoot>';
    echo '<tr>';
    echo '<td colspan="7"><button type="submit" class="btn btn-primary">Envoyer</button></td>';
    echo '</tr>';
    echo '</tfoot>';
    echo '</table>';
    echo'</form>';

    // Calcul de la moyenne pour cet intitulé d'examen
    $somme = array_sum($moyennes);
    $nombreNotes = count($moyennes);
    $moyenne = $nombreNotes > 0 ? $somme / $nombreNotes : 0;

    echo "La moyenne pour '$intitule' est : $moyenne<br>";
    echo '</form>'; // Fermez le formulaire ici, à la fin de la boucle
}



        
        

        echo '<div class="table-display" id="exams_' . $nom_classe . '_table" style="display: none;">';

        echo'<input type="text" name="titre_examen" placeholder="Le titre de l\'examen" oninput="updateIntitules(this.value)">';
       echo '<table class="table table-striped table-sm"  style="background-color: #ECF8F6 ;
       margin:0" >';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Date</th>';
        echo '<th>Nom</th>';
        echo '<th>Prénom</th>';
        echo '<th>Intitulé</th>';
        echo '<th>Note</th>';
        echo '<th>Appréciation</th>';
        echo '<th>Chemin du Fichier</th>';
        echo '<th>Modifier</th>';
        echo '</tr>';
        echo '</thead>'; 
        echo '<form method="post" action="">';
        echo '<tbody>';

        $requete = $bdd->prepare('SELECT users_eleve.eleve_id, users_eleve.nom, users_eleve.prenom
        FROM users_eleve
        INNER JOIN classe ON users_eleve.classe_id = classe.classe_id
        WHERE classe.nom_classe = :nom_classe
        ORDER BY users_eleve.nom ASC');

        $requete->execute(['nom_classe' => $nom_classe]);

        while ($eleve = $requete->fetch()) {
            echo '<tr>';
            echo '<td><input type="date" class="form-control" id="date" name="date" value="' . date('Y-m-d') . '"></td>';
            echo '<td>' . $eleve['nom'] . '</td>';
            echo '<td>' . $eleve['prenom'] . '</td>';
            echo "<td><input type='text' name='intitule_" . $eleve['eleve_id'] . "' value='" . (isset($_POST['titre_examen']) ? $_POST['titre_examen'] : '') . "' placeholder='Intitulé'></td>";

            echo '<td><input type="number" name="note_' . $eleve['eleve_id'] . '" placeholder="Note"></td>';
            echo '<td><input type="text" name="appreciation_' . $eleve['eleve_id'] . '" placeholder="Appréciation"></td>';
            echo '<td><a href=""></a><input type="file" name="image"></td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '<tfoot>';
        echo '<tr>';
        echo '<td colspan="7"><button type="submit" class="btn btn-primary">Envoyer</button></td>';
        echo '</tr>';
        echo '</tfoot>';
    
        echo '</table>';
        echo '</form>';
       
        echo '</div>';
        echo '</div>';
    }

    ?>
</div>

<table class="hidden-table">
<thead>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Intitulé</th>
            <th>Note</th>
        </tr>
    </thead>
</table>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
 const classSections = document.querySelectorAll('.class-section');
classSections.forEach(section => {
    section.addEventListener('click', function() {
        const nom_classe = this.getAttribute('data-classe');
        toggleFolder('exams_' + nom_classe);
    }); 
});

const examHeaders = document.querySelectorAll('.exam-header');
examHeaders.forEach(header => {
    header.addEventListener('click', function() {
        const tableId = this.getAttribute('data-table');
        showTable(tableId);
    });
});

function toggleFolder(folderId) {
    const folder = document.getElementById(folderId);
    folder.style.display = folder.style.display === 'none' ? 'block' : 'none';
}

function showTable(tableId) {
    const table = document.getElementById(tableId + '_table');
    table.style.display = table.style.display === 'none' ? 'block' : 'none';
}
function updateIntitules(value) {
    var intitules = document.querySelectorAll('input[name^="intitule_"]');
    intitules.forEach(function(intitule) {
        intitule.value = value;
    });
}
const paragraphs = document.querySelectorAll('p.show-table');

paragraphs.forEach(paragraph => {
    paragraph.addEventListener('click', function() {
        const intitule = this.getAttribute('data-intitule');
        const tableToShow = document.querySelector('.hidden-table[data-intitule="' + intitule + '"]');

        if (tableToShow) {
            tableToShow.style.display = tableToShow.style.display === 'none' ? 'block' : 'none';
        }
    });
});
</script>
</body>
</html>