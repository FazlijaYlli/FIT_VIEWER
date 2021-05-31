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
                    $errors[] = "La taille du fichier était supérieure à 10 Mo.";
                }

                // Si aucune erreurs n'est survenue, on affiche le fichier.
                if(count($errors) == 0) {
                    /////////////////////////
                    /// AFFICHAGE DU SITE ///
                    /////////////////////////

                    // Création de l'objet php-fit-file-analysis
                    $pFFA = new adriangibbons\phpFITFileAnalysis($_FILES['inputFile']['tmp_name'], $options);
                    include_once 'viewer_content.php';
                }
                else {
                    $_SESSION['errors'] = $errors;
                    header('Location:upload.php');
                    exit();
                }
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

                                $altitudeConfirm = true;
                                $bpmConfirm = true;
                                $powerConfirm = true;

                                $altitudePresent = false;
                                $bpmPresent = false;
                                $powerPresent = false;

                                //The program will take one out of 'numberPoints' points.
                                $numberPoints = 5;
                                $timestamps = array();
                                $i = 0;
                                foreach($pFFA->data_mesgs['record']['timestamp'] as $timestamp)
                                {
                                    if($i % $numberPoints == 0)
                                    {
                                        if(!isset($pFFA->data_mesgs['record']['power'][$timestamp]))
                                        {
                                            $powerConfirm = false;
                                        }
                                        else
                                        {
                                            $powerPresent = true;
                                        }

                                        if(!isset($pFFA->data_mesgs['record']['heart_rate'][$timestamp]))
                                        {
                                            $bpmConfirms = false;
                                        }
                                        else if ($pFFA->data_mesgs['record']['heart_rate'][$timestamp] == 0)
                                        {
                                            $bpmConfirm = false;
                                        }
                                        else
                                        {
                                            $bpmPresent = true;
                                        }

                                        if(!isset($pFFA->data_mesgs['record']['altitude'][$timestamp]))
                                        {
                                            $altitudeConfirm = false;
                                        }
                                        else if ($pFFA->data_mesgs['record']['altitude'][$timestamp] == 0)
                                        {
                                            $altitudeConfirm = false;
                                        }
                                        else
                                        {
                                            $altitudePresent = true;
                                        }
                                    }

                                    if($i % $numberPoints == 0)
                                    {
                                        if($altitudeConfirm)
                                        {
                                            $altitudeJS[] = $pFFA->data_mesgs['record']['altitude'][$timestamp];
                                        }

                                        if($bpmConfirm)
                                        {
                                            $bpmJS[] = $pFFA->data_mesgs['record']['heart_rate'][$timestamp];
                                        }

                                        if($powerConfirm)
                                        {
                                            $powerJS[] = $pFFA->data_mesgs['record']['power'][$timestamp];
                                        }

                                        //Insert the timestamp in the $count array.
                                        $timestamps[] = gmdate('H:i:s',$timestamp - $pFFA->data_mesgs['session']['start_time']);

                                        $altitudeConfirm = true;
                                        $bpmConfirm = true;
                                        $powerConfirm = true;
                                    }
                                    $i++;
                                }
                            ?>
                            <script type="text/javascript">
                                const altitudeArray = <?php echo json_encode($altitudeJS); ?>;
                                const bpmArray = <?php echo json_encode($bpmJS); ?>;
                                const powerArray = <?php echo json_encode($powerJS); ?>;
                                const numberPoints = <?php echo $numberPoints; ?>;
                                const numbersArray = <?php echo json_encode($timestamps); ?>;

                                const altitudePresent = !!parseInt(<?php echo $altitudePresent; ?>);
                                const powerPresent = !!parseInt(<?php echo $powerPresent; ?>);
                                const bpmPresent = !!parseInt(<?php echo $bpmPresent; ?>);

                                //CHART DATA
                                const multiData ={
                                    labels: numbersArray,
                                    datasets: [{
                                        label: '   (m) Altitude ',
                                        display: altitudePresent,
                                        //Styling
                                        backgroundColor: '#4C799E',
                                        borderColor: '#4C799E',
                                        borderWidth: 4,
                                        pointRadius: 0,
                                        data: altitudeArray,
                                        yAxisID: 'y',
                                    },{
                                        label: '   BPM ',
                                        display: bpmPresent,
                                        //Styling
                                        backgroundColor: '#699E42',
                                        borderColor: '#699E42',
                                        borderWidth: 4,
                                        pointRadius: 0,
                                        data: bpmArray,
                                        yAxisID: 'y1',
                                    },{
                                        label: '   (W) Puissance ',
                                        display: powerPresent,
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
                                                display: altitudePresent,
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
                                                display: bpmPresent,
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
                                                display: powerPresent,
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
                $errors[] = 'Veuillez séléctionner un fichier avant de l\'afficher.';
                $_SESSION['errors'] = $errors;
                header('Location:upload.php');
                exit();
            }
        }
        else
        {
            $errors[] = 'Une erreur inconnue s\'est produite.';
            $_SESSION['errors'] = $errors;
            header('Location:upload.php');
            exit();
        }