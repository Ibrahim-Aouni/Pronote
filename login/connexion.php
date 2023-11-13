<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['valeurs'])) {
    $_SESSION['valeurs'] = array();
}

try {
    $bdd = new PDO('mysql:host=localhost;dbname=pronote;charset=utf8', 'root', '');
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

if (isset($_POST['identifiant']) && isset($_POST['email']) && isset($_POST['mdp']) && isset($_POST['matiere'])) {
    $name = $_POST['identifiant'];
    $name=strip_tags($name);
    $name=htmlspecialchars($name);
    $_SESSION['name'] = $name; 
    $email = $_POST['email'];
    $email= strip_tags($email);
    $email= htmlspecialchars($email);
    $password = $_POST['mdp'];
    $password= password_hash($_POST['mdp'], PASSWORD_DEFAULT);
    $password=strip_tags($password);
    $password=htmlspecialchars($password);
    $matiere= $_POST['matiere'];
    $matiere=strip_tags($matiere);
    $matiere = htmlspecialchars($matiere);
    $_SESSION['matiere'] = $matiere; 

    
  if (preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
    
    } else {
        echo "Adresse email invalide";
     
    }

    
  if (preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/", $password)) {
        
    } else {
        echo "Mot de passe invalide. Il doit contenir au moins 8 caractères, dont au moins une lettre et un chiffre.";
        
    }

    $requete = $bdd->prepare('INSERT INTO users_prof (nom, email, mdp, matiere) VALUES (:identifiant, :email, :mdp, :matiere)');
    $requete->execute(array('identifiant' => $name, 'email' => $email, 'mdp' => $password, 'matiere' => $matiere));
    echo "Enregistrement inséré avec succès dans la base de données.";
    header('Location: signin.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./connexion.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <title>Document</title>
</head>
<body>
    

    <div class="container">
	<div class="form-container">
		<div class="custom-card active">
			<div class="custom-card-header">
				<h1 class="text-bold">Sign Up</h1>
			</div>
			<div class="custom-card-body">
				<ul class="nav">
					<li class="nav-item circle">
						<a class="nav-link" href="#" target="_blank" rel="noopener noreferrer">
                                <span class="fab fa-facebook-f"></span>
                        </a>
					</li>
					<li class="nav-item circle">
						<a class="nav-link" href="#" target="_blank" rel="noopener noreferrer">
                                <span class="fab fa-twitter"></span>
                            </a>
					</li>
					<li class="nav-item circle">
						<a class="nav-link" href="#" target="_blank" rel="noopener noreferrer">
                                <span class="fab fa-google"></span>
                            </a>
					</li>
				</ul>
				
                 <form action="" method="post" class="form">
                <div class="input-group">
                     <input type="text" placeholder="Identifiant" name="identifiant" class="form__input" id="name" />
                     <label for="name" class="form__label">Name</label>
                </div>
                <div class="input-group">
                     <input type="email" placeholder="Email" name="email" class="form__input" id="email" />
                     <label for="email" class="form__label">Email</label>
                </div> 
                <div class="input-group">
                    
                    <input type="password" placeholder="Mot de passe " name="mdp" class="form__input" id="subject" />
                    <label for="subject" class="form__label">PAssword</label>
                </div>
                <div class="input-group">
                    
                    <input type="texts" placeholder="Matiere " name="matiere" class="form__input" id="matiere" />
                    <label for="subject" class="form__label">Matière</label>
                </div>
       
       
        <button type="submit" class="btn btn-primary mt-2">Envoyer</button>
    </form> 
				
			</div>
		</div>
		<div class="custom-card">
			<div class="custom-card-header">
				<h1 class="text-bold">Hello, Friend!</h1>
			</div>
			<div class="custom-card-body">
				<h2 class="text-center text-cta">Start your journey with us today, enter your details to start.</h2>
				<a class="btn btn-outline-white mt-2" href="./signin.php">Sign in</a>
			</div>
		</div>
	</div>
</div>
</div>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>

</body>
</html>
