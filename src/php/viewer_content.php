<?php
if(!isset($_FILES['inputFile']))
{
    if(!isset($_SESSION['errors']))
    {
        $_SESSION['errors'] = array();
    }
    $errors = $_SESSION['errors'];
    $errors[] = "Une erreur inconnue s'est produite";
    $_SESSION['errors'] = $errors;
    header('Location:upload.php');
    exit();
}
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
    <a href="upload.php" class="blue-button" style="margin-top: 126px">Revenir à l'accueil</a>
    <div class="title" style="margin-top: 10px"><?php echo pathinfo($_FILES['inputFile']['name'])['basename'] ?></div>
    <div class="general-info-group">
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

<div id="multiChart" class="chart">
    <canvas id="multiGraph"></canvas>
</div>

<div class="card-group">
    <div class="card">
        <div class="primary-text" style="margin-bottom: 20px">
            Altitude
        </div>
        <div class="card-info-group">
            <div class="card-info secondary-text" style="margin-bottom: 20px">
                Minimum
            </div>
            <div class="card-info secondary-text" style="margin-bottom: 20px">
                Moyenne
            </div>
            <div class="card-info secondary-text" style="margin-bottom: 20px">
                Maximum
            </div>
            <?php
            if(isset($pFFA->data_mesgs['record']['altitude']))
            {
                // Affichage de l'élévation durant le parcours.
                // Affichage de l'altitude minimum, moyennne, et maximum.
                $altitude = $pFFA->data_mesgs['record']['altitude'];
                $max = max($altitude)." m";
                $moy = floor((array_sum($altitude) / count($altitude)))." m";
                $min = min($altitude)."  m";
                echo '
                                            <div class="card-info primary-text">
                                                '.$min.'
                                            </div>
                                            <div class="card-info primary-text">
                                                '.$moy.'
                                            </div>
                                            <div class="card-info primary-text">
                                                '.$max.'
                                            </div>
                                        ';
            }
            else
            {
                echo 'L\'altitude n\'est pas indiquée dans ce fichier ! <br>';
            }
            ?>
        </div>
    </div>

    <div class="card" style="background-color: #699E42">
        <div class="primary-text" style="margin-bottom: 20px">
            BPM
        </div>
        <div class="card-info-group">
            <div class="card-info secondary-text" style="margin-bottom: 20px">
                Minimum
            </div>
            <div class="card-info secondary-text" style="margin-bottom: 20px">
                Moyenne
            </div>
            <div class="card-info secondary-text" style="margin-bottom: 20px">
                Maximum
            </div>
            <?php
            if(isset($pFFA->data_mesgs['record']['heart_rate']))
            {
                $heart_rate = $pFFA->data_mesgs['record']['heart_rate'];
                $max = max($heart_rate)." BPM";
                $moy = floor((array_sum($heart_rate) / count($heart_rate)))." BPM";
                $min = min($heart_rate)."  BPM";
                echo '
                                            <div class="card-info primary-text">
                                                '.$min.'
                                            </div>
                                            <div class="card-info primary-text">
                                                '.$moy.'
                                            </div>
                                            <div class="card-info primary-text">
                                                '.$max.'
                                            </div>
                                        ';
            }
            else
            {
                echo 'Les battements par minutes ne sont pas indiqués dans ce fichier ! <br>';
            }
            ?>
        </div>
    </div>

    <div class="card" style="background-color: #cd3b3b">
        <div class="primary-text" style="margin-bottom: 20px">
            Puissance
        </div>
        <div class="card-info-group">
            <div class="card-info secondary-text" style="margin-bottom: 20px">
                Minimum
            </div>
            <div class="card-info secondary-text" style="margin-bottom: 20px">
                Moyenne
            </div>
            <div class="card-info secondary-text" style="margin-bottom: 20px">
                Maximum
            </div>
            <?php
            if(isset($pFFA->data_mesgs['record']['power']))
            {
                $power = $pFFA->data_mesgs['record']['power'];
                $max = max($power)." W";
                $moy = floor((array_sum($power) / count($power)))." W";
                $min = min($power)."  W";
                echo '
                                            <div class="card-info primary-text">
                                                '.$min.'
                                            </div>
                                            <div class="card-info primary-text">
                                                '.$moy.'
                                            </div>
                                            <div class="card-info primary-text">
                                                '.$max.'
                                            </div>
                                        ';
            }
            else
            {
                echo 'La puissance n\'est pas indiquée dans ce fichier ! <br>';
            }
            ?>
        </div>
    </div>
</div>