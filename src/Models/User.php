<?php

namespace Alexanderurakov\BookLibraryApi\Models;

use Alexanderurakov\BookLibraryApi\Config\Database;
use PDO;
use PDOException;

class User {
    private $conn;
    private $table_name = "users";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conn;
    }

    /**
     * Проверяет, существует ли пользователь с данным именем.
     *
     * @param string $username
     * @return bool
     */
    public function userExists($username) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Создает нового пользователя.
     *
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function create($username, $password) {
        if ($this->userExists($username)) {
            echo "Username already exists.";
            return false;
        }

        $query = "INSERT INTO " . $this->table_name . " (username, password) VALUES (:username, :password)";
        $stmt = $this->conn->prepare($query);

        // Закодировать пароль
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password_hash);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Логирование ошибки
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Проверяет имя пользователя и пароль для входа.
     *
     * @param string $username
     * @param string $password
     * @return mixed
     */
    public function login($username, $password) {
        $query = "SELECT id, password FROM " . $this->table_name . " WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password'])) {
                return $row['id'];
            }
        }
        return false;
    }

    /**
     * Получает список всех пользователей.
     *
     * @return array
     */
    public function getAllUsers() {
        $query = "SELECT id, username FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);

        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Логирование ошибки
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    /**
     * Возвращает ID пользователя по его имени.
     *
     * @param string $username
     * @return mixed ID пользователя или false, если пользователь не найден
     */
    public function getIdByUsername($username) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id'] : false;
    }
}