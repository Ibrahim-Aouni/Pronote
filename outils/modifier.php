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

$idModifier = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['newprenom']) && isset($_POST['newnom']) && isset($_POST['newnote'])) {
        $idModifier = $_POST['modifier'];

        $newprenom = strip_tags(htmlspecialchars($_POST['newprenom']));
        $newnom = strip_tags(htmlspecialchars($_POST['newnom']));
        $newnote = strip_tags(htmlspecialchars($_POST['newnote']));

        if (isset($_FILES['newimage']) && $_FILES['newimage']['error'] === 0) {
            $error = 1;
            $extensionsArray = ["jpeg", "jpg", "png", "pdf"];
            $informationImage = pathinfo($_FILES['newimage']['name']);
            $extensionImage = strtolower($informationImage['extension']);
            $adress = 'upload/' . uniqid() . '.' . $extensionImage;

            if (in_array($extensionImage, $extensionsArray) && $_FILES['newimage']['size'] <= 3000000) {
                if (move_uploaded_file($_FILES['newimage']['tmp_name'], $adress)) {
                    $error = 0;
                    echo 'Fichier copié avec succès.';

                    $query = 'UPDATE eleve SET firstname = :prenom, lastname = :nom, note = :note, nomfichier = :nomfichier, chemin_fichier = :chemin_fichier WHERE id = :id';
                    echo "SQL Query: $query"; // Print SQL query for debugging

                    $modifier = $bdd->prepare($query);
                    $modifier->execute(array(
                        'id' => $idModifier,
                        'prenom' => $newprenom,
                        'nom' => $newnom,
                        'note' => $newnote,
                        'nomfichier' => $informationImage['basename'],
                        'chemin_fichier' => $adress
                    ));

                    if ($modifier->errorCode() !== '00000') {
                        $errorInfo = $modifier->errorInfo();
                        echo "Update failed: " . $errorInfo[2];
                    } else {
                        echo "Modification réussie.";
                        header("Location: success.php"); // Redirect to a success page
                        exit();
                    }
                } else {
                    echo "Erreur lors de la copie du fichier.";
                }
            } else {
                echo "Veuillez télécharger un fichier avec une extension valide (jpeg, jpg, png, pdf) et une taille maximale de 3 Mo.";
            }
        }
    } elseif (isset($_POST['modifier'])) {
        $idModifier = $_POST['modifier'];

        $query = 'SELECT firstname, lastname, note, nomfichier, chemin_fichier FROM eleve WHERE prof_id = :id';
        echo "SQL Query: $query";

        $requete = $bdd->prepare($query);
        $requete->bindParam(':id', $idModifier, PDO::PARAM_INT);
        $requete->execute();

        while ($row = $requete->fetch()) {
            if (isset($_POST['modifier'])) {
                $idModifier = $_POST['modifier'];
                echo '<form method="post" action="" enctype="multipart/form-data">
                    <label for="newprenom">Nouveau prénom</label>
                    <input type="text" id="newprenom" name="newprenom" placeholder="Nouveau prénom" value="'.htmlspecialchars($row['firstname']).'">
                    
                    <label for="newnom">Nouveau nom</label>
                    <input type="text" id="newnom" name="newnom" placeholder="Nouveau nom" value="'.htmlspecialchars($row['lastname']).'">
                    
                    <label for="newnote">Nouvelle note</label>
                    <input type="number" id="newnote" name="newnote" placeholder="Nouvelle note" value="'.htmlspecialchars($row['note']).'">
                    
                    <input type="hidden" name="modifier" value="' . $idModifier . '">
                    
                    <label for="currentimage">Image actuelle</label>
                    <a href="' . $row['chemin_fichier'] . '"  target="_blank"/>' . $row['nomfichier'] . '</a>                   
                    <label for="newimage">Nouvelle image</label>
                    <input type="file" id="newimage" name="newimage"/>
                    
                    <input type="submit" class="form-control">
                </form>';
            }
        }
    }
}
?>
