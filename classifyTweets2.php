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

shuffle($arr);

$X_train = [];
$y_train = [];

// echo floor(count($arr) * 0.8);
$arr_train = [];
// Untuk training
for($i = 1; $i < round(count($arr) * 0.8);  $i++) {	
	array_push($arr_train, $arr[$i]);
	$text = explode(";", $arr[$i]);
	$X_train[] = $text[1];
	$y_train[] = $text[3];
}
$X_train_clean = $X_train;
$y_train_clean = $y_train;

$X_test = [];
$y_test = [];
// Untuk testing
for($i = round(count($arr) * 0.8); $i < count($arr);  $i++) {	
	$text = explode(";", $arr[$i]);
	$X_test[] = $text[1];
	$y_test[] = $text[3];
}

$X_test_clean = $X_test;
$y_test_clean = $y_test;



// Step 2: Prepare the Dataset
$vectorizer = new TokenCountVectorizer(new WordTokenizer());
$vectorizer->fit($X_train);
$vectorizer->transform($X_train);

$vectorizer2 = new TokenCountVectorizer(new WordTokenizer());
$vectorizer2->fit($X_test);
$vectorizer2->transform($X_test);


$tfIdfTransformer = new TfIdfTransformer();
$tfIdfTransformer->fit($X_train);
$tfIdfTransformer->transform($X_train);

$tfIdfTransformer2 = new TfIdfTransformer();
$tfIdfTransformer2->fit($X_test);
$tfIdfTransformer2->transform($X_test);

// print_r($y_train);

$dataset1 = new ArrayDataset($X_train, $y_train);
$split_dataset1 = new RandomSplit($dataset1, 0.0);
$X_train = $split_dataset1->getTrainSamples();
$y_train = $split_dataset1->getTrainLabels();

$dataset2 = new ArrayDataset($X_test, $y_test);
$split_dataset2 = new RandomSplit($dataset2, 0.0);
$X_test = $split_dataset2->getTestSamples();
$y_test = $split_dataset2->getTestLabels();

// Step 4: Train the classifier 
// $distanceMetric = new Euclidean();
// $classifier = new KNearestNeighbors(3, $distanceMetric);
// $classifier->train($X_train, $y_train);

echo count($X_train);
echo '<br>';
echo count($y_train);
echo '<br>';
echo count($X_test);
echo '<br>';
echo count($y_test);
echo '<br>';
echo '<br>';

// print_r($X_test);

// Step 5: Test the classifier accuracy 
// $predictedLabels = $classifier->predict($X_test);
// echo 'Accuracy: '.Accuracy::score($y_test, $predictedLabels);

// for($i = 0; $i <= 8; $i++) {
// 	echo $X_train_clean[$i];
// 	echo '<br>';
// }