<?php

namespace Infra\Database;

class MySQLConnection extends PDODatabase
{
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new \PDO(
                'mysql:host=' . DB_HOST . ';' . 'dbname=' . DB_NAME,
                DB_USER,
                DB_PASS,
                [
                    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
                ]
            );
        }

        return self::$instance;
    }
}
