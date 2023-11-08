<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $destinataire = $_POST["destinataire"];
    $sujet = $_POST["sujet"];
    $message = $_POST["message"];

    // En-têtes de l'e-mail
    $headers = "From: aouniibrahim94@gmail.com\r\n";
    $headers .= "Reply-To: aouniibrahim94@gmail.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n";

    // Envoi de l'e-mail
    if (mail($destinataire, $sujet, $message, $headers)) {
        echo "L'e-mail a été envoyé avec succès.";
    } else {
        echo "L'envoi de l'e-mail a échoué.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Formulaire d'envoi d'e-mail</title>
</head>
<body>
    <h1>Contactez-nous</h1>
    <p>Remplissez le formulaire ci-dessous pour nous contacter.</p>

    <form action="" method="post">
        <label for="destinataire">Destinataire :</label>
        <input type="email" id="destinataire" name="destinataire" required>

        <label for="sujet">Sujet :</label>
        <input type="text" id="sujet" name="sujet" required>

        <label for="message">Message :</label>
        <textarea id="message" name="message" rows="4" required></textarea>

        <input type="submit" value="Envoyer">
    </form>
</body>
</html>
