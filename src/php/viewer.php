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

// Création de l'objet php-fit-file-analysis
$pFFA = new adriangibbons\phpFITFileAnalysis($_FILES['inputFile']['tmp_name'], $options);

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
                    $errors[] = "La taille du fichier était supérieure à 10 Mo.";
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
                        <link rel="stylesheet" href="../../resources/css/style.css">
                        <title>Visualiseur</title>
                    </head>
                    <body>
                    <header>
                        <?php include_once 'header.php'?>
                    </header>
                    <div class="file-info">
                        <a href="upload.php" class="blue-button">Revenir à l'accueil</a>
                        <div class="title" style="margin-top: 10px"><?php echo pathinfo($_FILES['inputFile']['name'])['basename'] ?></div>
                        <div class="general-infos">
                            <div class="general-info">
                                <div class="primary-text">
                                    <?php
                                    // Affichage de la durée totale de la course.
                                    // gmdate utilise Fuseau horaire de Greenwich
                                    // => Pas besoin de soustraire ou d'addition le fuseau horaire du serveur !
                                    echo gmdate('H:i:s', floor($pFFA->data_mesgs['session']['total_elapsed_time']));
                                    ?>
                                </div>
                                <div class="secondary-text">Durée totale</div>
                            </div>
                            <div class="general-info">
                                <div class="primary-text">
                                    <?php
                                        // Affichage de la distance totale parcourue.
                                        echo $pFFA->data_mesgs['session']['total_distance'].' km';
                                    ?>
                                </div>
                                <div class="secondary-text">Distance Parcourue</div>
                            </div>
                            <div class="general-info">
                                <div class="primary-text">
                                    <?php
                                        echo gmdate('d/m/Y H:i:s', floor($pFFA->data_mesgs['session']['start_time']));
                                    ?>
                                </div>
                                <div class="secondary-text">Date et heure de début</div>
                            </div>
                        </div>
                    </div>

                    <div id="multiChart" class="chart" ">
                        <canvas id="multiGraph"</canvas>
                    </div>

                    <?php









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

                    // Affichage des infos comme le produit sur lequel les données furent collectées, le constructeur et le type de sport effectué le long du parcours.
                    echo '
                      <div id="infos">
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
                    echo '
                      <div id="altitude">
                    ';
                    if(isset($pFFA->data_mesgs['record']['altitude']))
                    {
                        // Affichage de l'élévation durant le parcours.
                        // Affichage de l'altitude minimum, moyennne, et maximum.
                        $altitude = $pFFA->data_mesgs['record']['altitude'];
                        echo "Max : ".max($altitude)." m<br>";
                        echo "Average : ".floor((array_sum($altitude) / count($altitude)))." m<br>";
                        echo "Min : ".min($altitude)."  m<br>";
                    }
                    else
                    {
                        echo 'L\'altitude n\'est pas indiquée dans ce fichier ! <br>';
                    }
                    echo '</div>';

                    // Si le fichier contient des données d'énergie, on les affiche. Sinon, message d'erreur.
                    echo '
                      <div id="power">
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
                    echo '
                      <div id="bpm">
                    ';
                    if(isset($pFFA->data_mesgs['record']['heart_rate']))
                    {
                        // Affichage des BPM minimum, moyens, et maximum.
                        $bpm = $pFFA->data_mesgs['record']['heart_rate'];
                        echo "Max : ".max($bpm)." BPM<br>";
                        echo "Average : ".floor((array_sum($bpm) / count($bpm)))." BPM<br>";
                        echo "Min : ".min($bpm)."  BPM<br>";
                    }
                    else
                    {
                        echo 'Les BPM ne sont pas indiqués dans ce fichier ! <br>';
                    }
                    echo '</div>';

                    ?>
                    <footer style="position: static">
                        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.2.1/dist/chart.min.js" integrity="sha256-uVEHWRIr846/vAdLJeybWxjPNStREzOlqLMXjW/Saeo=" crossorigin="anonymous"></script>
                        <?php include_once 'footer.php' ?>
                            <?php
                                $altitudeJS = array();
                                $speedJS = array();
                                $bpmJS = array();
                                $powerJS = array();
                                $confirm = true;
                                //The program will take one out of 'numberPoints' points.
                                $numberPoints = 10;
                                $timestamps = array();
                                $i = 0;
                                foreach($pFFA->data_mesgs['record']['timestamp'] as $timestamp)
                                {
                                    if($i % $numberPoints == 0)
                                    {
                                        if(!isset($pFFA->data_mesgs['record']['power'][$timestamp]))
                                        {
                                            $confirm = false;
                                        }
                                        if(!isset($pFFA->data_mesgs['record']['heart_rate'][$timestamp]))
                                        {
                                            $confirm = false;
                                        }
                                        if(!isset($pFFA->data_mesgs['record']['altitude'][$timestamp]))
                                        {
                                            $confirm = false;
                                        }
                                        else if ($pFFA->data_mesgs['record']['altitude'][$timestamp] == 0)
                                        {
                                            $confirm = false;
                                        }
                                    }

                                    if($i % $numberPoints == 0 && $confirm)
                                    {
                                        $powerJS[] = $pFFA->data_mesgs['record']['power'][$timestamp];
                                        $bpmJS[] = $pFFA->data_mesgs['record']['heart_rate'][$timestamp];
                                        $altitudeJS[] = $pFFA->data_mesgs['record']['altitude'][$timestamp];
                                        //Insert the timestamp in the $count array.
                                        $timestamps[] = gmdate('H:i:s',$timestamp - $pFFA->data_mesgs['session']['start_time']);
                                    }
                                    else if ($i % $numberPoints == 0)
                                    {
                                        $confirm = true;
                                    }

                                    $i++;
                                }
                            ?>
                            <script type="text/javascript">
                                var altitudeArray = <?php echo json_encode($altitudeJS); ?>;
                                var bpmArray = <?php echo json_encode($bpmJS); ?>;
                                var powerArray = <?php echo json_encode($powerJS); ?>;
                                var numberPoints = <?php echo $numberPoints; ?>;
                                var numbersArray = <?php echo json_encode($timestamps); ?>;

                                console.log(altitudeArray);
                                console.log(powerArray);
                                console.log(numbersArray);

                                //CHART DATA
                                const multiData ={
                                    labels: numbersArray,
                                    datasets: [{
                                        label: ' (m) Altitude ',
                                        //Styling
                                        backgroundColor: '#4C799E',
                                        borderColor: '#4C799E',
                                        borderWidth: 4,
                                        pointRadius: 0,
                                        data: altitudeArray,
                                        yAxisID: 'y',
                                    },{
                                        label: '  BPM ',
                                        //Styling
                                        backgroundColor: '#699E42',
                                        borderColor: '#699E42',
                                        borderWidth: 4,
                                        pointRadius: 0,
                                        data: bpmArray,
                                        yAxisID: 'y1',
                                    },{
                                        label: ' (W) Power ',
                                        //Styling
                                        backgroundColor: '#CD3B3B',
                                        borderColor: '#CD3B3B',
                                        borderWidth: 4,
                                        pointRadius: 0,
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
                                                grid: {
                                                    color: "rgba(64,64,64)",
                                                    borderColor: "rgba(64,64,64)",
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
                                                    borderColor: '#cd3b3b',
                                                    borderWidth: 4,
                                                    lineWidth: 0,
                                                    display: false,
                                                    color: "rgba(0,0,0,0)",
                                                },
                                                ticks: {
                                                    color: '#cd3b3b',
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
                                                    borderColor: '#699E42',
                                                    borderWidth: 4,
                                                    lineWidth: 0,
                                                    display: false,
                                                    color: "rgba(0,0,0,0)",
                                                },
                                                ticks: {
                                                    color: '#699E42',
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
                                                    borderColor: '#4C799E',
                                                    borderWidth: 4,
                                                    lineWidth: 0,
                                                    display: false,
                                                    color: "rgba(0,0,0,0)",
                                                },
                                                ticks: {
                                                    color: '#4C799E',
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
                                            },
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
                                        },
                                    }
                                };

                                var multiChart = new Chart (
                                    document.getElementById('multiGraph'),
                                    config
                                )
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
            <p>Vous êtes tombé à un mauvais endroit. Veuillez revenir à l\'accueil en cliquant <a href="upload.php"> ici.</a></p>
            ';
        }
