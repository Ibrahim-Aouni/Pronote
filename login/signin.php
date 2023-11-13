<?php

try {
    $bdd = new PDO('mysql:host=localhost;dbname=pronote;charset=utf8', 'root', '');
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $email = strip_tags($email);
    $email = htmlspecialchars($email);
    $password = $_POST['password'];
   

    if (preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
        $requete = $bdd->prepare('SELECT email, mdp FROM users_prof WHERE email = :email');
        $requete->execute(array('email' => $email));
        $resultat = $requete->fetch();

        if ($resultat) {
           echo $password;
            if (password_verify($password,$resultat['mdp'])) {
                header('Location: ../index.php');
                exit;
            } else {
                echo "Mot de passe incorrect.";
            }
        } else {
            echo "Email non trouvé.";
        }
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
    <link rel="stylesheet" href="./connexion.css">
    <title>Document</title>
</head>
<body>
<div class="container">
	<div class="form-container">
		<div class="custom-card active">
			<div class="custom-card-header">
				<h1 class="text-bold">Sign In</h1>
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
                     <input type="email" placeholder="Email" name="email" class="form__input" id="email" />
                     <label for="email" class="form__label">Email</label>
                </div> <div class="input-group">
                    
                    <input type="password" placeholder="Mot de passe " name="password" class="form__input" id="subject" />
                    <label for="subject" class="form__label">Password</label>
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
				<a class="btn btn-outline-white mt-2" href="connexion.php">Sign up</a>
			</div>
		</div>
	</div>
</div>
</div>
    
</body>
</html>