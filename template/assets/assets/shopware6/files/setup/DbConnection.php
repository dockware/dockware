<?php

namespace Dockware\Setup;

require_once '/var/www/html/vendor/autoload.php';

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\FetchMode;


class DbConnection
{

    /**
     * @return Connection
     */
    public function connect()
    {
        $connString = "mysql://{{ db.user }}:{{ db.pwd }}@{{ db.host }}:{{ db.port }}/{{ db.database }}";

        $params = [
            'url' => $connString,
            'charset' => 'utf8mb4',
        ];

        $connection = DriverManager::getConnection($params, new Configuration());

        $connection->executeQuery('USE `{{ db.database }}`');

        return $connection;
    }

}