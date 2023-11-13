<?php
session_start();
define('PAGE', 'index');

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $bdd = new PDO('mysql:host=localhost;dbname=pronote;charset=utf8', 'root', '');
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

if (isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['mail']) && isset($_POST['mot_de_passe'])) {
    $nom = htmlspecialchars($_POST['nom']);
    $_SESSION['nom'] = $nom;
    $mail = htmlspecialchars($_POST['mail']);
    $_SESSION['mail'] = $mail;
    $prenom = htmlspecialchars($_POST['prenom']);
    $_SESSION['prenom'] = $prenom;

    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);

    if (preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $mail)) {
        $requete = $bdd->prepare('INSERT INTO users_eleve(prenom, nom, email, mot_de_passe) VALUES(:prenom, :nom, :mail, :mot_de_passe)');
        $requete->execute(array('prenom' => $prenom, 'nom' => $nom, 'mail' => $mail, 'mot_de_passe' => $mot_de_passe));
        header('Location: signin_eleve.php');
        exit;
    } else {
        echo "Adresse email invalide";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de Connexion - Élève</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: -webkit-linear-gradient(left, #0072ff, #00c6ff);
        }

        .contact-form {
            background: #fff;
            margin-top: 10%;
            margin-bottom: 5%;
            width: 70%;
        }

        .contact-form .form-control {
            border-radius: 1rem;
        }

        .contact-image {
            text-align: center;
        }

        .contact-image img {
            border-radius: 6rem;
            width: 11%;
            margin-top: -3%;
        }

        .contact-form form {
            padding: 14%;
        }

        .contact-form form .row {
            margin-bottom: -7%;
        }

        .contact-form h3 {
            margin-bottom: 8%;
            margin-top: -10%;
            text-align: center;
            color: #0062cc;
        }

        .contact-form .btnContact {
            width: 50%;
            border: none;
            border-radius: 1rem;
            padding: 1.5%;
            background: #dc3545;
            font-weight: 600;
            color: #fff;
            cursor: pointer;
        }

        .btnContactSubmit {
            width: 50%;
            border-radius: 1rem;
            padding: 1.5%;
            color: #fff;
            background-color: #0062cc;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container contact-form">
    <div class="contact-image">
        <img src="https://cdn-icons-png.flaticon.com/128/10156/10156019.png" alt="rocket_contact"/>
    </div>
    <form method="post">
        <h3>Drop Us a Message</h3>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <input type="text" name="prenom" class="form-control" placeholder="Votre Prénom" value=""/>
                </div>
                <div class="form-group">
                    <input type="text" name="nom" class="form-control" placeholder="Votre nom" value=""/>
                </div>
                <div class="form-group">
                    <input type="email" name="mail" class="form-control" placeholder="Your Email *" value=""/>
                </div>
                <div class="form-group">
                    <input type="password" name="mot_de_passe" class="form-control" placeholder="Mot de passe" value=""/>
                </div>
                <div class="form-group">
                    <input type="number " name="age" class="form-control" placeholder="Age" value=""/>
                </div>
                <div class="form-group">
                    <input type="submit" class="btnContact" value="Send Message"/>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
