<?php

namespace utils;

use PDO;

class DatabaseConnector
{
    private static ?DatabaseConnector $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $host = 'db';
        $db = 'ehealth';
        $user = 'user';
        $pass = 'password';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public static function getInstance(): DatabaseConnector
    {
        if (self::$instance === null) {
            self::$instance = new DatabaseConnector();
        }

        return self::$instance;
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}