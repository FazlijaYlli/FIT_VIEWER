<?php
include('../../vendor/adriangibbons/php-fit-file-analysis/src/phpFITFileAnalysis.php');  // this file is in the project's root folder
$options = [
    'units'                   => 'metric',
    'pace'                    => false,
];
$pFFA = new adriangibbons\phpFITFileAnalysis('../../resources/fit-files/1.fit', $options);
?>

<html lang="fr-fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Page de debug.</title>
</head>
    <body>
        <?php include "header.php" ?>
        <h1>Bienvenue sur FIT File Viewer.</h1>
        <pre>
        <?php

        // DATES
        date_default_timezone_set('Europe/Paris');
        foreach($pFFA->data_mesgs['record']['timestamp'] as $timestamp)
        {
            echo date('d-m-Y_H:i:s', $timestamp);
            echo '<br>';
        }

        //STATISTIQUES
        //print_r($pFFA->data_mesgs['record']['distance']);
        //print_r($pFFA->data_mesgs['record']['speed']);
        //print_r($pFFA->data_mesgs['record']['altitude']);
        //print_r($pFFA->data_mesgs['record']['timestamp']);

        //EXEMPLES
        //echo "Max Speed: ".max($pFFA->data_mesgs['record']['speed'])."<br>";
        //echo "Average Speed: ".(array_sum($pFFA->data_mesgs['record']['speed']) / count($pFFA->data_mesgs['record']['speed']))."<br>";
        //echo "Min Speed: ".min($pFFA->data_mesgs['record']['speed'])."<br>";

        include "footer.php"
        ?>
        </pre>
    </body>
</html>
