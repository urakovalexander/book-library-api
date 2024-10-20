<?php

namespace Alexanderurakov\BookLibraryApi\Models;

use Alexanderurakov\BookLibraryApi\Config\Database;
use PDO;
use PDOException;

/**
 * @OA\Schema(
 *   schema="ExternalBook",
 *   type="object",
 *   description="Внешняя книга",
 *   @OA\Property(property="id", type="integer", description="ID внешней книги"),
 *   @OA\Property(property="user_id", type="integer", description="ID пользователя, который сохранил книгу"),
 *   @OA\Property(property="external_id", type="string", description="Внешний ID книги"),
 *   @OA\Property(property="title", type="string", description="Название книги"),
 *   @OA\Property(property="description_or_url", type="string", description="Описание или URL на книгу"),
 *   @OA\Property(property="created_at", type="string", format="date-time", description="Дата создания записи"),
 * )
 */

class ExternalBook {
    private PDO $conn;
    private string $table_name = "external_books";

    public function __construct() {
        $this->conn = Database::getInstance();
    }

    /**
     * Сохраняет найденную книгу в базе данных.
     *
     * @param int $userId ID пользователя.
     * @param string $externalId Внешний ID книги.
     * @param string $title Название книги.
     * @param string $descriptionOrUrl Описание или URL на книгу.
     * @return bool Возвращает true, если книга успешно сохранена, иначе false.
     */
    public function save(int $userId, string $externalId, string $title, string $descriptionOrUrl): bool {
        $query = "INSERT INTO $this->table_name (user_id, external_id, title, description_or_url) 
                  VALUES (:user_id, :external_id, :title, :description_or_url)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':external_id', $externalId);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description_or_url', $descriptionOrUrl);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}