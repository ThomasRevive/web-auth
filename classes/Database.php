<?php

class Database {
    private static $instance = null;
    private $pdo;

    public function __construct() {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $this->pdo = new PDO("mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']}", $_ENV['DB_USER'], $_ENV['DB_PASS']);
    }

    // Prevent our singleton from being cloned or restorable from strings
    protected function __clone()
    {
    }
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    public static function getInstance() {
        if (empty(self::$instance)) {
            self::$instance = new Database();
        }

        return self::$instance->pdo;
    }
}