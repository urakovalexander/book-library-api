<?php

namespace Alexanderurakov\BookLibraryApi\Models;

use Alexanderurakov\BookLibraryApi\Config\Database;
use PDO;
use PDOException;

/**
 * @OA\Schema(
 *   schema="Token",
 *   type="object",
 *   description="Токен пользователя",
 *   @OA\Property(property="user_id", type="integer", description="ID пользователя, которому принадлежит токен"),
 *   @OA\Property(property="token", type="string", description="JWT токен"),
 *   @OA\Property(property="created_at", type="string", format="date-time", description="Дата создания токена"),
 *   @OA\Property(property="updated_at", type="string", format="date-time", description="Дата обновления токена")
 * )
 */

class Token {
    private PDO $conn;
    private string $table_name = "tokens";

    public function __construct() {
        $this->conn = Database::getInstance();
    }


    /**
     * Сохраняет токен для пользователя.
     *
     * @param int $userId ID пользователя.
     * @param string $token Токен.
     * @param string $expiresAt Дата и время истечения токена.
     * @return bool Возвращает true, если токен успешно сохранен, иначе false.
     */
    public function saveToken(int $userId, string $token, string $expiresAt): bool {
        $query = "INSERT INTO $this->table_name (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expires_at', $expiresAt);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Удаляет токен пользователя.
     *
     * @param int $userId ID пользователя.
     * @return bool Возвращает true, если токен успешно удален, иначе false.
     */
    public function deleteToken(int $userId): bool {
        $query = "DELETE FROM $this->table_name WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Проверяет, существует ли токен для пользователя.
     *
     * @param int $userId ID пользователя.
     * @return bool Возвращает true, если токен существует, иначе false.
     */
    public function tokenExists(int $userId): bool {
        $query = "SELECT token FROM $this->table_name WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}