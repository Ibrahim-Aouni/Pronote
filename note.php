<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
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

                    // Vérification si le nom et le prénom existent déjà dans la base de données
                    $requeteVerif = $bdd->prepare('SELECT COUNT(*) AS count FROM eleve WHERE firstname = ? AND lastname = ?');
                    $requeteVerif->execute([$nouveauPrenom, $nouveauNom]);
                    $resultatVerif = $requeteVerif->fetch();

                    if ($resultatVerif['count'] > 0) {
                        echo "Cet élève existe déjà dans la base de données. Veuillez entrer un élève différent.";
                    } elseif ($nouvelleValeur >= 0 && $nouvelleValeur <= 20) {
                        $requete = $bdd->prepare('INSERT INTO eleve (firstname, lastname, note, nomfichier, chemin_fichier) VALUES (?, ?, ?, ?, ?)');
                        if ($requete->execute([$nouveauNom, $nouveauPrenom, $nouvelleValeur, $informationImage['filename'], $adress])) {
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
