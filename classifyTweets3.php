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
// print_r($arr);
for($i = 1; $i < count($arr);  $i++) {	
	$text = explode(";", $arr[$i]);
	$label[] = $text[3];
	$konten[] = $text[1];
	$index[] = $text[0];
}



$vectorizer = new TokenCountVectorizer(new WordTokenizer());
$vectorizer->fit($konten);
$vectorizer->transform($konten);

$tfIdfTransformer = new TfIdfTransformer();
$tfIdfTransformer->fit($konten);
$tfIdfTransformer->transform($konten);

$combined = array_combine($index, $konten);
// for($i = 1; $i < count($sample);  $i++) {	
// 	$combined=array("index"=>$sample["index"][$i],"kontent"=>$sample["konten"][$i]);
// }

// echo '<pre>';
// print_r($combined);
// echo '<pre>';


// Step 2: Generate the training/testing Dataset
$dataset = new ArrayDataset($combined, $label);

$split_dataset = new RandomSplit($dataset, 0.2);

$X_train = $split_dataset->getTrainSamples();
$y_train = $split_dataset->getTrainLabels();
$X_test  = $split_dataset->getTestSamples();
$y_test  = $split_dataset->getTestLabels();

// echo '<pre>';
// print_r($X_train);
// echo '<pre>';

// // Step 3: Preprocessing


// // Step 4: Train the classifier 
// $distanceMetric = new Euclidean();
// $classifier = new KNearestNeighbors(3, $distanceMetric);
// $classifier->train($X_train, $y_train);

// echo count($X_train);
// echo '<br>';
// echo count($y_train);
// echo '<br>';
// echo count($X_test);
// echo '<br>';
// echo count($y_test);
// echo '<br>';
// echo '<br>';

// Step 5: Test the classifier accuracy 
// $predictedLabels = $classifier->predict($X_test);
// echo 'Accuracy: '.Accuracy::score($y_test, $predictedLabels);

