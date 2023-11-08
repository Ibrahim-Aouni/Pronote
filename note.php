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


if (isset($_POST['valeur']) && isset($_POST['nom']) && isset($_POST['prenom']) & isset($_FILES['fichier'])) {
    $nouveauNom = $_POST['nom'];
    $nouveauNom =strip_tags($nouveauNom);
    $nouveauNom=htmlspecialchars($nouveauNom);
    $nouveauPrenom = $_POST['prenom'];
    $nouveauPrenom= strip_tags($nouveauPrenom);
    $nouveauPrenom=htmlspecialchars($nouveauPrenom);
    $nouvelleValeur = $_POST['valeur'];
    $nouvelleValeur=strip_tags($nouvelleValeur);
    $nouvelleValeur= htmlspecialchars($nouvelleValeur);
    $ancienPrenom= $nouveauPrenom;

    


    $verificationIdentite = $bdd->prepare('SELECT COUNT(*) as count FROM eleve WHERE firstname = :prenom AND lastname = :nom');
    $verificationIdentite->execute(array('prenom' => $nouveauPrenom, 'nom' => $nouveauNom));
    $verificationIdentite = $verificationIdentite->fetch();


    if( $verificationIdentite['count']> 0){
        echo"Cet élève existe déjà";

        
    } elseif ($nouvelleValeur >= 0 && $nouvelleValeur <= 20 ) {
        $_SESSION['valeurs'][] = array('prenom' => $nouveauPrenom, 'nom' => $nouveauNom, 'note' => $nouvelleValeur);
        
        $requete = $bdd->prepare('INSERT INTO eleve (firstname, lastname, note) VALUES (?, ?, ?)');
    if ($requete->execute([$nouveauPrenom, $nouveauNom, $nouvelleValeur])) {
        echo "Fichier importé avec succès dans la base de données.";
    } else {
        echo "Erreur lors de l'import du fichier.";
    }
        echo "Enregistrement inséré avec succès dans la base de données.";
    } else {
        echo "Veuillez entrer une valeur entre 0 et 20";
    
}
}

if (isset($_POST['valeur']) && isset($_POST['nom']) && isset($_POST['prenom'])) {
    $nouveauNom = $_POST['nom'];
    $nouveauNom =strip_tags($nouveauNom);
    $nouveauNom=htmlspecialchars($nouveauNom);
    $nouveauPrenom = $_POST['prenom'];
    $nouveauPrenom= strip_tags($nouveauPrenom);
    $nouveauPrenom=htmlspecialchars($nouveauPrenom);
    $nouvelleValeur = $_POST['valeur'];
    $nouvelleValeur=strip_tags($nouvelleValeur);
    $nouvelleValeur= htmlspecialchars($nouvelleValeur);

    $doublonExiste = false;
    foreach ($_SESSION['valeurs'] as $valeur) {
        if ($valeur['prenom'] == $nouveauPrenom && $valeur['nom'] == $nouveauNom) {
            $doublonExiste = true;
            break;
        }
    }

    if ($doublonExiste) {
        echo "Cet élève existe déjà dans la session.";
    } elseif ($nouvelleValeur == -1) {
        echo "Les valeurs sont : " . implode(', ', $_SESSION['valeurs']);
    } elseif ($nouvelleValeur >= 0 && $nouvelleValeur <= 20) {
        $_SESSION['valeurs'][] = array('prenom' => $nouveauPrenom, 'nom' => $nouveauNom, 'note' => $nouvelleValeur);
        echo "Enregistrement en session réussi.";
    } else {
        echo "Veuillez entrer une valeur entre 0 et 20";
    }
}
?>