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

        // Vérification du téléchargement du nouveau fichier
        if (isset($_FILES['newimage']) && $_FILES['newimage']['error'] === 0) {
            $error = 1;
            $extensionsArray = ["jpeg", "jpg", "png", "pdf"];
            $informationImage = pathinfo($_FILES['newimage']['name']);
            $extensionImage = strtolower($informationImage['extension']);
            $adress = 'upload/' . time() . rand() . rand() . '.' . $extensionImage;

            if (in_array($extensionImage, $extensionsArray) && $_FILES['newimage']['size'] <= 3000000) {
                if (move_uploaded_file($_FILES['newimage']['tmp_name'], $adress)) {
                    $error = 0;
                    echo 'Fichier copié avec succès.';

                    // Mise à jour de la base de données
                    $modifier = $bdd->prepare('UPDATE eleve SET firstname = :prenom, lastname = :nom, note = :note, nomfichier = :nomfichier, chemin_fichier = :chemin_fichier WHERE id = :id');
                    $modifier->execute(array(
                        'id' => $idModifier,
                        'prenom' => $newprenom,
                        'nom' => $newnom,
                        'note' => $newnote,
                        'nomfichier' => $informationImage['filename'],
                        'chemin_fichier' => $adress
                    ));

                    echo "Modification réussie.";
                } else {
                    echo "Erreur lors de la copie du fichier.";
                }
            } else {
                echo "Veuillez télécharger un fichier avec une extension valide (jpeg, jpg, png, pdf) et une taille maximale de 3 Mo.";
            }
        } else {
            // Si aucun nouveau fichier n'est téléchargé, mettre à jour la base de données sans changer le fichier
            $modifier = $bdd->prepare('UPDATE eleve SET firstname = :prenom, lastname = :nom, note = :note WHERE id = :id');
            $modifier->execute(array(
                'id' => $idModifier,
                'prenom' => $newprenom,
                'nom' => $newnom,
                'note' => $newnote
            ));

            echo "Modification réussie.";
        }
    } elseif (isset($_POST['modifier'])) {
        // Formulaire de modification
        $idModifier = $_POST['modifier'];
        echo '<form method="post" action="" enctype="multipart/form-data">
            <input type="text" name="newprenom" placeholder="Nouveau prénom">
            <input type="text" name="newnom" placeholder="Nouveau nom">
            <input type="number" name="newnote" placeholder="Nouvelle note">
            <input type="hidden" name="modifier" value="' . $idModifier . '">
            <input type="file" name="newimage"/>
            <input type="submit" class="form-control">
        </form>';
    }
}
?>
