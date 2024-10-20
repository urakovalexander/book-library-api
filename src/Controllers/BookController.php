<?php

namespace Alexanderurakov\BookLibraryApi\Controllers;

use Alexanderurakov\BookLibraryApi\Models\Book;

/**
 * @OA\PathItem(
 *     path="/books"
 * )
 */

/**
 * @OA\Tag(
 *     name="books",
 *     description="Операции с книгами"
 * )
 */
class BookController
{
    private Book $bookModel;

    public function __construct()
    {
        $this->bookModel = new Book();
    }

    /**
     * @OA\Post(
     *     path="/books",
     *     tags={"books"},
     *     summary="Создание новой книги",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "title", "content"},
     *             @OA\Property(property="user_id", type="integer", description="ID пользователя"),
     *             @OA\Property(property="title", type="string", description="Название книги"),
     *             @OA\Property(property="content", type="string", description="Содержимое книги")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Книга успешно создана",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Book created successfully.")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Ошибка при создании книги"),
     *     @OA\Response(response=500, description="Внутренняя ошибка сервера")
     * )
     */
    public function create(array $data): array
    {
        $userId = $data['user_id'];
        $title = $data['title'];
        $content = $data['content'];

        if ($this->bookModel->create($userId, $title, $content)) {
            http_response_code(201);
            return ['status' => 'success', 'message' => 'Book created successfully.'];
        }
        http_response_code(400);
        return ['status' => 'error', 'message' => 'Failed to create book.'];
    }

    /**
     * @OA\Get(
     *     path="/books/user/{id}",
     *     tags={"books"},
     *     summary="Получение списка книг пользователя",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID пользователя"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список книг",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Book"))
     *     ),
     *     @OA\Response(response=404, description="Пользователь не найден"),
     *     @OA\Response(response=500, description="Внутренняя ошибка сервера")
     * )
     */
    public function getBooksByUser(int $userId): array
    {
        return $this->bookModel->getBooksByUser($userId);
    }

    /**
     * @OA\Get(
     *     path="/books/{id}",
     *     tags={"books"},
     *     summary="Получить книгу по ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID книги"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Информация о книге",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="title", type="string", example="Моя книга"),
     *             @OA\Property(property="content", type="string", example="Текст книги...")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Книга не найдена"),
     *     @OA\Response(response=500, description="Внутренняя ошибка сервера")
     * )
     */
    public function getBookById(int $bookId): array|false
    {
        return $this->bookModel->getBookById($bookId);
    }

    /**
     * @OA\Put(
     *     path="/books/{id}",
     *     tags={"books"},
     *     summary="Обновление книги",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID книги"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "content"},
     *             @OA\Property(property="title", type="string", description="Название книги"),
     *             @OA\Property(property="content", type="string", description="Содержимое книги")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Книга успешно обновлена",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Book updated successfully.")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Книга не найдена"),
     *     @OA\Response(response=500, description="Внутренняя ошибка сервера")
     * )
     */
    public function update(int $bookId, array $data): array
    {
        $title = $data['title'];
        $content = $data['content'];

        if ($this->bookModel->update($bookId, $title, $content)) {
            return ['status' => 'success', 'message' => 'Book updated successfully.'];
        }
        http_response_code(404);
        return ['status' => 'error', 'message' => 'Failed to update book.'];
    }

    /**
     * @OA\Delete(
     *     path="/books/{id}",
     *     tags={"books"},
     *     summary="Удаление книги",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID книги"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Книга успешно удалена",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Book deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Книга не найдена"),
     *     @OA\Response(response=500, description="Внутренняя ошибка сервера")
     * )
     */
    public function delete(int $bookId): array
    {
        if ($this->bookModel->delete($bookId)) {
            return ['status' => 'success', 'message' => 'Book deleted successfully.'];
        }
        http_response_code(404);
        return ['status' => 'error', 'message' => 'Failed to delete book.'];
    }

    /**
     * @OA\Post(
     *     path="/books/restore",
     *     tags={"books"},
     *     summary="Восстановление удаленной книги",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"book_id"},
     *             @OA\Property(property="book_id", type="integer", description="ID книги")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Книга успешно восстановлена",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Book restored successfully.")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Книга не найдена"),
     *     @OA\Response(response=500, description="Внутренняя ошибка сервера")
     * )
     */
    public function restore(int $bookId): array
    {
        if ($this->bookModel->restore($bookId)) {
            return ["message" => "Book restored successfully"];
        } else {
            http_response_code(500);
            return ["message" => "Error restoring book"];
        }
    }
}