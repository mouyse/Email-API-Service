<?php
declare(strict_types=1);

namespace Src\Database;

/**
 * Class PdoFactory
 * Factory for creating and configuring PDO instances for database connections.
 */
class PdoFactory
{
    /**
     * Creates and configures a PDO instance.
     *
     * @param array $config
     * @return \PDO
     */
    public static function create(array $config): \PDO
    {
        $dsn = "mysql:host={$config['host']};dbname={$config['name']}";
        $pdo = new \PDO($dsn, $config['user'], $config['password']);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }
} 