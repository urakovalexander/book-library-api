<?php

namespace Alexanderurakov\BookLibraryApi\Models;

use Alexanderurakov\BookLibraryApi\Config\Database;
use PDO;
use PDOException;

/**
 * @OA\Schema(
 *   schema="Book",
 *   type="object",
 *   description="Книга",
 *   @OA\Property(property="id", type="integer", description="ID книги"),
 *   @OA\Property(property="user_id", type="integer", description="ID пользователя"),
 *   @OA\Property(property="title", type="string", description="Название книги"),
 *   @OA\Property(property="content", type="string", description="Содержимое книги"),
 *   @OA\Property(property="is_deleted", type="boolean", description="Книга удалена"),
 *   @OA\Property(property="created_at", type="string", format="date-time", description="Дата создания"),
 *   @OA\Property(property="updated_at", type="string", format="date-time", description="Дата обновления"),
 *   @OA\Property(property="deleted_at", type="string", format="date-time", description="Дата удаления")
 * )
 */
class Book {
    private PDO $conn;
    private string $table_name = "books";

    public function __construct() {
        $this->conn = Database::getInstance();
    }

    /**
     * Создает новую книгу.
     *
     * @param int $userId ID пользователя.
     * @param string $title Название книги.
     * @param string $content Содержимое книги.
     * @return bool Возвращает true, если книга успешно создана, иначе false.
     */
    public function create(int $userId, string $title, string $content): bool {
        $query = "INSERT INTO $this->table_name (user_id, title, content) VALUES (:user_id, :title, :content)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Возвращает список книг пользователя.
     *
     * @param int $userId ID пользователя.
     * @return array Массив книг с полями id, title.
     */
    public function getBooksByUser(int $userId): array {
        $query = "SELECT id, title FROM $this->table_name WHERE user_id = :user_id AND is_deleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);

        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    /**
     * Возвращает книгу по ID.
     *
     * @param int $bookId ID книги.
     * @return array|false Массив с полями title и content или false, если книга не найдена.
     */
    public function getBookById(int $bookId): false|array
    {
        $query = "SELECT title, content FROM $this->table_name WHERE id = :id AND is_deleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $bookId);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
    }

    /**
     * Обновляет книгу.
     *
     * @param int $bookId ID книги.
     * @param string $title Новое название книги.
     * @param string $content Новое содержимое книги.
     * @return bool Возвращает true, если книга успешно обновлена, иначе false.
     */
    public function update(int $bookId, string $title, string $content): bool {
        $query = "UPDATE $this->table_name SET title = :title, content = :content WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $bookId);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Удаляет книгу с возможностью восстановления.
     *
     * @param int $bookId ID книги.
     * @return bool Возвращает true, если книга успешно удалена, иначе false.
     */
    public function delete(int $bookId): bool {
        $query = "UPDATE $this->table_name SET is_deleted = 1, deleted_at = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $bookId);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Восстанавливает удаленную книгу.
     *
     * @param int $bookId ID книги.
     * @return bool Возвращает true, если книга успешно восстановлена, иначе false.
     */
    public function restore(int $bookId): bool {
        $query = "UPDATE $this->table_name SET is_deleted = 0, deleted_at = NULL WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $bookId);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

}