<?php

namespace Alexanderurakov\BookLibraryApi\Controllers;

use Alexanderurakov\BookLibraryApi\Models\ExternalBook;

/**
 * @OA\PathItem(
 *     path="/external-books"
 * )
 */
/**
 * @OA\Tag(
 *     name="external-books",
 *     description="Операции с внешними книгами"
 * )
 */
class ExternalBookController {
    private ExternalBook $externalBookModel;

    public function __construct() {
        $this->externalBookModel = new ExternalBook();
    }

    /**
     * @OA\Post(
     *     path="/external-books",
     *     tags={"external-books"},
     *     summary="Сохранение найденной книги",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "external_id", "title"},
     *             @OA\Property(property="user_id", type="integer", description="ID пользователя"),
     *             @OA\Property(property="external_id", type="string", description="Внешний ID книги"),
     *             @OA\Property(property="title", type="string", description="Название книги"),
     *             @OA\Property(property="description_or_url", type="string", description="Описание или URL на книгу")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Книга успешно сохранена",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="External book saved successfully")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Отсутствуют обязательные поля"),
     *     @OA\Response(response=500, description="Не удалось сохранить внешнюю книгу")
     * )
     */
    public function save(array $data): array {
        $userId = $data['user_id'] ?? null;
        $externalId = $data['external_id'] ?? null;
        $title = $data['title'] ?? null;
        $descriptionOrUrl = $data['description_or_url'] ?? null;

        if (!$userId || !$externalId || !$title) {
            return ['status' => 'error', 'message' => 'Missing required fields'];
        }

        $externalBook = new ExternalBook();

        if ($externalBook->save($userId, $externalId, $title, $descriptionOrUrl)) {
            return ['status' => 'success', 'message' => 'External book saved successfully'];
        }

        return ['status' => 'error', 'message' => 'Failed to save external book'];
    }

    /**
     * @OA\Get(
     *     path="/external-books/search",
     *     tags={"external-books"},
     *     summary="Поиск книг по запросу",
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Строка запроса для поиска книг"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список найденных книг",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/ExternalBook"))
     *     ),
     *     @OA\Response(response=500, description="Ошибка сервера")
     * )
     */
    public function searchExternalBooks(string $query): array {
        $books = [];

        // Поиск через Google Books API
        $googleApiUrl = "https://www.googleapis.com/books/v1/volumes?q=" . urlencode($query);
        $googleResponse = file_get_contents($googleApiUrl);
        $googleBooks = json_decode($googleResponse, true)['items'] ?? [];

        foreach ($googleBooks as $book) {
            $books[] = [
                'external_id' => $book['id'] ?? '',
                'title' => $book['volumeInfo']['title'] ?? '',
                'description_or_url' => $book['volumeInfo']['infoLink'] ?? '',
            ];
        }

        // Поиск через Mann-Ivanov-Ferber
        $mannApiUrl = "https://www.mann-ivanov-ferber.ru/book/search.ajax?q=" . urlencode($query);
        $mannResponse = file_get_contents($mannApiUrl);
        $mannBooks = json_decode($mannResponse, true) ?? [];

        foreach ($mannBooks as $book) {
            $books[] = [
                'external_id' => $book['id'] ?? '', // предполагается, что в ответе есть ID
                'title' => $book['title'] ?? '',
                'description_or_url' => $book['url'] ?? '',
            ];
        }

        return $books;
    }
}