<?php

try {
    $bdd = new PDO('mysql:host=localhost;dbname=pronote;charset=utf8', 'root', '');
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Requête pour récupérer les données depuis la base de données
$sql = "SELECT intitule, note, appreciation, eleve_id FROM examen";
$result = $bdd->query($sql);

// Créer une nouvelle feuille de calcul
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Écrire les données dans la feuille de calcul
if ($result->rowCount() > 0) {
    $row = 1;
    while ($row_data = $result->fetch(PDO::FETCH_ASSOC)) {
        $column = 1;
        foreach ($row_data as $value) {
            $sheet->setCellValueByColumnAndRow($column, $row, $value);
            $column++;
        }
        $row++;
    }
}

// Enregistrer le fichier Excel
$writer = new Xlsx($spreadsheet);
$writer->save('donnees_de_ma_base.xlsx');

?>
