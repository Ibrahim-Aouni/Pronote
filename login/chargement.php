<?php 


error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Pronote Website</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #55a79a; /* Turquoise background color */
            color: #ffffff; /* White text color */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        h1 {
            font-size: 3em;
            margin-bottom: 20px;
        }

        .compartments {
            display: flex;
            justify-content: space-around;
            width: 80%;
            max-width: 1200px;
            margin-top: 20px;
        }

        .compartment {
            text-align: center;
            padding: 20px;
            background-color: #3498db; /* Blue background color */
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease; /* Smooth transition for hover effect */
        }

        .compartment:hover {
            background-color: #2980b9; /* Darker blue on hover */
        }

        .compartment h2 {
            font-size: 1.5em;
            margin-bottom: 10px;
        }

        .compartment img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Pronote</h1>

    <div class="compartments">
        <div class="compartment">
            <h2>Professeur</h2>
            <a href="signin.php"> 
                <img src="https://cdn-icons-png.flaticon.com/128/3429/3429433.png"  href="./" alt="Compartment 1 Image">
            </a>
           
        </div>

        <div class="compartment">
            <h2>Eleve</h2>
            <a href="signup_eleve.php">
                <img src="https://cdn-icons-png.flaticon.com/128/10156/10156019.png" alt="Compartment 2 Image">
            </a>
            
        </div>
    </div>
    
</body>

</html>
