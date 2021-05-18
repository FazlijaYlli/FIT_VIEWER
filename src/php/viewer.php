<?php
//  ETML
//  NOM : YLLI FAZLIJA
//  DATE : 17.05.21
//  DESCRIPTION : PAGE DE VALIDATION DE DONNEES DU FICHIER TELECHARGE PRECEDEMMENT. ON TESTE L'EXTENSION ET LA TAILLE.
//                SI ERREUR, ON AJOUTE L'ERREUR AU TABLEAU ET ON L'AFFICHE SUR LA PAGE "UPLOAD.PHP"

session_start();
$errors = array();

//  Inclusion de la classe servant à parser le fichier et extraire les données.
include('../../vendor/adriangibbons/php-fit-file-analysis/src/phpFITFileAnalysis.php');  // this file is in the project's root folder
$options = [
    'units'                   => 'metric',
    'pace'                    => false,
];

        // Si l'utilisateur vient de la page upload.php, en utilisant le bouton submit
        if(isset($_POST['inputSubmit']) && isset($_FILES['inputFile'])) {
        /////////////////////////////
        /// VALIDATION DE DONNEES ///
        /////////////////////////////

            if($_FILES['inputFile']['size'] > 0)
            {
                // Vérification de l'extension.
                if(pathinfo($_FILES['inputFile']['name'])['extension'] != 'fit')
                {
                    $errors[] = "Le fichier téléchargé n'était pas un fichier FIT.";
                }

                // Vérification de la taille.
                if($_FILES['inputFile']['size'] >= 10000000)
                {
                    $errors[] = "La taille du fichier était supérieure à 10 Mo";
                }

                // Si aucune erreurs n'est survenue, on affiche le fichier.
                if(count($errors) == 0)
                {
                    /////////////////////////
                    /// AFFICHAGE DU SITE ///
                    /////////////////////////

                    echo '
                    <html>
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport"
                              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
                        <meta http-equiv="X-UA-Compatible" content="ie=edge">
                        <title>Visualiseur</title>
                    </head>
                    <body style="display:flex; flex-direction: column; align-items: center;">
                      ';

                    // Création de l'objet php-fit-file-analysis
                    $pFFA = new adriangibbons\phpFITFileAnalysis($_FILES['inputFile']['tmp_name'], $options);

                    // Set de la timezone
                    date_default_timezone_set('Europe/Paris');

                    // Affichage de la durée totale de la course.
                    // (Date de fin - Date de début) - 1h = Durée totale
                    $duration = date("H:i:s",(end($pFFA->data_mesgs['record']['timestamp']) - $pFFA->data_mesgs['record']['timestamp'][1]) - 3600);
                    echo '<button onclick="Show(\'duration\')">Afficher la durée totale</button>';
                    echo '<div id="duration" style="display:none;">';
                    echo $duration;
                    echo '</div>';

                    // Si le fichier contient des données de vitesse, on les affiche, Sinon, message d'erreur.
                    if(isset($pFFA->data_mesgs['record']['speed']))
                    {
                        // Affichage de la vitesse minimum, moyenne, maximum
                        echo '
                          <button onclick="Show(\'speed\')">Vitesses</button>
                          <div id="speed" style="display: none">
                    ';
                        echo "Max : " . max($pFFA->data_mesgs['record']['speed']) . " km/h<br>";
                        echo "Average : " . (array_sum($pFFA->data_mesgs['record']['speed']) / count($pFFA->data_mesgs['record']['speed'])) . "  km/h<br>";
                        echo "Min : " . min($pFFA->data_mesgs['record']['speed']) . "  km/h<br>";
                        echo '</div>';
                    }
                    else {
                        echo 'La vitesse n\'est pas indiquée dans ce fichier !';
                    }

                    // Si le fichier contient des données de puissances, on les affiche. Sinon, message d'erreur.
                    if(isset($pFFA->data_mesgs['record']['power']))
                    {
                        // Affichage de la puissance minimum, moyenne, et maximum.
                        echo '
                          <button onclick="Show(\'power\')">Puissance</button>
                          <div id="power" style="display: none">
                    ';

                        echo "Max : " . max($pFFA->data_mesgs['record']['power']) . " W<br>";
                        echo "Average : " . (array_sum($pFFA->data_mesgs['record']['power']) / count($pFFA->data_mesgs['record']['power'])) . " W<br>";
                        echo "Min : " . min($pFFA->data_mesgs['record']['power']) . "  W<br>";
                        echo '</div>';
                    }
                    else
                    {
                        echo 'La puissance n\'est pas indiquée dans ce fichier !';
                    }

                    // Si le fichier contient des données de BPM, on les affiche. Sinon, message d'erreur.
                    if(isset($pFFA->data_mesgs['record']['heart_rate']))
                    {
                        // Affichage des BPM minimum, moyens, et maximum.
                        echo '
                          <button onclick="Show(\'bpm\')">BPM</button>
                          <div id="bpm" style="display: none">
                        ';

                        echo "Max : ".max($pFFA->data_mesgs['record']['heart_rate'])." BPM<br>";
                        echo "Average : ".(array_sum($pFFA->data_mesgs['record']['heart_rate']) / count($pFFA->data_mesgs['record']['heart_rate']))." BPM<br>";
                        echo "Min : ".min($pFFA->data_mesgs['record']['heart_rate'])."  BPM<br>";
                        echo '</div>';
                    }
                    else
                    {
                        echo 'Les BPM n\'est pas indiquée dans ce fichier !';
                    }

                    // Affichage de la chaque point, avec les données qui correspondent à ce point à côté.
                    echo '
                        <button onclick="Show(\'pointsWithUnknown\')">All points (With unknown data)</button>
                        <div id="pointsWithUnknown" style="display: none; flex-direction: row; flex-wrap: wrap; justify-content: space-between; width: 80%; margin: auto">
                    ';

                    foreach($pFFA->data_mesgs['record']['timestamp'] as $timestamp)
                    {
                        echo '<div style="margin: 10px; text-align: center;">';
                            echo 'TIMESTAMP : '.$timestamp;
                            echo '<br>';
                            echo 'DATE : '.date('d-m-Y H:i:s ', $timestamp);
                            echo '<br>';

                            // AFFICHAGE DE LA VITESSE
                            if(isset($pFFA->data_mesgs['record']['speed'][$timestamp]))
                            {
                                echo 'SPEED : '.$pFFA->data_mesgs['record']['speed'][$timestamp];
                            }
                            else
                            {
                                echo 'SPEED : UNKNOWN';
                            }
                            echo '<br>';

                            // AFFICHAGE DES BPM
                            if(isset($pFFA->data_mesgs['record']['heart_rate'][$timestamp]))
                            {
                                echo 'BPM : '.$pFFA->data_mesgs['record']['heart_rate'][$timestamp];

                            }
                            else
                            {
                                echo 'BPM : UNKNOWN';
                            }
                            echo '<br>';

                            // AFFICHAGE DES WATTS
                            if(isset($pFFA->data_mesgs['record']['power'][$timestamp]))
                            {
                                echo 'POWER : '.$pFFA->data_mesgs['record']['power'][$timestamp];
                            }
                            else
                            {
                                echo 'POWER : UNKNOWN';
                            }
                            echo '<br>';
                            echo '<br>';
                        echo '</div>';
                    }
                    echo '</div>';

                    // Affichage de la chaque point, avec les données qui correspondent à ce point à côté. Si une des donnée du point est inconnue, on supprime le point.
                    echo '
                        <button onclick="Show(\'pointsWithoutUnknown\')">All Points (Without unknown data)</button>
                        <div id="pointsWithoutUnknown" style="display: none; flex-direction: row; flex-wrap: wrap; justify-content: space-between; width: 80%; margin: auto">
                    ';

                    foreach($pFFA->data_mesgs['record']['timestamp'] as $timestamp)
                    {

                            if(!isset($pFFA->data_mesgs['record']['speed'][$timestamp]) || !isset($pFFA->data_mesgs['record']['heart_rate'][$timestamp]) || !isset($pFFA->data_mesgs['record']['power'][$timestamp]))
                            {
                                continue;
                            }

                            echo '<div style="margin: 10px; text-align: center;">';
                            echo 'TIMESTAMP : '.$timestamp;
                            echo '<br>';
                            echo 'DATE : '.date('d-m-Y H:i:s ', $timestamp);
                            echo '<br>';

                            // AFFICHAGE DE LA VITESSE
                            if(isset($pFFA->data_mesgs['record']['speed'][$timestamp]))
                            {
                                echo 'SPEED : '.$pFFA->data_mesgs['record']['speed'][$timestamp];
                            }
                            else
                            {
                                echo 'SPEED : UNKNOWN';
                            }
                            echo '<br>';

                            // AFFICHAGE DES BPM
                            if(isset($pFFA->data_mesgs['record']['heart_rate'][$timestamp]))
                            {
                                echo 'BPM : '.$pFFA->data_mesgs['record']['heart_rate'][$timestamp];

                            }
                            else
                            {
                                echo 'BPM : UNKNOWN';
                            }
                            echo '<br>';

                            // AFFICHAGE DES WATTS
                            if(isset($pFFA->data_mesgs['record']['power'][$timestamp]))
                            {
                                echo 'POWER : '.$pFFA->data_mesgs['record']['power'][$timestamp];
                            }
                            else
                            {
                                echo 'POWER : UNKNOWN';
                            }
                            echo '<br>';
                            echo '<br>';
                        echo '</div>';
                    }
                    echo '</div>';

                    echo '
                        <footer>
                            <script>
                                function Show(id) {
                                    var x = document.getElementById(id);
                                    if (x.style.display === "none") {
                                        x.style.display = "flex";
                                    } else {
                                        x.style.display = "none";
                                    }
                                }
                            </script>
                        </footer>
                    </body>
                    </html>
                    ';
                }
                else
                {
                    $_SESSION['errors'] = $errors;
                    header('Location:upload.php');
                    exit();
                }
            }
            else
            {
                $errors[] = "Veuillez choisir un fichier avant d'afficher.";
                $_SESSION['errors'] = $errors;
                header('Location:upload.php');
                exit();
            }
        } else {
            // Si l'utilisateur est venu sur la page en écrivant dans l'URL par exemple, écrire cette page d'erreur.
            echo '
            <h1>Oops !</h1>
            <p>Vous êtes tombé à un mauvais endroit. Veuillez revenir à l\'accueil en cliquant <a href="index.php"> ici.</a></p>
            ';
        }
