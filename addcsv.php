<?php
$list = array(
    array('\\"Peter\\"', "Griffin", "Oslo", "Norway"),
    array("Glenn", "Quagmire", "Oslo", "Norway")
);

$file = fopen('tweetHome.csv', 'a');  // 'a' for append to file - created if doesn't exit

fputcsv($file, array("\n"), ';');

foreach ($list as $line) {
    fputcsv($file, $line, ';');
}

fclose($file);
