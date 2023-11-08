<?php

try {
    $bdd = new PDO('mysql:host=localhost;dbname=pronote;charset=utf8', 'root', '');
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

$inserer_csv = 'ajout_eleves.csv';
if (($handle = fopen($inserer_csv, "r")) !== false) {
    while (($data = fgetcsv($handle, 1000, ",")) !== false) {
        $sql = "INSERT INTO eleve (firstname, lastname, note) VALUES ('" . $data[0] . "', '" . $data[1] . "', '" . $data[2] . "')";
        if ($bdd->query($sql) === false) {
            echo "Erreur d'insertion de ligne: " . $bdd->errorInfo()[2] . "<br>";
        } else {
            echo "Ligne insérée avec succès!<br>";
        }
    }
    fclose($handle);
} else {
    echo "Impossible d'ouvrir le fichier CSV.";
}


?>