<?php

namespace Alexanderurakov\BookLibraryApi\Models;

use Alexanderurakov\BookLibraryApi\Config\Database;
use PDO;
use PDOException;

/**
 * @OA\Schema(
 *   schema="BookRevision",
 *   type="object",
 *   description="Ревизия книги",
 *   @OA\Property(property="id", type="integer", description="ID ревизии"),
 *   @OA\Property(property="book_id", type="integer", description="ID книги"),
 *   @OA\Property(property="title", type="string", description="Название ревизии"),
 *   @OA\Property(property="content", type="string", description="Содержимое ревизии"),
 *   @OA\Property(property="created_at", type="string", format="date-time", description="Дата создания ревизии"),
 *   @OA\Property(property="updated_at", type="string", format="date-time", description="Дата обновления ревизии")
 * )
 */

class BookRevisions {
    private PDO $conn;
    private string $table_name = "book_revisions";

    public function __construct() {
        $this->conn = Database::getInstance();
    }

    public function createRevision(int $bookId, string $title, string $content): bool {
        $query = "INSERT INTO $this->table_name (book_id, title, content) VALUES (:book_id, :title, :content)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':book_id', $bookId);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function getRevisionsByBookId(int $bookId): array {
        $query = "SELECT id, title, content FROM $this->table_name WHERE book_id = :book_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':book_id', $bookId);

        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }
}