<?php

use NunoMaduro\Collision\Provider;
use RWC\TwitterStream\Rule;
use RWC\TwitterStream\RuleBuilder;
use RWC\TwitterStream\TwitterStream;

require __DIR__ . '/vendor/autoload.php';

(new Provider)->register();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$bearerToken = $_ENV['TWITTER_BEARER_TOKEN'];
$apiKey = $_ENV['TWITTER_APIKEY'];
$apiSecretKey = $_ENV['TWITTER_SECRET_APIKEY'];

$twitterStream = new TwitterStream(
    $bearerToken,
    $apiKey,
    $apiSecretKey
);

Rule::deleteBulk(...Rule::all());
RuleBuilder::create('cats')
    ->hasImages()
    ->save();

foreach ($twitterStream->filteredTweets() as $tweet) {
    dump($tweet);
}