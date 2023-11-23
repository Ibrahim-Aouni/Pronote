<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$matiere = ''; 

if (isset($_SESSION['matiere'])) {
    $matiere = $_SESSION['matiere'];
} else {
    echo 'Vous n\'êtes pas connecté';
}
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
  } else {
   echo'Vous n\'êtes pas connecté';
  }
try {
    
    $bdd = new PDO('mysql:host=localhost;dbname=pronote;charset=utf8', 'root', '');
    } catch (Exception $e) {
        die('Erreur de connexion à la base de données : ' . $e->getMessage());
    }


include('./navbar.php');

$requete = $bdd->prepare('SELECT * FROM users_prof WHERE email= :emam');
$requete->execute(array('emam' => $email));
$row = $requete->fetch();
$nom= $row['nom'];
   

function getNomClasses($bdd, $email) {
    $requete = $bdd->prepare('SELECT * FROM classe JOIN users_prof ON classe.prof_anglais = users_prof.prof_id WHERE users_prof.email = :email');
    $requete->execute(['email' => $email]);
    
    $nom_classes = [];
    while ($row = $requete->fetch()) {
        $nom_classes[] = $row['nom_classe'];
    }
    
    return $nom_classes;
}
function getIntitulesExamens($bdd, $nom_classe) {
    $AllExamen = $bdd->prepare('SELECT DISTINCT examen.intitule AS examen_intitule FROM examen INNER JOIN users_eleve ON users_eleve.eleve_id = examen.eleve_id INNER JOIN classe ON users_eleve.classe_id = classe.classe_id WHERE classe.nom_classe = :nom_classe');

    $AllExamen->bindParam(':nom_classe', $nom_classe, PDO::PARAM_STR);
    $AllExamen->execute();

    $intitules = [];
    while ($row = $AllExamen->fetch()) {
        $intitules[] = $row['examen_intitule'];
    }
    
    return $intitules;
}
function getClassesAndAverages($bdd, $email){
    $nom_classes = getNomClasses($bdd, $email);

    $moyennes_classes = [];
    $moyennes = [];

    foreach ($nom_classes as $nom_classe) {
        $intitules = getIntitulesExamens($bdd, $nom_classe);

        $moyennesIntitules = [];

        foreach ($intitules as $intitule) {
            $table = $bdd->prepare('SELECT note FROM examen WHERE intitule = :intitule');
            $table->execute(array('intitule' => $intitule));
        
            $moyennes_intitule = [];

            while ($row = $table->fetch()) {
                $moyennes_intitule[] = $row['note'];
            }

            if (count($moyennes_intitule) > 0) {
                $moyenne_intitule = array_sum($moyennes_intitule) / count($moyennes_intitule);
            } else {
                $moyenne_intitule = 0; // Or set a default value
            }

            // Store the average for this intitule
            $moyennesIntitules[$intitule] = $moyenne_intitule;
        }

        // Calculate the average for the class
        if (count($moyennesIntitules) > 0) {
            $moyenne_classe = array_sum($moyennesIntitules) / count($moyennesIntitules);
        } else {
            $moyenne_classe = 0; // Or set a default value
        }

        // Store the class average
        $moyennes_classes[$nom_classe] = $moyenne_classe;

        // Store the averages for each intitule
        $moyennes[$nom_classe] = $moyennesIntitules;
    }

    return [$moyennes_classes, $moyennes];
}

// Utilisation de la fonction
[$moyennes_classes, $moyennes] = getClassesAndAverages($bdd, $email);

$classes= array_keys($moyennes);

$nom_classes = getNomClasses($bdd, $email);

$moyennes_par_classe= getClassesAndAverages($bdd, $email);

// Conversion en JSON pour utilisation en JavaScript
$moyennes_json = json_encode($moyennes_par_classe);

$classe =json_encode($nom_classes);


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagramme à Barres Bootstrap Petit</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

    <style>
         body{
        background-color: #18534F ;
                color: #18534F;

      }
        canvas{
            background-color: #ECF8F6 ;
            fill:white;
            color:white;
            stroke: white;
        }
        .card-body{
            background-color: #ECF8F6 ;
        }
        .custom-rectangle {
            height: 200px;
            border-radius: 15px;
            margin-bottom: 20px;
            background-color: #ECF8F6 ;
            position: relative;
           
            text-align: center;
      
        }
        .right-rectangle {
            height: 100vh;
            border-radius: 15px;
            background-color: #f0f0f0;
            position: relative;
            overflow: hidden;
        }
        .rectangle-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            transition: all 0.3s ease-in-out;
        }
        .rectangle-content img {
            max-width: 100px;
            height: auto;
            margin-bottom: 20px;
        }
        
        /* Animation */
        
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Rectangle sur le côté gauche -->
        <div class="col-lg-4">
            <div class="custom-rectangle" style="height: 600px;">
                <img src="https://cdn-icons-png.flaticon.com/128/10156/10156012.png" alt="Icon">
                <p style="color:#FEEAA1 ;"><?php echo $nom ?></p>
                <br>
                <!-- Trois rectangles de couleur fluo -->
                <div class="colored-rectangles">
                <div class="col-lg-12" style="background-color: #ffdd19; width: 100%; height: 100px; border-radius: 15px; display: flex; align-items: center; justify-content: space-between; padding: 0 20px;">
                <div style="display: flex; align-items: center;">
                    <div style="width: 50px; height: 50px; border-radius: 50%; background-color: #226D68; color: #fff; display: flex; justify-content: center; align-items: center; font-weight: bold; font-size: 20px;">JE</div>
                    <h3 style="margin-left: 10px;">Nom de l'élève</h3>
                    </div>
                    <img src="https://cdn-icons-png.flaticon.com/128/2583/2583344.png" alt="Médaille" style="width: 30px; height: 30px;">
                </div>
                <div class="col-lg-12" style="background-color: #c0C0C0; width: 100%; height: 100px; border-radius: 15px; display: flex; align-items: center; justify-content: space-between; padding: 0 20px;">
                <div style="display: flex; align-items: center;">
                    <div style="width: 50px; height: 50px; border-radius: 50%; background-color: #226D68; color: #fff; display: flex; justify-content: center; align-items: center; font-weight: bold; font-size: 20px;">JE</div>
                    <h3 style="margin-left: 10px;">Nom de l'élève</h3>
                    </div>
                    <img src="https://cdn-icons-png.flaticon.com/128/2583/2583319.png" alt="Médaille" style="width: 30px; height: 30px;">
                </div>
                <div class="col-lg-12" style="background-color: #dfb891; width: 100%; height: 100px; border-radius: 15px; display: flex; align-items: center; justify-content: space-between; padding: 0 20px;">
                <div style="display: flex; align-items: center;">
                    <div style="width: 50px; height: 50px; border-radius: 50%; background-color: #226D68; color: #fff; display: flex; justify-content: center; align-items: center; font-weight: bold; font-size: 20px;">JE</div>
                    <h3 style="margin-left: 10px;">Nom de l'élève</h3>
                    </div>
                    <img src="https://www.flaticon.com/free-icon/medal_2583434" alt="Médaille" style="width: 30px; height: 30px;">
                </div>
                    <div class="col-lg-12" style="background-color: #FFFF00;height: 100px; border-radius: 15px;"></div>
                </div>
            </div>
        </div>
        
        <!-- Rectangles à droite -->
        <div class="col-lg-8">
            <div class="row">
                <!-- Premier rectangle en haut -->
                <div class="col-lg-12 h-50">
                    <div class="custom-rectangle" style="height: 200px;">
                        <div class="rectangle-content">
                            <h3 style="opacity: 10;">Bienvenue <?php echo $nom ?> &#128075;</h3>
                            <p style="opacity: 10;">
                                <?php echo $nom ?><br>
                                "Votre passion et votre dévouement nourrissent les esprits, façonnent l'avenir et inspirent chaque jour. Votre engagement transforme des vies et ouvre des portes vers un avenir brillant. Continuez à semer les graines du savoir, vous faites une différence incroyable dans le monde."
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Deux rectangles en dessous -->
                <div class="col-lg-6 h-50" style=" padding-bottom: 100px">
                    <div class="custom-rectangle" style="height: 200px; padding-bottom: 100px">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Moyenne globale de la classe</h6>
                                <canvas id="myChart" style="max-height: 300px; width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 h-50">
                    <div class="custom-rectangle" style="height: 300px;">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Evolution de la classe en fonction de la moyenne</h6>
                                <canvas id="myChart2" style="max-height: 300px; width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Trois rectangles en dessous -->
            <div class="row">
                <div class="col-lg-4">
                    <div class="custom-rectangle">
                        <?php 
                        foreach ($nom_classes as $nom_classe) {
                            echo '<div class="class-section" data-classe="' . $nom_classe . '">' . $nom_classe . '</div>';
                        }
                        ?>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="custom-rectangle"></div>
                </div>
                <div class="col-lg-4">
                    <div class="custom-rectangle"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container mt-5">
    <div class="row">
        <div class="col">
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                    
                        <th scope="col">Nom</th>
                        <th scope="col">Prénom</th>
                        <th scope="col">Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    
                    $requete =$bdd->prepare('SELECT nom, prenom, email FROM users_eleve ');
                    $requete -> execute();
                    while ($row = $requete->fetch()) {
                        echo '<tr>';
                     
                        echo '<td>' . $row['nom'] . '</td>';
                        echo '<td>' . $row['prenom'] . '</td>';
                        echo '<td>' . $row['email'] . '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const moyennesParClasse = <?php echo $moyennes_json; ?>;
const moyenne = moyennesParClasse[0];
const vue = moyennesParClasse[1];

const labels = Object.keys(moyenne);
const valeur = Object.values(vue);
var ctx = document.getElementById('myChart').getContext('2d');

const moyennesData = Object.values(moyennesParClasse).map(parseFloat);
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: labels,
            data: moyenne,
            backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)'],
            borderColor: ['rgba(255, 99, 132, 1)', 'rgba( 54, 162, 235, 1)', 'rgba(255, 206, 86, 1)'],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                ticks: {
                    beginAtZero: true,
                    color: 'rgba(24, 83, 79)' // Couleur des étiquettes de l'axe y en blanc
                },
                grid: {
                    color: 'rgba(24, 83, 79)', // Couleur des lignes de l'axe y
                    borderColor: 'rgba(24, 83, 79)' // Couleur de la ligne zéro de l'axe y
                },gridLines: {
                    color: 'rgba(24, 83, 79)', // Couleur des lignes de l'axe x
                    zeroLineColor: 'rgba(24, 83, 79)' // Couleur de la ligne zéro de l'axe x
                }
            },
            x: {
                ticks: {
                    color: 'rgba(24, 83, 79)' // Couleur des étiquettes de l'axe x en blanc
                },
                grid: {
                    color: 'rgba(24, 83, 79)', // Couleur des lignes de l'axe x
                    borderColor: 'rgba(24, 83, 79)' // Couleur de la ligne zéro de l'axe x
                }
            }
        },
        // Changement de la couleur de fond du graphique
        plugins: {
            legend: {
                display: true,
                labels: {
                    color: 'rgba(24, 83, 79)'
                }
            },
            title: {
                display: true,
                text: 'Titre du graphique',
                color: 'rgba(24, 83, 79)'
            },
            annotation: {
                annotations: [{
                    type: 'box',
                    xScaleID: 'x-axis-0',
                    yScaleID: 'y-axis-0',
                    xMin: 'jan',
                    xMax: 'dec',
                    yMin: 0,
                    yMax: 100,
                    backgroundColor: 'rgba(24, 83, 79)'
                }]
            }
        }
    }
});

function getBorderColor(index) {
    const colors = ['rgba(255, 99, 132, 1)', 'rgb(247, 152, 6)', 'rgba(54, 162, 235, 1)'];
    return colors[index % colors.length]; // Boucler sur les couleurs si l'indice dépasse la longueur du tableau de couleurs
}

function getBackgroundColor(index) {
    const colors = ['rgba(255, 99, 132, 0.2)', 'rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)'];
    return colors[index % colors.length]; // Boucler sur les couleurs si l'indice dépasse la longueur du tableau de couleurs
}

const datasets = [];

for (let i = 0; i < labels.length; i++) {
    const dataset = {
        label: labels[i],
        data: valeur[i],
        borderColor: getBorderColor(i),
        backgroundColor: getBackgroundColor(i),
        tension: 0.3,
        fill: false
    };
    datasets.push(dataset);
}

const ctx2 = document.getElementById('myChart2').getContext('2d');
var myChart2 = new Chart(ctx2, {
    type: 'line',
    data: {
        labels: ['jan', 'fev', 'mars', 'avr', 'mai', 'juin', 'sept', 'oct', 'nov', 'dec'],
        datasets: datasets,
    },
    options: {
        maintainAspectRatio: false,
        responsive: true,
        scales: {
            y: {
                ticks: {
                    beginAtZero: true,
                    color: 'rgba(24, 83, 79)' // Couleur des étiquettes de l'axe y en blanc
                },
                grid: {
                    color: 'rgba(24, 83, 79)', // Couleur des lignes de l'axe y
                    borderColor: 'rgba(24, 83, 79)' // Couleur de la ligne zéro de l'axe y
                },gridLines: {
                    color: 'rgba(24, 83, 79)', // Couleur des lignes de l'axe x
                    zeroLineColor: 'rgba(24, 83, 79)' // Couleur de la ligne zéro de l'axe x
                }
            },
            x: {
                ticks: {
                    color: 'rgba(24, 83, 79)' // Couleur des étiquettes de l'axe x en blanc
                },
                grid: {
                    color: 'rgb(24, 83, 79, 2)', // Couleur des lignes de l'axe x
                    borderColor: 'rgba(24, 83, 79)' // Couleur de la ligne zéro de l'axe x
                }
            }
        },
        plugins: {
            legend: {
                display: true,
                labels: {
                    color: 'rgba(24, 83, 79)'
                }
            },
            title: {
                display: true,
                text: 'Titre du graphique',
                color: 'rgba(24, 83, 79)'
            },
            annotation: {
                annotations: [{
                    type: 'box',
                    xScaleID: 'x-axis-0',
                    yScaleID: 'y-axis-0',
                    xMin: 'jan',
                    xMax: 'dec',
                    yMin: 0,
                    yMax: 100,
                    backgroundColor: 'rgba(24, 83, 79)'
                }]
            }
        }
    }
});

</script>


<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
