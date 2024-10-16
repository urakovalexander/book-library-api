<?php
require '../vendor/autoload.php';

use Alexanderurakov\BookLibraryApi\Controllers\AuthController;
use Alexanderurakov\BookLibraryApi\Controllers\UserController;

header("Content-Type: application/json");
$requestMethod = $_SERVER["REQUEST_METHOD"];
$requestUri = $_SERVER["REQUEST_URI"];

$authController = new AuthController();
$userController = new UserController();

if ($requestMethod === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (isset($_GET['action'])) {
        if ($_GET['action'] === 'register') {
            echo $authController->register($data);
        } elseif ($_GET['action'] === 'login') {
            echo $authController->login($data);
        }
    }
}

// Обрабатываем запрос GET на получение списка участников
if ($requestMethod === 'GET' && $requestUri === '/users') {
    $userController->getAllUsers();
}