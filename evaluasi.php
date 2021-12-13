<?php

namespace PhpmlExercise;

ini_set('max_execution_time', '3000');

use PhpmlExercise\Classification\SentimentAnalysis;
use Phpml\Dataset\CsvDataset;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\WordTokenizer;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\Dataset\ArrayDataset;
use Phpml\CrossValidation\StratifiedRandomSplit;
use Phpml\Metric\Accuracy;
use Phpml\Metric\ClassificationReport;
use Phpml\CrossValidation\RandomSplit;
use Phpml\Classification\KNearestNeighbors;
use Phpml\Math\Distance\Euclidean;
use Phpml\Math\Distance\Asymmetric;
use Phpml\Math\Distance\Overlap;
use Phpml\Math\Distance\Cosine;
use Sastrawi\Stemmer\StemmerFactory;
use Sastrawi\StopWordRemover\StopWordRemoverFactory;

require __DIR__ . '/vendor/autoload.php';

// Step 1: Load the Dataset
$data = file_get_contents('tweet.csv');
$arr = explode("\n", $data);
$label = [];
$sample = [];
for ($i = 1; $i < count($arr); $i++) {
    $text = explode(";", $arr[$i]);
    $label[] = $text[3];
    $sample[] = $text[1];
}


$dataset1 = new ArrayDataset($sample, $label);
$split_dataset1 = new RandomSplit($dataset1, 0.2, 1);
$X_train1 = $split_dataset1->getTrainSamples();
$y_train1 = $split_dataset1->getTrainLabels();
$X_test1  = $split_dataset1->getTestSamples();
$y_test1  = $split_dataset1->getTestLabels();

// Step 2: Prepare the Dataset
$stemmerFactory = new StemmerFactory();
$stemmer = $stemmerFactory->createStemmer();
$stopwordFactory = new StopWordRemoverFactory();
$stopword = $stopwordFactory->createStopWordRemover();
$stemSample = array();
foreach ($sample as $row => $value) {
    $stemSentence = $stemmer->stem($value);
    $stopSentence = $stopword->remove($stemSentence);
    array_push($stemSample, $stopSentence);
}

$vectorizer = new TokenCountVectorizer(new WordTokenizer());
$vectorizer->fit($sample);
$vectorizer->transform($sample);

$tfIdfTransformer = new TfIdfTransformer();
$tfIdfTransformer->fit($sample);
$tfIdfTransformer->transform($sample);

// Step 3: Generate the training/testing Dataset
$dataset = new ArrayDataset($sample, $label);
$split_dataset = new RandomSplit($dataset, 0.2, 1);

$X_train = $split_dataset->getTrainSamples();
$y_train = $split_dataset->getTrainLabels();
$X_test  = $split_dataset->getTestSamples();
$y_test  = $split_dataset->getTestLabels();

//step 4: training
$overlap = new Overlap();
$classifierOverlap = new KNearestNeighbors(count($X_train) / 3, $overlap);
$classifierOverlap->train($X_train, $y_train);

$asymmetric = new Asymmetric();
$classifierAsymmetric = new KNearestNeighbors(count($X_train) / 3, $asymmetric);
$classifierAsymmetric->train($X_train, $y_train);

$cosine = new Cosine();
$classifierCosine = new KNearestNeighbors(count($X_train) / 3, $cosine);
$classifierCosine->train($X_train, $y_train);

// Step 5: predict 
$predictedLabelsOverlap = $classifierOverlap->predict($X_test);
$predictedLabelsAsymmetric = $classifierAsymmetric->predict($X_test);
$predictedLabelsCosine = $classifierCosine->predict($X_test);

//step 6: test the model
$accuracyOverlap = Accuracy::score($y_test, $predictedLabelsOverlap) * 100;
$accuracyAsymmetric = Accuracy::score($y_test, $predictedLabelsAsymmetric) * 100;
$accuracyCosine = Accuracy::score($y_test, $predictedLabelsCosine) * 100;

$validOverlap = 0;
$validAsymmetric = 0;
$validCosine = 0;

for ($i = 0; $i < count($y_test); $i++) {
    if ($predictedLabelsOverlap[$i] == $y_test[$i])  $validOverlap++;
    if ($predictedLabelsAsymmetric[$i] == $y_test[$i]) $validAsymmetric++;
    if ($predictedLabelsCosine[$i] == $y_test[$i]) $validCosine++;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Evaluasi</title>

    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>

</head>
<style>
    .loader {
        border: 16px solid #f3f3f3;
        /* Light grey */
        border-top: 16px solid #3498db;
        /* Blue */
        border-radius: 50%;
        width: 120px;
        height: 120px;
        animation: spin 2s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Project IIR</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="">Evaluasi</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- End of navigation -->

    <div class="loader" id="loader" style="display: none;"></div>

    <!-- result table Overlap-->
    <div class="container">
        <h1>Overlap</h1>
        <h2><?= 'Accuracy: ' . round($accuracyOverlap, 2) . '%'; ?></h2>
        <h3>Jumlah Valid : <?= $validOverlap ?></h3>
        <table class="display" id="dataTableOverlap">
            <thead>
                <tr>
                    <th scope="col">Tweets</th>
                    <th scope="col">Sentiment Original</th>
                    <th scope="col">Sentiment Sistem</th>
                    <th scope="col">Valid</th>
                </tr>
            </thead>
            <tbody>
                <?php
                for ($i = 0; $i < count($y_test); $i++) :
                    $y_test[$i] = str_replace('"', "", $y_test[$i]);
                    $predictedLabelsOverlap[$i] = str_replace('"', "", $predictedLabelsOverlap[$i]);
                ?>
                    <tr>
                        <td><?= $X_test1[$i]; ?></td>
                        <td><?= ($y_test[$i] == '1') ? "Positive" : (($y_test[$i] == '0') ? "Negative" : "Neutral") ?></td>
                        <td><?= ($predictedLabelsOverlap[$i] == '1') ? "Positive" : (($predictedLabelsOverlap[$i] == '0') ? "Negative" : "Neutral") ?></td>
                        <td><?= ($predictedLabelsOverlap[$i] == $y_test[$i]) ? "v" : "x" ?></td>
                    </tr>
                <?php
                endfor;
                ?>
            </tbody>
        </table>
        <!-- end of table -->

        <!-- result table Asymmetric-->
        <h1>Asymmetric</h1>
        <h2><?= 'Accuracy: ' . round($accuracyAsymmetric, 2) . '%'; ?></h2>
        <h3>Jumlah Valid : <?= $validAsymmetric ?></h3>
        <table class="display" id="dataTableAsymmetric">
            <thead>
                <tr>
                    <th scope="col">Tweets</th>
                    <th scope="col">Sentiment Original</th>
                    <th scope="col">Sentiment Sistem</th>
                    <th scope="col">Valid</th>
                </tr>
            </thead>
            <tbody>
                <?php
                for ($i = 0; $i < count($y_test); $i++) :
                    $y_test[$i] = str_replace('"', "", $y_test[$i]);
                    $predictedLabelsAsymmetric[$i] = str_replace('"', "", $predictedLabelsAsymmetric[$i]);
                ?>
                    <tr>
                        <td><?= $X_test1[$i]; ?></td>
                        <td><?= ($y_test[$i] == '1') ? "Positive" : (($y_test[$i] == '0') ? "Negative" : "Neutral") ?></td>
                        <td><?= ($predictedLabelsAsymmetric[$i] == '1') ? "Positive" : (($predictedLabelsAsymmetric[$i] == '0') ? "Negative" : "Neutral") ?></td>
                        <td><?= ($predictedLabelsAsymmetric[$i] == $y_test[$i]) ? "v" : "x" ?></td>
                    </tr>
                <?php
                endfor;
                ?>
            </tbody>
        </table>
        <!-- end of table -->

        <!-- result table Cosine-->
        <h1>Cosine</h1>
        <h2><?= 'Accuracy : ' . round($accuracyCosine, 2) . '%'; ?></h2>
        <h3>Jumlah Valid : <?= $validCosine ?></h3>
        <table class="display" id="dataTableCosine">
            <thead>
                <tr>
                    <th scope="col">Tweets</th>
                    <th scope="col">Sentiment Original</th>
                    <th scope="col">Sentiment Sistem</th>
                    <th scope="col">Valid</th>
                </tr>
            </thead>
            <tbody>
                <?php
                for ($i = 0; $i < count($y_test); $i++) :
                    $y_test[$i] = str_replace('"', "", $y_test[$i]);
                    $predictedLabelsCosine[$i] = str_replace('"', "", $predictedLabelsCosine[$i]);
                ?>
                    <tr>
                        <td><?= $X_test1[$i]; ?></td>
                        <td><?= ($y_test[$i] == '1') ? "Positive" : (($y_test[$i] == '0') ? "Negative" : "Neutral") ?></td>
                        <td><?= ($predictedLabelsCosine[$i] == '1') ? "Positive" : (($predictedLabelsCosine[$i] == '0') ? "Negative" : "Neutral") ?></td>
                        <td><?= ($predictedLabelsCosine[$i] == $y_test[$i]) ? "v" : "x" ?></td>
                    </tr>
                <?php
                endfor;
                ?>
            </tbody>
        </table>
    </div>
    <!-- end of table -->
</body>

</html>

<script type="text/javascript">
    $(document).ready(function() {
        $('#loader').hide();
        $('#dataTableOverlap').DataTable();
        $('#dataTableAsymmetric').DataTable();
        $('#dataTableCosine').DataTable();
    });
</script>