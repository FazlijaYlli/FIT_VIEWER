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
    'pace'                    => false
];

        // Si l'utilisateur vient de la page upload.php, en utilisant le bouton submit
        if(isset($_POST['inputSubmit']) && isset($_FILES['inputFile'])) {
        /////////////////////////////
        /// VALIDATION DE DONNEES ///
        /////////////////////////////

            if($_FILES['inputFile']['size'] > 0)
            {
                $extensions = ['fit','FIT'];
                // Vérification de l'extension.
                if(in_array(pathinfo($_FILES['inputFile']['name'])['extension'] != 'fit', $extensions))
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

                    ?>
                    <html>
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport"
                              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
                        <meta http-equiv="X-UA-Compatible" content="ie=edge">
                        <title>Visualiseur</title>
                    </head>
                    <body style="display:flex; flex-direction: column; align-items: center;">
                    <header>
                        <?php include_once 'header.php'?>
                    </header>
                    <?php

                    // Création de l'objet php-fit-file-analysis
                    $pFFA = new adriangibbons\phpFITFileAnalysis($_FILES['inputFile']['tmp_name'], $options);

                    // Affichage de la durée totale de la course.
                    echo '<button onclick="Show(\'duration\')">Afficher la durée totale</button>';
                    echo '<div id="duration" style="display:none;">';
                    // gmdate utilise Fuseau horaire de Greenwich
                    // => Pas besoin de soustraire ou d'addition le fuseau horaire du serveur !
                    echo gmdate('H:i:s', floor($pFFA->data_mesgs['session']['total_elapsed_time']));
                    echo '</div>';

                    // Affichage de la distance totale parcourue.
                    echo '<button onclick="Show(\'distance\')">Afficher la distance totale</button>';
                    echo '<div id="distance" style="display:none;">';
                    echo $pFFA->data_mesgs['session']['total_distance'].' km';
                    echo '</div>';


                    // Affichage de la vitesse minimum, moyenne, maximum
                    echo '
                          <button onclick="Show(\'speed\')">Vitesses</button>
                          <div id="speed" style="display: none">
                    ';
                    // Si le fichier contient des données de vitesse, on les affiche, Sinon, message d'erreur.
                    if(isset($pFFA->data_mesgs['record']['speed']))
                    {
                        $speed = $pFFA->data_mesgs['record']['speed'];
                        echo "Max : ".max($speed). " km/h<br>";
                        echo "Average : ".floor((array_sum($speed) / count($speed)))."  km/h<br>";
                        echo "Min : ".min($speed) . "  km/h<br>";
                    }
                    else {
                        echo 'La vitesse n\'est pas indiquée dans ce fichier !';
                    }
                    echo '</div>';

                    // Affichage des infos comme le produit sur lequel les données furent collectées, le constructeur et le type de sport effectué le long du parcours.
                    echo '
                      <button onclick="Show(\'infos\')">Infos</button>
                      <div id="infos" style="display: none">
                    ';

                    if(isset($pFFA->data_mesgs['device_info']['product']))
                    {
                        echo 'PRODUCT : '.$pFFA->product().'<br>';
                    } else { echo 'PRODUCT : UNKNOWN <br>'; }

                    if(isset($pFFA->data_mesgs['device_info']['manufacturer']))
                    {
                        echo 'MANUFACTURER : '.$pFFA->manufacturer().'<br>';
                    } else { echo 'MANUFACTURER : UNKNOWN <br>'; }

                    if(isset($pFFA->data_mesgs['session']['sport']))
                    {
                        echo 'SPORT : '.$pFFA->sport().'<br>';
                    } else { echo 'SPORT : UNKNOWN <br>'; }

                    echo '</div>';


                    // Si le fichier contient des données d'altitude, on les affiche. Sinon, message d'erreur.
                    if(isset($pFFA->data_mesgs['record']['altitude']))
                    {
                        // Affichage de l'élévation durant le parcours.
                        // Affichage de l'altitude minimum, moyennne, et maximum.
                        echo '
                          <button onclick="Show(\'altitude\')">Altitude</button>
                          <div id="altitude" style="display: none">
                        ';
                        $altitude = $pFFA->data_mesgs['record']['altitude'];
                        echo "Max : ".max($altitude)." m<br>";
                        echo "Average : ".floor((array_sum($altitude) / count($altitude)))." m<br>";
                        echo "Min : ".min($altitude)."  m<br>";
                    }
                    else
                    {
                        echo '
                          <button onclick="Show(\'altitude\')">Altitude</button>
                          <div id="altitude" style="display: none">
                        ';
                        echo 'L\'altitude n\'est pas indiquée dans ce fichier ! <br>';
                    }
                    echo '</div>';

                    // Si le fichier contient des données d'énergie, on les affiche. Sinon, message d'erreur.
                    echo '
                      <button onclick="Show(\'power\')">Puissance</button>
                      <div id="power" style="display: none">
                    ';
                    if(isset($pFFA->data_mesgs['record']['power']))
                    {
                        // Affichage de l'énergie minimum, moyenne, et maximum, en watt.
                        // On affiche aussi les KiloJoules, obtenu avec la méthode "powerMetrics" retournant différentes valeurs, dont les KJ, selon le fichier.
                        $power = $pFFA->data_mesgs['record']['power'];
                        echo "Max : " . max($power) . " W<br>";
                        echo "Average : " . floor((array_sum($power) / count($power))) . " W<br>";
                        echo "Min : " . min($power) . "  W<br>";
                    }
                    else
                    {
                        echo 'La puissance n\'est pas indiquée dans ce fichier ! <br>';
                    }
                    echo '</div>';

                    // Si le fichier contient des données de BPM, on les affiche. Sinon, message d'erreur.
                    if(isset($pFFA->data_mesgs['record']['heart_rate']))
                    {
                        // Affichage des BPM minimum, moyens, et maximum.
                        echo '
                          <button onclick="Show(\'bpm\')">BPM</button>
                          <div id="bpm" style="display: none">
                        ';
                        $bpm = $pFFA->data_mesgs['record']['heart_rate'];
                        echo "Max : ".max($bpm)." BPM<br>";
                        echo "Average : ".floor((array_sum($bpm) / count($bpm)))." BPM<br>";
                        echo "Min : ".min($bpm)."  BPM<br>";
                    }
                    else
                    {
                        echo '
                          <button onclick="Show(\'power\')">Puissance</button>
                          <div id="power" style="display: none">
                        ';
                        echo 'Les BPM ne sont pas indiqués dans ce fichier ! <br>';
                    }
                    echo '</div>';



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
                                echo 'SPEED : '.($pFFA->data_mesgs['record']['speed'][$timestamp]);
                            }
                            else
                            {
                                echo 'SPEED : UNKNOWN';
                            }
                            echo '<br>';

                            // AFFICHAGE DE L'ALTITUDE
                            if(isset($pFFA->data_mesgs['record']['altitude'][$timestamp]))
                            {
                                echo 'ALTITUDE : '.$pFFA->data_mesgs['record']['altitude'][$timestamp];
                            }
                            else
                            {
                                echo 'ALTITUDE : UNKNOWN';
                            }
                            echo '<br>';

                            // AFFICHAGE DE LA DISTANCE
                            if(isset($pFFA->data_mesgs['record']['distance'][$timestamp]))
                            {
                                echo 'DISTANCE : '.$pFFA->data_mesgs['record']['distance'][$timestamp];
                            }
                            else
                            {
                                echo 'DISTANCE : UNKNOWN';
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

                            // AFFICHAGE DE L'ALTITUDE
                            if(isset($pFFA->data_mesgs['record']['altitude'][$timestamp]))
                            {
                                echo 'ALTITUDE : '.$pFFA->data_mesgs['record']['altitude'][$timestamp];
                            }
                            else
                            {
                                echo 'ALTITUDE : UNKNOWN';
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

                    ?>
                    <button onclick="Show('multiChart')">Graphique</button>
                    <div id="multiChart" style="display: none; width: 95%; height: 30%;">
                        <canvas id="multiGraph"</canvas>
                    </div>

                    <footer>
                            <?php include_once 'footer.php' ?>
                            <?php
                                $altitudeJS = array();
                                $speedJS = array();
                                $count = count($pFFA->data_mesgs['record']['timestamp']);
                                $i = 0;
                                foreach($pFFA->data_mesgs['record']['timestamp'] as $timestamp)
                                {
                                    if($i % 5 == 0)
                                    {
                                        if(isset($pFFA->data_mesgs['record']['power'][$timestamp]))
                                        {
                                            $powerJS[] = $pFFA->data_mesgs['record']['power'][$timestamp];
                                        }
                                        if(isset($pFFA->data_mesgs['record']['heart_rate'][$timestamp]))
                                        {
                                            $bpmJS[] = $pFFA->data_mesgs['record']['heart_rate'][$timestamp];
                                        }
                                        if(isset($pFFA->data_mesgs['record']['altitude'][$timestamp]) && $pFFA->data_mesgs['record']['altitude'][$timestamp] != 0)
                                        {
                                            $altitudeJS[] = $pFFA->data_mesgs['record']['altitude'][$timestamp];
                                        }
                                    }
                                    $i++;
                                }
                            ?>
                            <script src="https://cdn.jsdelivr.net/npm/chart.js@3.2.1/dist/chart.min.js" integrity="sha256-uVEHWRIr846/vAdLJeybWxjPNStREzOlqLMXjW/Saeo=" crossorigin="anonymous"></script>
                            <script type="text/javascript">

                                var altitudeArray = <?php echo json_encode($altitudeJS); ?>;
                                var bpmArray = <?php echo json_encode($bpmJS); ?>;
                                var powerArray = <?php echo json_encode($powerJS); ?>;

                                var count = <?php echo $count; ?>;
                                var numbersArray = [];
                                for (i = 0; i < count ; i++)
                                {
                                    ////////////////////////////////////////////////////////////////////////////////////////////////////////
                                    /// CODE REPRIS DE STACK OVERFLOW //////////////////////////////////////////////////////////////////////
                                    /// AUTEUR : ARON ROTTVEEL /////////////////////////////////////////////////////////////////////////////
                                    /// LIEN : https://stackoverflow.com/questions/847185/convert-a-unix-timestamp-to-time-in-javascript ///
                                    ////////////////////////////////////////////////////////////////////////////////////////////////////////
                                    if(i % 5 === 0)
                                    {
                                        let unix_timestamp = i;
                                        // Create a new JavaScript Date object based on the timestamp
                                        // multiplied by 1000 so that the argument is in milliseconds, not seconds.
                                        var date = new Date(unix_timestamp * 1000);
                                        // Hours part from the timestamp
                                        var hours = date.getHours();
                                        // Minutes part from the timestamp
                                        var minutes = "0" + date.getMinutes();
                                        // Seconds part from the timestamp
                                        var seconds = "0" + date.getSeconds();

                                        var formattedTime = hours - 1 + ':' + minutes.substr(-2) + ':' + seconds.substr(-2);

                                        numbersArray.push(formattedTime);
                                    }
                                }

                                console.log(altitudeArray);
                                console.log(powerArray);
                                console.log(numbersArray);

                                //CHART DATA
                                const multiData ={
                                    labels: numbersArray,
                                    datasets: [{
                                        label: ' (m) Altitude ',
                                        //Styling
                                        backgroundColor: 'rgb(255,0,0)',
                                        borderColor: 'rgb(255,0,0)',
                                        borderWidth: 4,
                                        pointRadius: 2,
                                        data: altitudeArray,
                                        yAxisID: 'y',
                                    },{
                                        label: '  BPM ',
                                        //Styling
                                        backgroundColor: 'rgb(0,255,0)',
                                        borderColor: 'rgb(0,255,0)',
                                        borderWidth: 4,
                                        pointRadius: 2,
                                        data: bpmArray,
                                        yAxisID: 'y1',
                                    },{
                                        label: ' (W) Power ',
                                        //Styling
                                        backgroundColor: 'rgb(0,0,255)',
                                        borderColor: 'rgb(0,0,255)',
                                        borderWidth: 4,
                                        pointRadius: 2,
                                        data: powerArray,
                                        yAxisID: 'y2',
                                    }],
                                };

                                //CHART CONFIG
                                const config = {
                                    //Type of chart
                                    type: 'line',
                                    //Inserting the data above.
                                    data: multiData,
                                    options: {
                                        //With these options I am able to resize the graph.
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        scales: {
                                            x: {
                                                display: true,
                                                ticks: {
                                                    display: true,
                                                },
                                                title: {
                                                    display: true,
                                                    font: {
                                                        size: 20,
                                                        weight: 'bold',
                                                    },
                                                    text: "Track Points",
                                                    align: 'center'
                                                }
                                            },
                                            y: {
                                                display: true,
                                                type: 'linear',
                                                grid: {
                                                    borderColor: 'red'
                                                },
                                                ticks: {
                                                    color: 'red',
                                                    callback: function (val) {
                                                        return val + " m";
                                                    },
                                                },
                                                title: {
                                                    display: true,
                                                    font: {
                                                        size: 20,

                                                        weight: 'bold',
                                                    },
                                                    text: "",
                                                    align: 'center'
                                                }
                                            },
                                            y1: {
                                                display: true,
                                                type: 'linear',
                                                grid: {
                                                    borderColor: 'green'
                                                },
                                                ticks: {
                                                    color: 'green',
                                                    callback: function (val) {
                                                        return val + " BPM";
                                                    },
                                                },
                                                title: {
                                                    display: true,
                                                    font: {
                                                        size: 20,
                                                        weight: 'bold',
                                                    },
                                                    text: "",
                                                    align: 'center'
                                                }
                                            },
                                            y2: {
                                                display: true,
                                                type: 'linear',
                                                grid: {
                                                    borderColor: 'blue'
                                                },
                                                ticks: {
                                                    color: 'blue',
                                                    callback: function (val) {
                                                        return val + " W";
                                                    },
                                                },
                                                title: {
                                                    display: true,
                                                    font: {
                                                        size: 20,
                                                        weight: 'bold',
                                                    },
                                                    text: "",
                                                    align: 'center'
                                                }
                                            }
                                        },
                                        interaction : {
                                            intersect: false,
                                            mode : 'nearest',
                                            axis: 'x'
                                        },
                                        plugins : {
                                            tooltip : {
                                                position: 'nearest',
                                                titleFont: {
                                                    family: "'Helvetica','Arial','sans-serif'",
                                                    size: 24,
                                                    weight: 'bold',
                                                },
                                                bodyFont: {
                                                    family: "'Helvetica','Arial','sans-serif'",
                                                    size: 12,
                                                    weight: 'normal',
                                                },
                                                bodySpacing: 10,
                                                padding: 20,
                                                boxWidth: 3,
                                            }
                                        }
                                    }
                                };

                                var multiChart = new Chart (
                                    document.getElementById('multiGraph'),
                                    config
                                )
                            </script>
                            <script>
                                function Show(id) {
                                    const x = document.getElementById(id);
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
                    <?php
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
