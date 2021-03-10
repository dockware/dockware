<?php


include(__DIR__.'/_variables.inc.php');

$sh = '';
foreach ($tags as $tag) {
    if ($tag === '.' || $tag === '..') {
        continue;
    }

    $make = 'make build image=' . $image . ' tag=' . $tag;
    $sh .= $make . "\n";
}

$test = $make = 'make test image=' . $image;
$sh .= $test;

file_put_contents('dist/build_' . $image . '.sh', $sh);



