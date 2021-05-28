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
    <link href="../../resources/css/style.css" type="text/css" rel="stylesheet">
    <title>Téléchargement d'un fichier</title>
</head>
<body style="height: 500px;">
    <?php include 'header.php'; ?>
    <div class="container">
        <div class="title" style="margin-top: 182px">Bienvenue sur FFV !</div>
        <?php
        if(isset($_SESSION['errors']))
        {
            echo '<ul class="error">';
            foreach ($_SESSION['errors'] as $error) {
                echo "
                <li style='line-height: 30px;'>$error</li>
            ";
            }
            echo '</ul>';
            session_destroy();
        }
        ?>
        <form style="display: flex; flex-direction: column; align-items: center;" action="viewer.php" method="post" enctype="multipart/form-data">
            <h3>Pour télécharger votre fichier FIT, cliquez sur le bouton ci-dessous et séléctionnez votre fichier.</h3>

            <label for="inputFile" class="gray-button">
                Choisir un fichier...
            </label>

            <input class="gray-button" onchange="ShowFileName(); const x = document.getElementsByClassName('gray-button'); x[0].innerHTML='Fichier choisi !'; x[0].className='green-button';" type="file" name="inputFile" id="inputFile">

            <div id="inputName" class="secondary-text" style="display: none; font-weight: bold; font-size: 10pt"></div>

            <label for="inputSubmit">
                <h3> Une fois ceci fait, cliquez sur "Afficher le fichier"</h3>
            </label>

            <input class="blue-button" type="submit" value="Afficher le fichier"  name="inputSubmit" id="inputSubmit">
        </form>
    </div>
    <footer>
        <?php include 'footer.php'; ?>
    </footer>
    <script>

        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /// CODE REPRIS DE STACK OVERFLOW ////////////////////////////////////////////////////////////////////////////////////
        /// AUTEUR : JAI /////////////////////////////////////////////////////////////////////////////////////////////////////
        /// LIEN :  https://stackoverflow.com/questions/857618/javascript-how-to-extract-filename-from-a-file-input-control///
        /// FONCTION :
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        function ShowFileName()
        {
            const fullPath = document.getElementById('inputFile').value;
            if (fullPath) {
                const startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
                let filename = fullPath.substring(startIndex);
                if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
                    filename = filename.substring(1);
                }
                document.getElementById('inputName').style.display='block';
                document.getElementById('inputName').innerHTML=filename;
            }
        }
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    </script>
</body>
</html>