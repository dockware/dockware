<?php

require_once '/var/www/html/vendor/autoload.php';

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\FetchMode;

$newCurrency = $argv[1];

# ----------------------------------------------------------------------------
$connection = DriverManager::getConnection(
[
    "driver" => "pdo_mysql",
    "host" => "localhost",
    "port" => 3306,
    "user" => "root",
    "password" => "root",
    "dbname" => "shopware",
    "charset" => "utf8mb4"
]
);

$connection->executeQuery('USE `shopware`');
# ----------------------------------------------------------------------------

$row = $connection->executeQuery("SELECT factor FROM currency WHERE iso_code = '" . $newCurrency . "'")->fetch();
$factor = $row['factor'];

if ((double)$factor === 1.0)
{
    # already default
    return;
}

$sql = "
START TRANSACTION;

SET @defaultID = (SELECT id FROM currency WHERE iso_code = 'EUR');
SET @otherID = (SELECT id FROM currency WHERE iso_code = '" . $newCurrency . "');
UPDATE currency SET id = 'temp' WHERE iso_code = 'EUR';
UPDATE currency SET id = @defaultID WHERE iso_code = '" . $newCurrency . "';
UPDATE currency SET id = @otherID WHERE iso_code = 'EUR';

SET @fixFactor = (SELECT 1/factor FROM currency WHERE iso_code = '" . $newCurrency . "');
UPDATE currency SET factor = IF(iso_code = '" . $newCurrency . "', 1, factor * @fixFactor);

COMMIT;
";

$connection->executeQuery($sql);
