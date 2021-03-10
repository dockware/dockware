<?php

if (count($argv) < 2) {
    throw new Exception('you have to provide the image name you want to build');
}


$image = $argv[1];
$tag = null;
$tags = scandir(__DIR__ . '/../dist/images/' . $image);

if (count($argv) === 3) {
    $tag = $argv[2];
    if (trim($tag) !== '') {
        $tags = [];
        $tags[] = $tag;
    }
}

