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
use Phpml\Math\Distance\Cosine;
use Phpml\Math\Distance\Euclidean;
use Phpml\ModelManager;
use Phpml\Math\Distance\Dice;
use Phpml\Math\Distance\Jaccard;

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

// Step 2: Preprocessing
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

// echo "<pre>";
// print_r($X_test);
// echo "</pre>";

// Step 4: Train the classifier 
$dice = new Dice();
$classifierDice = new KNearestNeighbors(count($X_train) / 3, $dice);
$classifierDice->train($X_train, $y_train);

$jaccard = new Jaccard();
$classifierJaccard = new KNearestNeighbors(count($X_train) / 3, $jaccard);
$classifierJaccard->train($X_train, $y_train);

$cosine = new Cosine();
$classifierCosine = new KNearestNeighbors(count($X_train) / 3, $cosine);
$classifierCosine->train($X_train, $y_train);

// Step 5: Test the classifier accuracy 
$predictedLabelsDice = $classifierDice->predict($X_test[0]);
$predictedLabelsJaccard = $classifierJaccard->predict($X_test[0]);
$predictedLabelsCosine = $classifierCosine->predict($X_test[0]);
// echo 'Accuracy: ' . Accuracy::score($y_test, $predictedLabels);

// $new = [
// 	"Kualitas film jelek susah dimengerti",
// 	"Menurut gw sejauh ini bagus banget film nya",
// 	"Bagus banget sumpah wajib nonton"
// ];

//save model
// $filepath = "model\\model.test";
// $modelManager = new ModelManager();
// $modelManager->saveToFile($classifier, $filepath);

// retrieve model
// $restoredClassifier = $modelManager->restoreFromFile($filepath);
// $prediction = $restoredClassifier->predict(array_slice($X_test, 0, 1));

echo "<pre>";
print_r($predictedLabelsDice);
echo "</pre>";
echo "<pre>";
print_r($predictedLabelsJaccard);
echo "</pre>";
echo "<pre>";
print_r($predictedLabelsCosine);
echo "</pre>";
