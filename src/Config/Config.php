<?php
namespace Src\Config;

class Config
{
    private static ?Config $instance = null;
    private array $config;

    private function __construct()
    {
        $this->config = $_ENV; // or load from files
    }

    public static function getInstance(): Config
    {
        if (self::$instance === null) {
            self::$instance = new Config();
        }
        return self::$instance;
    }

    public function get(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    // Optionally, add helpers for specific config groups
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
