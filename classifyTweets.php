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
for($i = 1; $i < count($arr);  $i++) {	
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

echo "<pre>";
print_r($y_train1);
echo "</pre>";

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

echo "<pre>";
print_r($y_train);
echo "</pre>";

// Step 4: Train the classifier 
$distanceMetric = new Euclidean();
$classifier = new KNearestNeighbors(3, $distanceMetric);
$classifier->train($X_train, $y_train);

// Step 5: Test the classifier accuracy 
$predictedLabels = $classifier->predict($X_test);
echo 'Accuracy: '.Accuracy::score($y_test, $predictedLabels);

