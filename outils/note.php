<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['name'])) {
    $name = $_SESSION['name'];
   
} else {
   echo'Vous n\'êtes pas connecté';
}

try {
    $bdd = new PDO('mysql:host=localhost;dbname=pronote;charset=utf8', 'root', '');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['valeur']) && isset($_POST['nom']) && isset($_POST['prenom'])) {
        $nouveauNom = strip_tags(htmlspecialchars($_POST['nom']));
        $nouveauPrenom = strip_tags(htmlspecialchars($_POST['prenom']));
        $nouvelleValeur = intval($_POST['valeur']);

        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $error = 1;
            $extensionsArray = ["jpeg", "jpg", "png", "pdf"];
            $informationImage = pathinfo($_FILES['image']['name']);
            $extensionImage = strtolower($informationImage['extension']);
            $adress = 'upload/' . time() . rand() . rand() . '.' . $extensionImage;

            if (in_array($extensionImage, $extensionsArray) && $_FILES['image']['size'] <= 3000000) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $adress)) {
                    $error = 0;
                    echo 'Fichier copié avec succès.';

                    
                    $requeteVerif = $bdd->prepare('SELECT COUNT(*) AS count FROM eleve WHERE firstname = ? AND lastname = ?');
                    $requeteVerif->execute([$nouveauPrenom, $nouveauNom]);
                    $resultatVerif = $requeteVerif->fetch();

                    if ($resultatVerif['count'] > 0) {
                        echo "Cet élève existe déjà dans la base de données. Veuillez entrer un élève différent.";
                    } elseif ($nouvelleValeur >= 0 && $nouvelleValeur <= 20) {
                        echo $name;
                        $comparer = $bdd->prepare('SELECT prof_id FROM users_prof where  nom = :nom');
                        $comparer -> execute(array('nom' => $name));
                        $row = $comparer->fetch();
                        $prof_id=$row['prof_id']; 
                        echo $prof_id;
                        $requete = $bdd->prepare('INSERT INTO eleve (eleve.firstname, eleve.lastname, eleve.note, eleve.nomfichier, eleve.chemin_fichier ,eleve.prof_id) VALUES (?, ?, ?, ?, ?,?) ');
                        if ($requete->execute([$nouveauNom, $nouveauPrenom, $nouvelleValeur, $informationImage['filename'], $adress, $prof_id])) {
                            echo "Enregistrement en base de données réussi.";
                        } else {
                            echo "Erreur lors de l'enregistrement en base de données.";
                        }
                    } else {
                        echo "Veuillez entrer une valeur entre 0 et 20";
                    }
                } else {
                    echo "Erreur lors de la copie du fichier.";
                }
            } else {
                echo "Veuillez télécharger un fichier avec une extension valide (jpeg, jpg, png, pdf) et une taille maximale de 3 Mo.";
            }
        }
    }
}
?>
