<?php

namespace Alexanderurakov\BookLibraryApi\Config;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {

        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        // Загрузка переменных окружения для подключения к базе данных
        $this->host = $_ENV['DB_HOST'];
        $this->db_name = $_ENV['DB_NAME'];
        $this->username = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASS'];

        $this->conn = null;

        // Попытка установить соединение с базой данных
        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->db_name}", $this->username, $this->password);
            // Установка режима обработки ошибок
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            // Обработка ошибок соединения
            echo "Connection error: " . $exception->getMessage();
        }
    }
}