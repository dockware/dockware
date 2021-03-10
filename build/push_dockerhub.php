<?php

include(__DIR__.'/_variables.inc.php');


$sh = '';
foreach ($tags as $tag) {
    if ($tag === '.' || $tag === '..') {
        continue;
    }

    $dockerpushCommand = 'docker push dockware/' . $image . ':' . $tag;
    passthru($dockerpushCommand, $returnValue);
}




