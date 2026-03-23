<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $config = config('database');
        $dsn = sprintf(
            '%s:host=%s;port=%d;dbname=%s;charset=%s',
            $config['driver'],
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset']
        );

        try {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false,
            ];

            if (defined('PDO::MYSQL_ATTR_MULTI_STATEMENTS')) {
                $options[PDO::MYSQL_ATTR_MULTI_STATEMENTS] = false;
            }

            self::$connection = new PDO($dsn, $config['username'], $config['password'], $options);
        } catch (PDOException $e) {
            report_exception($e);
            http_response_code(500);
            echo 'Erro interno ao conectar com o banco de dados.';
            exit;
        }

        return self::$connection;
    }
}
