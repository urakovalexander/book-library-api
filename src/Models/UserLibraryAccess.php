<?php

namespace Alexanderurakov\BookLibraryApi\Models;

use Alexanderurakov\BookLibraryApi\Config\Database;
use PDO;
use PDOException;

/**
 * @OA\Schema(
 *   schema="UserLibraryAccess",
 *   type="object",
 *   description="Доступ к библиотеке пользователей",
 *   @OA\Property(property="owner_user_id", type="integer", description="ID владельца библиотеки"),
 *   @OA\Property(property="access_user_id", type="integer", description="ID пользователя, которому предоставлен доступ"),
 *   @OA\Property(property="created_at", type="string", format="date-time", description="Дата предоставления доступа")
 * )
 */

class UserLibraryAccess {
    private PDO $conn;
    private string $table_name = "user_library_access";

    public function __construct() {
        $this->conn = Database::getInstance();
    }

    /**
     * Предоставляет доступ к библиотеке другому пользователю.
     *
     * @param int $ownerUserId ID владельца библиотеки.
     * @param int $accessUserId ID пользователя, которому предоставляется доступ.
     * @return bool Возвращает true, если доступ успешно предоставлен, иначе false.
     */
    public function grantAccess(int $ownerUserId, int $accessUserId): bool {
        $query = "INSERT INTO $this->table_name (owner_user_id, access_user_id) VALUES (:owner_user_id, :access_user_id)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':owner_user_id', $ownerUserId);
        $stmt->bindParam(':access_user_id', $accessUserId);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Возвращает список пользователей с доступом к библиотеке.
     *
     * @param int $ownerUserId ID владельца библиотеки.
     * @return array Массив пользователей с полями id и username.
     */
    public function getAccessList(int $ownerUserId): array {
        $query = "SELECT u.id, u.username FROM $this->table_name ula
                  JOIN users u ON ula.access_user_id = u.id
                  WHERE ula.owner_user_id = :owner_user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':owner_user_id', $ownerUserId);

        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }
}