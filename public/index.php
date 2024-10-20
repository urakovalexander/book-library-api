<?php
require '../vendor/autoload.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

use Alexanderurakov\BookLibraryApi\Controllers\BookController;
use Alexanderurakov\BookLibraryApi\Controllers\BookRevisionsController;
use Alexanderurakov\BookLibraryApi\Controllers\ExternalBookController;
use Alexanderurakov\BookLibraryApi\Controllers\TokenController;
use Alexanderurakov\BookLibraryApi\Controllers\UserController;
use Alexanderurakov\BookLibraryApi\Controllers\UserLibraryAccessController;

$requestMethod = $_SERVER["REQUEST_METHOD"];
$requestUri = $_SERVER["REQUEST_URI"];

$userController = new UserController();
$bookController = new BookController();
$bookRevisionsController = new BookRevisionsController();
$externalBookController = new ExternalBookController();
$tokenController = new TokenController();
$userLibraryAccessController = new UserLibraryAccessController();

// Обрабатываем запрос GET на получение списка участников
if ($requestMethod === 'GET' && $requestUri === '/users') {
    echo json_encode($userController->getAllUsers());
}

// Создание нового пользователя
if ($requestMethod === 'POST' && $requestUri === '/users/register') {
    $data = json_decode(file_get_contents("php://input"), true);
    echo json_encode($userController->register($data));
}


// Аутентификация пользователя
if ($requestMethod === 'POST' && $requestUri === '/users/login') {
    $data = json_decode(file_get_contents("php://input"), true);
    echo json_encode($userController->login($data));
}

// Восстановление удаленной книги
if ($requestMethod === 'POST' && $requestUri === '/books/restore') {
    $data = json_decode(file_get_contents("php://input"), true);
    $bookId = $data['book_id'];
    echo json_encode($bookController->restore($bookId));
}

// Создание новой книги
if ($requestMethod === 'POST' && $requestUri === '/books') {
    $data = json_decode(file_get_contents("php://input"), true);
    echo json_encode($bookController->create($data));
}

// Получение книг по ID пользователя
if ($requestMethod === 'GET' && preg_match('/\/books\/user\/(\d+)/', $requestUri, $matches)) {
    $userId = (int)$matches[1];
    echo json_encode($bookController->getBooksByUser($userId));
}

// Получение книги по ID
if ($requestMethod === 'GET' && preg_match('/\/books\/(\d+)/', $requestUri, $matches)) {
    $bookId = (int)$matches[1];
    echo json_encode($bookController->getBookById($bookId));
}

// Обновление книги
if ($requestMethod === 'PUT' && preg_match('/\/books\/(\d+)/', $requestUri, $matches)) {
    $bookId = (int)$matches[1];
    $data = json_decode(file_get_contents("php://input"), true);
    echo json_encode($bookController->update($bookId, $data));
}

// Удаление книги
if ($requestMethod === 'DELETE' && preg_match('/\/books\/(\d+)/', $requestUri, $matches)) {
    $bookId = (int)$matches[1];
    echo json_encode($bookController->delete($bookId));
}

// Создание новой ревизии книги
if ($requestMethod === 'POST' && $requestUri === '/book-revisions') {
    $data = json_decode(file_get_contents("php://input"), true);
    $bookId = $data['book_id'] ?? null;
    $title = $data['title'] ?? null;
    $content = $data['content'] ?? null;

    // Проверяем, что все необходимые параметры переданы
    if ($bookId === null || $title === null || $content === null) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields.']);
        exit;
    }

    echo json_encode($bookRevisionsController->createRevision($data));
}

// Получение ревизий для книги
if ($requestMethod === 'GET' && preg_match('/\/book-revisions\/(\d+)/', $requestUri, $matches)) {
    $bookId = (int)$matches[1];
    $revisions = $bookRevisionsController->getRevisionsByBookId($bookId);

    if (empty($revisions)) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'No revisions found for this book.']);
    } else {
        echo json_encode($revisions);
    }
}

// Сохранение найденной книги
if ($requestMethod === 'POST' && $requestUri === '/external-books') {
    $data = json_decode(file_get_contents("php://input"), true);
    echo json_encode($externalBookController->save($data));
}

// Поиск внешних книг
if ($requestMethod === 'GET' && preg_match('/\/external-books\/search/', $requestUri)) {
    $query = $_GET['query'] ?? '';
    echo json_encode($externalBookController->searchExternalBooks($query));
}

// Сохранение токена
if ($requestMethod === 'POST' && $requestUri === '/tokens') {
    $data = json_decode(file_get_contents("php://input"), true);
    echo json_encode($tokenController->saveToken($data));
}

// Удаление токена
if ($requestMethod === 'DELETE' && preg_match('/\/tokens\/(\d+)/', $requestUri, $matches)) {
    $userId = (int)$matches[1];
    echo json_encode($tokenController->deleteToken($userId));
}

// Проверка существования токена
if ($requestMethod === 'GET' && preg_match('/\/tokens\/(\d+)/', $requestUri, $matches)) {
    $userId = (int)$matches[1];
    echo json_encode($tokenController->tokenExists($userId));
}

// Предоставление доступа к библиотеке
if ($requestMethod === 'POST' && $requestUri === '/user-library-access') {
    $data = json_decode(file_get_contents("php://input"), true);
    echo json_encode($userLibraryAccessController->grantAccess($data));
}

// Получение списка пользователей с доступом
if ($requestMethod === 'GET' && preg_match('/\/user-library-access\/(\d+)/', $requestUri, $matches)) {
    $ownerUserId = (int)$matches[1];
    echo json_encode($userLibraryAccessController->getAccessList($ownerUserId));
}