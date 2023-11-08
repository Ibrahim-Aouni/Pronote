<?php

try {
    $bdd = new PDO('mysql:host=localhost;dbname=pronote;charset=utf8', 'root', '');
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

$requete = $bdd->prepare('SELECT firstname, lastname, note FROM eleve');
$requete->execute();

$nomFichierCSV = 'liste_eleves.csv';

$handle = fopen($nomFichierCSV, 'w');

fputcsv($handle, array('Prénom', 'Nom', 'Note'));

while ($row = $requete->fetch()) {
    fputcsv($handle, array($row['firstname'], $row['lastname'], $row['note']));
}

fclose($handle);

echo 'Données exportées avec succès dans le fichier : ' . $nomFichierCSV;

?>
