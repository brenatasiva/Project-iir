<?php
ini_set('max_execution_time', '3000');
require "vendor/autoload.php";
require "classifyTweets.php";

use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\Tokenization\WordTokenizer;
use Phpml\Math\Distance\Dice;
use Phpml\Math\Distance\Jaccard;
use Phpml\Math\Distance\Cosine;
use Phpml\Classification\KNearestNeighbors;
use Phpml\ModelManager;
use Abraham\TwitterOAuth\TwitterOAuth;



// Step 1: Load the Dataset for training the model
$data = file_get_contents('tweet.csv');
$arr = explode("\n", $data);
$label = [];
$sample = [];
for ($i = 1; $i < count($arr); $i++) {
    $text = explode(";", $arr[$i]);
    $label[] = $text[3];
    $sample[] = $text[1];
}

// Step 2: Preprocessing
$vectorizer = new TokenCountVectorizer(new WordTokenizer());
$vectorizer->fit($sample);
$vectorizer->transform($sample);

$tfIdfTransformer = new TfIdfTransformer();
$tfIdfTransformer->fit($sample);
$tfIdfTransformer->transform($sample);

// Step 3: Train the classifier 
if ($_POST['method'] == 'Dice')
    $distanceMetric = new Dice();
else if ($_POST['method'] == 'Jaccard')
    $distanceMetric = new Jaccard();
else if ($_POST['method'] == 'Cosine')
    $distanceMetric = new Cosine();

$classifier = new KNearestNeighbors(count($sample) / 3, $distanceMetric);
$classifier->train($sample, $label);

//step 4: crawl the data
$usertok = "OQHfQVniUJ5Z0siLb1tWGB8Cn";
$usersectok = "8KVrZ7HpDk3WJj5SGXRDnoL6fXSnWfb29flXfn7OPXE9RrXo2Y";
$aptok =  "1454789972368658443-VKp7ccXtcXXDpBFbIzydHGRPfr0aib";
$apsectok = "NLnzuHBmopGeGPLQE4xs45njQ6TMIdd9J5XyP2l3xrm7m";
$keyword = $_POST['keyword'];
echo $keyword;
echo $_POST['method'];

$connection = new TwitterOAuth(
    $usertok,
    $usersectok,
    $aptok,
    $apsectok
);
$content = $connection->get("account/verify_credentials");
$status = $connection->get("search/tweets", ["q" => $keyword, "count" => "100"]);
$arrays = json_decode(json_encode($status), true);
$data = array();
$i = 0;
$tweet = array();
foreach ($arrays["statuses"] as $key) {
    $text =  $arrays["statuses"][$i]['text'];
    $user_id = $arrays["statuses"][$i]["user"]["screen_name"];
    $arr_tmp = array("text" => $text, "userid" => $user_id);
    array_push($data, $arr_tmp);
    array_push($tweet, $text);
    $i++;
}


// Step 5: pre process crawled data 
$vectorizer->transform($tweet);
$tfIdfTransformer->transform($tweet);

//step 6: analyze sentiment label
$predictedLabels = $classifier->predict($tweet);


for($i = 0; $i < count($data); $i++) {
    $data[$i]['predictedLabels'] = $predictedLabels[$i];
}

echo "<pre>";
print_r($data);
echo "</pre>";


// $result = array("predictedLabel" => $predictedLabels, "crawl" => $data);

// echo json_encode($result);