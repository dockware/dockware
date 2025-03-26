<?php

require_once '/var/www/html/vendor/autoload.php';

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\FetchMode;

$newAccessKey = $argv[1];

/**
 * So Shopware can work with the new sales channel api key, it needs the prefix swsc
 * Check if the prefix was provided, otherwise set it
 */
$identifier = mb_substr($newAccessKey, 0, 4);
if ($identifier != 'SWSC')
{
    $newAccessKey = 'SWSC' . $newAccessKey;
}

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

$sql = "
START TRANSACTION;

UPDATE sales_channel
LEFT JOIN sales_channel_domain
ON sales_channel.id = sales_channel_domain.sales_channel_id
SET access_key = '" . $newAccessKey . "'
WHERE sales_channel_domain.url = 'http://localhost';

COMMIT;
";

$connection->executeQuery($sql);
echo $newAccessKey;
