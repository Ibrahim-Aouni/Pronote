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

    if ($error === 0) {
        $nomFichier = $_FILES['fichier']['name'];
        $typeFichier = $_FILES['fichier']['type'];
        $donneesFichier = file_get_contents($_FILES['fichier']['tmp_name']);
            var_dump($nomFichier);
var_dump($typeFichier);
var_dump(strlen($donneesFichier));
                try {
                // ...
                $requete = $bdd->prepare('INSERT INTO eleve (nomfichier, typefichier, donnefichier) VALUES (?, ?, ?)');
                if ($requete->execute([$nomFichier['filename'], $typeFichier, $donneesFichier])) {
                    echo "Fichier importé avec succès dans la base de données.";
                } else {
                    echo "Erreur lors de l'import du fichier.";
                    var_dump($requete->errorInfo()); // Affiche les informations sur l'erreur SQL
                }
            } catch (PDOException $e) {
                echo "Erreur de base de données : " . $e->getMessage();
            }
        }
    }


?>
