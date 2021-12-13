<?php
ini_set('max_execution_time', '3000');
require "vendor/autoload.php";
// require "classifyTweets.php";

use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\Tokenization\WordTokenizer;
use Phpml\Math\Distance\Overlap;
use Phpml\Math\Distance\Asymmetric;
use Phpml\Math\Distance\Cosine;
use Phpml\Classification\KNearestNeighbors;
use Phpml\ModelManager;
use Abraham\TwitterOAuth\TwitterOAuth;
use Sastrawi\Stemmer\StemmerFactory;
use Sastrawi\StopWordRemover\StopWordRemoverFactory;



// Step 1: Load the Dataset for training the model
$data = file_get_contents('tweetHome.csv');
$arr = explode("\n", $data);
$label = [];
$sample = [];
$lastIdx = "";
for ($i = 1; $i < count($arr) - 1; $i++) {
    $text = explode(";", $arr[$i]);
    $label[] = $text[3];
    $sample[] = $text[1];
    $lastIdx = $text[0];
}

// Step 2: Preprocessing
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
$vectorizer->fit($stemSample);
$vectorizer->transform($stemSample);

$tfIdfTransformer = new TfIdfTransformer();
$tfIdfTransformer->fit($stemSample);
$tfIdfTransformer->transform($stemSample);

// Step 3: Train the classifier 
if ($_POST['method'] == 'Overlap')
    $distanceMetric = new Overlap();
else if ($_POST['method'] == 'Asymmetric')
    $distanceMetric = new Asymmetric();
else if ($_POST['method'] == 'Cosine')
    $distanceMetric = new Cosine();

$classifier = new KNearestNeighbors(count($stemSample) / 3, $distanceMetric);
$classifier->train($stemSample, $label);

//step 4: crawl the data
$usertok = "OQHfQVniUJ5Z0siLb1tWGB8Cn";
$usersectok = "8KVrZ7HpDk3WJj5SGXRDnoL6fXSnWfb29flXfn7OPXE9RrXo2Y";
$aptok =  "1454789972368658443-VKp7ccXtcXXDpBFbIzydHGRPfr0aib";
$apsectok = "NLnzuHBmopGeGPLQE4xs45njQ6TMIdd9J5XyP2l3xrm7m";
$keyword = $_POST['keyword'];
// echo $keyword;
// echo $_POST['method'];

$connection = new TwitterOAuth(
    $usertok,
    $usersectok,
    $aptok,
    $apsectok
);
$content = $connection->get("account/verify_credentials");
$status = $connection->get("search/tweets", ["q" => $keyword, "count" => "5"]);
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
$stemTweet = array();
foreach ($tweet as $row => $value) {
    $stemSentence = $stemmer->stem($value);
    $stopSentence = $stopword->remove($stemSentence);
    array_push($stemTweet, $stopSentence);
}


$vectorizer->transform($stemTweet);
$tfIdfTransformer->transform($stemTweet);

//step 6: analyze sentiment label
$predictedLabels = $classifier->predict($stemTweet);

// echo "<pre>";
// print_r($predictedLabels);
// echo "</pre>";

//step 7:input crawled data to csv
$list = array();
$lastIdx = str_replace('"', "", $lastIdx);
// echo "<pre>";
// print_r($data);
// echo "</pre>";

for ($i = 0; $i < count($data); $i++) {
    $data[$i]['text'] = str_replace(array("\r", "\n"), "", $data[$i]['text']);
    $predictedLabels[$i] = str_replace(array("\r", "\n"), "", $predictedLabels[$i]);
    $predictedLabels[$i] = str_replace('"', "", $predictedLabels[$i]);
    $list[] = array((intval($lastIdx) + $i + 1), $data[$i]['text'], $data[$i]['userid'], $predictedLabels[$i]);
}

// echo "<pre>";
// print_r($list);
// echo "</pre>";

$file = fopen('tweetHome.csv', 'a');  // 'a' for append to file - created if doesn't exit

foreach ($list as $line) {
    fputcsv($file, $line, ';');
}

fclose($file);


for ($i = 0; $i < count($stemTweet); $i++) {
    $predictedLabels[$i] = str_replace('"', "", $predictedLabels[$i]);
    $result[] = array("predictedLabel" => $predictedLabels[$i], "crawl" => $data[$i]);
}
echo json_encode($result);
