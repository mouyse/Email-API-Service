<?php
namespace Src\Database;

class PdoFactory
{
    public static function create(array $config): \PDO
    {
        $dsn = "mysql:host={$config['host']};dbname={$config['name']}";
        $pdo = new \PDO($dsn, $config['user'], $config['password']);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }
} 