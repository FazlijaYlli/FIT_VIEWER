<?php
//  ETML
//  NOM : YLLI FAZLIJA
//  DATE : 17.05.21
//  DESCRIPTION : PAGE D'UPLOAD DE FICHIER FIT. LE FORMULAIRE SE TROUVE SUR CETTE PAGE, PUIS LA VALIDATION DE DONNEES SE FAIT SUR LA PAGE "VIEWER.PHP".
session_start();

?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Téléchargement d'un fichier</title>
</head>
<body>
<div style="display: flex;">
    <div style="margin: 50px auto 50px; display: flex; flex-direction: column; align-items: center">
        <?php include 'header.php'; ?>
        <h1>Téléchargement d'un fichier FIT</h1>
        <?php
        if(isset($_SESSION['errors']))
        {
            echo '<ul>';
            foreach ($_SESSION['errors'] as $error) {
                echo "
                <li style='color: red'>$error</li>
            ";
            }
            echo '</ul>';
            session_destroy();
        }
        ?>
        <form style="display: flex; flex-direction: column; align-items: center;" action="viewer.php" method="post" enctype="multipart/form-data">
            <label for="inputFile">
                <h3>Pour télécharger votre fichier FIT, cliquez sur le bouton ci-dessous et séléctionnez votre fichier.</h3>
            </label>
            <input type="file" name="inputFile" id="inputFile">
            <label for="inputSubmit">
                <h3> Une fois ceci fait, cliquez sur "Afficher le fichier"</h3>
            </label>
            <input type="submit" value="Afficher le fichier"  name="inputSubmit" id="inputSubmit">
        </form>
        <?php include 'footer.php'; ?>
    </div>
</div>
</body>
</html>