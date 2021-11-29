<?php
require "vendor/autoload.php";

use Abraham\TwitterOAuth\TwitterOAuth;

$usertok = "OQHfQVniUJ5Z0siLb1tWGB8Cn";
$usersectok = "8KVrZ7HpDk3WJj5SGXRDnoL6fXSnWfb29flXfn7OPXE9RrXo2Y";
$aptok =  "1454789972368658443-VKp7ccXtcXXDpBFbIzydHGRPfr0aib";
$apsectok = "NLnzuHBmopGeGPLQE4xs45njQ6TMIdd9J5XyP2l3xrm7m";

$connection = new TwitterOAuth(
    $usertok,
    $usersectok,
    $aptok,
    $apsectok
);
$content = $connection->get("account/verify_credentials");
$status = $connection->get("search/tweets", ["q" => "indihome", "count" => "100"]);
$arrays = json_decode(json_encode($status), true);
$data = array();
$i = 0;
$data = array();
foreach ($arrays["statuses"] as $key) {
    $text =  $arrays["statuses"][$i]['text'];
    $user_id = $arrays["statuses"][$i]["user"]["screen_name"];
    $arr_tmp = array("text" => $text, "userid" => $user_id);
    array_push($data, $arr_tmp);
    $i++;
}
echo "<pre>";
print_r($data);
echo "</pre>";