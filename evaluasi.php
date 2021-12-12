<?php

namespace PhpmlExercise;

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
// echo "<pre>";
// print_r($X_test1);
// echo "</pre>";

// Step 2: Prepare the Dataset
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

$distanceMetric = new Euclidean();
$classifier = new KNearestNeighbors(3, $distanceMetric);
$classifier->train($X_train, $y_train);

// Step 5: Test the classifier accuracy 
$predictedLabels = $classifier->predict($X_test);


// echo "<pre>";
// print_r($X_test);
// echo "</pre>";

// echo "<pre>";
// print_r($predictedLabels);
// echo "</pre>";

// jaccard
for ($i = 0; $i < $total - 1; $i++) {
    $num = 0;
    $denum1 = 0;
    $denum2 = 0;
    $denum3 = 0;
    for ($j = 0; $j < $total; $j++) {
        $Wkq = $sample_data[$total - 1][$j];
        $Wkj = $sample_data[$i][$j];
        $num += $Wkq * $Wkj;
        $denum1 += pow($Wkq, 2);
        $denum2 += pow($Wkj, 2);
        $denum3 = $num;
    }
    $result = $num / ($denum1 + $denum3 - $denum3);
    echo "D" . ($i + 1) . " and Q = " . round($result, 2) . "<br>";
}

// cosine
for ($i = 0; $i < $total - 1; $i++) {
    $num = 0;
    $denum1 = 0;
    $denum2 = 0;
    for ($j = 0; $j < $total; $j++) {
        $Wkq = $sample_data[$total - 1][$j];
        $Wkj = $sample_data[$i][$j];
        $num += $Wkq * $Wkj;
        $denum1 += pow($Wkq, 2);
        $denum2 += pow($Wkj, 2);
    }
    $result = $num / sqrt($denum1 * $denum2);
    echo "D" . ($i + 1) . " and Q = " . round($result, 2) . "<br>";
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


    <!-- result table Euclidean-->
    <div class="container">
        <h1>Euclidean</h1>
        <h2><?= 'Accuracy: ' . Accuracy::score($y_test, $predictedLabels); ?></h2>
        <table class="display" id="dataTableEuclidean">
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
                    $predictedLabels[$i] = str_replace('"', "", $predictedLabels[$i]);
                ?>
                    <tr>
                        <td><?= $X_test1[$i]; ?></td>
                        <td><?= ($y_test[$i] == '1') ? "Positive" : (($y_test[$i] == '0') ? "Negative" : "Neutral") ?></td>
                        <td><?= ($predictedLabels[$i] == '1') ? "Positive" : (($predictedLabels[$i] == '0') ? "Negative" : "Neutral") ?></td>
                        <td><?= ($predictedLabels[$i] == $y_test[$i]) ? "v" : "x" ?></td>
                    </tr>
                <?php
                endfor;
                ?>
            </tbody>
        </table>
        <!-- end of table -->

        <!-- result table Jaccard-->
        <h1>Jaccard</h1>
        <table class="display" id="dataTableJaccard">
            <thead>
                <tr>
                    <th scope="col">Tweets</th>
                    <th scope="col">Sentiment Original</th>
                    <th scope="col">Sentiment Sistem</th>
                    <th scope="col">Valid</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Mark</td>
                    <td>Otto</td>
                    <td>Otto</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Jacob</td>
                    <td>Thornton</td>
                    <td>Otto</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Larry the Bird</td>
                    <td></td>
                    <td>Otto</td>
                </tr>
            </tbody>
        </table>
        <!-- end of table -->

        <!-- result table cosine-->
        <h1>Cosine</h1>
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
                <tr>
                    <td>1</td>
                    <td>Mark</td>
                    <td>Otto</td>
                    <td>Otto</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Jacob</td>
                    <td>Thornton</td>
                    <td>Otto</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Larry the Bird</td>
                    <td></td>
                    <td>Otto</td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- end of table -->
</body>

</html>

<script type="text/javascript">
    $(document).ready(function() {
        $('#dataTableEuclidean').DataTable();
        $('#dataTableJaccard').DataTable();
        $('#dataTableCosine').DataTable();
    });
</script>