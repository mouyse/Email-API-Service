<?php
declare(strict_types=1);

namespace Src\Config;

/**
 * Class Config
 * Singleton for application configuration management, including environment and database settings.
 */
class Config
{
    /**
     * @var Config|null
     */
    private static ?Config $instance = null;
    /**
     * @var array
     */
    private array $config;

    /**
     * Config constructor. Loads configuration from environment or files.
     */
    private function __construct()
    {
        $this->config = $_ENV; // or load from files
    }

    /**
     * Returns the singleton instance of Config.
     *
     * @return Config
     */
    public static function getInstance(): Config
    {
        if (self::$instance === null) {
            self::$instance = new Config();
        }
        return self::$instance;
    }

    /**
     * Gets a configuration value by key, with optional default.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Returns the database configuration as an array.
     *
     * @return array
     */
    public function getDatabaseConfig(): array
    {
        return [
            'host' => $this->get('DB_HOST', 'localhost'),
            'name' => $this->get('DB_NAME', 'your_database_name'),
            'user' => $this->get('DB_USER', 'your_username'),
            'password' => $this->get('DB_PASSWORD', 'your_password'),
        ];
    }
}
