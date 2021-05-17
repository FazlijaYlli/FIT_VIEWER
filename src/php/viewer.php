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

/////////////////////////////
/// VALIDATION DE DONNEES ///
/////////////////////////////

// Si l'utilisateur vient de la page upload.php, en utilisant le bouton submit
if(isset($_POST['inputSubmit']) && isset($_FILES['inputFile'])) {
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
            $pFFA = new adriangibbons\phpFITFileAnalysis($_FILES['inputFile']['tmp_name'], $options);
            foreach($pFFA->data_mesgs['record']['timestamp'] as $timestamp)
            {
                echo date('d-m-Y_H:i:s', $timestamp);
                echo '<br>';
            }
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
?>
