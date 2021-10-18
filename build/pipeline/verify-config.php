<?php

$swImage = $argv[1];
$swTag = $argv[2];


$svrUnitFile = __DIR__ . '/../../tests/svrunit/suites/' . $swImage . '/' . $swTag . '.xml';


if (!file_exists($svrUnitFile)) {
    echo "SVRUNIT XML File does not exist: " . $svrUnitFile . PHP_EOL;
    exit(1);
}


echo "Configuration Valid!" . PHP_EOL;
