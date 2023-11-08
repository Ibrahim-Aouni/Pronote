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

$idModifier = null;

if (isset($_POST['newprenom']) && isset($_POST['newnom']) && isset($_POST['newnote'])) {
    $idModifier = $_POST['modifier'];
    
    $newprenom = $_POST['newprenom'];
    $newprenom= strip_tags($newprenom);
    $newprenom=htmlspecialchars($newprenom);
    $newnom = $_POST['newnom'];
    $newnom= strip_tags($newnom);
    $newnom=htmlspecialchars($newnom);
    $newnote = $_POST['newnote'];
    $newnote =strip_tags($newnote);
    $newnote=htmlspecialchar($newnote);
    
    $modifier = $bdd->prepare('UPDATE eleve SET firstname = :prenom, lastname = :nom, note = :note WHERE id = :id');
    $modifier->execute(array('id' => $idModifier, 'prenom' => $newprenom, 'nom' => $newnom, 'note' => $newnote));
} elseif (isset($_POST['modifier'])) {
    $idModifier = $_POST['modifier'];
    echo '<form method="post" action="">
        <input type="text" name="newprenom" placeholder="Nouveau prénom">
        <input type="text" name="newnom" placeholder="Nouveau nom">
        <input type="number" name="newnote" placeholder="Nouvelle note">
        <input type="hidden" name="modifier" value="' . $idModifier . '">
        <input type="submit" class="form-control">
    </form>';
}
?>
