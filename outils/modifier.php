<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('note.php');
try {
    $bdd = new PDO('mysql:host=localhost;dbname=pronote;charset=utf8', 'root', '');
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}


function getStudentData($idModifier) {
    global $bdd;

    $requete = $bdd->prepare('SELECT firstname, lastname, note, nomfichier, chemin_fichier FROM eleve WHERE id = :id');
    $requete->bindParam(':id', $idModifier, PDO::PARAM_INT);
    $requete->execute();

    return $requete->fetch(PDO::FETCH_ASSOC);
}

function updateStudent($idModifier, $newprenom, $newnom, $newnote, $extensionImage, $extensionsArray) {
    global $bdd;

    

    if (in_array($extensionImage, $extensionsArray) && $_FILES['image']['size'] <= 3000000) {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $adress)) {
            $error = 0;
            echo 'Fichier copié avec succès.';

            
    $adress = ''; 

    $modifier = $bdd->prepare('UPDATE eleve SET firstname = :prenom, lastname = :nom, note = :note, nomfichier = :nomfichier, chemin_fichier = :chemin_fichier WHERE id = :id');
    $modifier->execute(array(
        'id' => $idModifier,
        'prenom' => $newprenom,
        'nom' => $newnom,
        'note' => $newnote,
        'nomfichier' => $informationImage['basename'], 
        'chemin_fichier' => $adress 
    ));

    return ($modifier->errorCode() === '00000');
}
}
}

$idModifier = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['modif'])) {
        $idModifier = $_POST['modifier'];
        $studentData = getStudentData($idModifier);

        echo '<form method="post" action="" enctype="multipart/form-data">
            <label for="newprenom">Nouveau prénom</label>
            <input type="text" id="newprenom" name="newprenom" placeholder="Nouveau prénom" value="'.htmlspecialchars($studentData['firstname']).'">
                    
            <label for="newnom">Nouveau nom</label>
            <input type="text" id="newnom" name="newnom" placeholder="Nouveau nom" value="'.htmlspecialchars($studentData['lastname']).'">
                    
            <label for="newnote">Nouvelle note</label>
            <input type="number" id="newnote" name="newnote" placeholder="Nouvelle note" value="'.htmlspecialchars($studentData['note']).'">
                    
            <input type="hidden" name="modifier" value="' . $idModifier . '">
                    
            <label for="currentimage">Image actuelle</label>
            <a href="' . $studentData['chemin_fichier'] . '"  target="_blank"/>' . $studentData['nomfichier'] . '</a>                   
            <label for="newimage">Nouvelle image</label>
            <input type="file" id="newimage" name="newimage"/>
                    
            <input type="submit" class="form-control">
        </form>';
    } elseif (isset($_POST['newprenom']) && isset($_POST['newnom']) && isset($_POST['newnote']) && isset($_FILES['newimage']) && isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $idModifier = $_POST['modifier'];
        $newprenom = strip_tags(htmlspecialchars($_POST['newprenom']));
        $newnom = strip_tags(htmlspecialchars($_POST['newnom']));
        $newnote = strip_tags(htmlspecialchars($_POST['newnote']));
     
        $error = 1;
        $extensionsArray = ["jpeg", "jpg", "png", "pdf"];
        $informationImage = pathinfo($_FILES['image']['name']);
        $extensionImage = strtolower($informationImage['extension']);
        $adress = 'upload/' . time() . rand() . rand() . '.' . $extensionImage;
        $success = updateStudent();

        if ($success) {
            echo "Modification réussie.";
        } else {
            echo "Erreur lors de la modification.";
        
        $success = updateStudent($idModifier, $newprenom, $newnom, $newnote, $extensionImage, $extensionsArray);

        if ($success) {
            echo "Modification réussie.";
        } else {
            echo "Erreur lors de la modification.";
        }
    }
    
        
}
}
?>
