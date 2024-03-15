<?php

namespace Dockware\Setup;

require_once '/var/www/html/vendor/autoload.php';

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\FetchMode;

class CurrencySwitcher
{

    /**
     * @var DbConnection
     */
    private $db;

    /**
     *
     */
    public function __construct()
    {
        $this->db = new DbConnection();
    }

    /**
     * @param string $newCurrency
     * @return void
     */
    public function updateCurrency(string $newCurrency)
    {
        $row = $this->db->executeQuery("SELECT factor FROM currency WHERE iso_code = '" . $newCurrency . "'")->fetch();
        $factor = $row['factor'];

        if ((double)$factor === 1.0) {
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

        $this->db->executeQuery($sql);
    }
}