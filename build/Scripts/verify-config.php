<?php

$swImage = $argv[1];
$swTag = $argv[2];


if (!isSVRUnitValid($swImage, $swTag)) {
    echo "SVRUNIT XML File does not exist." . PHP_EOL;
    exit(1);
} else {
    echo "* SVRUnit File is existing" . PHP_EOL;
}


$layerCount = getLayersCount($swImage, $swTag);


echo "* Docker Layers: " . $layerCount . PHP_EOL;
echo "========================================" . PHP_EOL;

#if ($layerCount >= 127) {
#    echo "DOCKER LAYER COUNT IS TOO HIGH: " . $layerCount . PHP_EOL;
#    exit(1);
#}

echo "Configuration Valid!" . PHP_EOL;


/**
 * @param $swImage
 * @param $swTag
 * @return bool
 */
function isSVRUnitValid($swImage, $swTag)
{
    $svrUnitFile = __DIR__ . '/../../tests/svrunit/suites/' . $swImage . '/' . $swTag . '.xml';

    return file_exists($svrUnitFile);
}

/**
 * @param $swImage
 * @param $swTag
 * @return int
 */
function getLayersCount($swImage, $swTag)
{
    $dockerFile = __DIR__ . '/../../dist/images/' . $swImage . '/' . $swTag . '/Dockerfile';
    $dockerFileContent = file_get_contents($dockerFile);

    $runs = substr_count($dockerFileContent, "RUN ");
    $adds = substr_count($dockerFileContent, "ADD ");
    $copy = substr_count($dockerFileContent, "COPY ");

    return $runs + $adds + $copy;
}
