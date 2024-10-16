<?php

namespace Alexanderurakov\BookLibraryApi\Controllers;

use Alexanderurakov\BookLibraryApi\Config\Database;
use PDO;
use Exception;

class UserController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->conn;
    }

    /**
     * Получает всех пользователей и возвращает их в формате JSON.
     *
     * Пример JSON ответа
     * {
     * "id":1,
     * "username":"new_user"
     * }
     * @return void
     */
    public function getAllUsers(): void
    {
        try {
            $query = "SELECT id, username FROM users";
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($users);
        } catch (Exception $e) {
            echo json_encode([
                "message" => "Unable to retrieve users.",
                "error" => $e->getMessage()
            ]);
        }
    }
}