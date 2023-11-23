<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Vérifie si la session est déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
} else {
    echo 'Vous n\'êtes pas connecté';
    exit();
}

try {
    $bdd = new PDO('mysql:host=localhost;dbname=pronote;charset=utf8', 'root', '');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['note'], $_POST['intitule'], $_POST['appreciation'])) {
        $appreciation = htmlspecialchars($_POST['appreciation'], ENT_QUOTES);
        $intitule = htmlspecialchars($_POST['intitule'], ENT_QUOTES);
        $note = intval($_POST['note']);

        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $extensionsArray = ["jpeg", "jpg", "png", "pdf"];
            $informationImage = pathinfo($_FILES['image']['name']);
            $extensionImage = strtolower($informationImage['extension']);
            $adress = 'upload/' . time() . rand() . rand() . '.' . $extensionImage;

            if (in_array($extensionImage, $extensionsArray) && $_FILES['image']['size'] <= 3000000) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $adress)) {
                    echo 'Fichier copié avec succès.';

                    $requeteProf = $bdd->prepare('SELECT matiere FROM users_prof WHERE email = :email');
                    $requeteProf->execute(['email' => $email]);
                    $row = $requeteProf->fetch();
                    $matiere = $row['matiere'];

                    $requete = $bdd->prepare('INSERT INTO examen (note, intitule, appreciation) VALUES (?, ?, ?)');
                    if ($requete->execute([$note, $intitule, $appreciation])) {
                        echo "Enregistrement en base de données réussi.";
                    } else {
                        echo "Erreur lors de l'enregistrement en base de données.";
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
