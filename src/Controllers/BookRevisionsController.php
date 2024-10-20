<?php

namespace Alexanderurakov\BookLibraryApi\Controllers;

use Alexanderurakov\BookLibraryApi\Models\BookRevisions;

/**
* @OA\PathItem(
*     path="/book-revisions"
* )
*/
/**
* @OA\Tag(
*     name="book-revisions",
*     description="Операции с ревизиями книг"
* )
*/
class BookRevisionsController {
private $bookRevisionsModel;

public function __construct() {
$this->bookRevisionsModel = new BookRevisions();
}

/**
* @OA\Post(
*     path="/book-revisions",
*     tags={"book-revisions"},
*     summary="Создание новой ревизии книги",
*     @OA\RequestBody(
*         required=true,
*         @OA\JsonContent(
*             required={"book_id", "title", "content"},
*             @OA\Property(property="book_id", type="integer", description="ID книги"),
*             @OA\Property(property="title", type="string", description="Название ревизии"),
*             @OA\Property(property="content", type="string", description="Содержимое ревизии")
*         )
*     ),
*     @OA\Response(
*         response=200,
*         description="Ревизия успешно создана",
*         @OA\JsonContent(
*             @OA\Property(property="status", type="string", example="success"),
*             @OA\Property(property="message", type="string", example="Revision created successfully.")
*         )
*     ),
*     @OA\Response(response=500, description="Не удалось создать ревизию")
* )
*/
public function createRevision(array $data): array {
$bookId = $data['book_id'];
$title = $data['title'];
$content = $data['content'];

if ($this->bookRevisionsModel->createRevision($bookId, $title, $content)) {
return ['status' => 'success', 'message' => 'Revision created successfully.'];
}
return ['status' => 'error', 'message' => 'Failed to create revision.'];
}

/**
* @OA\Get(
*     path="/book-revisions/{book_id}",
*     tags={"book-revisions"},
*     summary="Получение всех ревизий для книги",
*     @OA\Parameter(
*         name="book_id",
*         in="path",
*         required=true,
*         @OA\Schema(type="integer"),
*         description="ID книги"
*     ),
*     @OA\Response(
*         response=200,
*         description="Список ревизий",
*         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/BookRevision"))
*     ),
*     @OA\Response(response=404, description="Ревизии не найдены"),
*     @OA\Response(response=500, description="Ошибка сервера")
* )
*/
public function getRevisionsByBookId(int $bookId): array {
return $this->bookRevisionsModel->getRevisionsByBookId($bookId);
}
}