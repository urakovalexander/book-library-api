<?php

namespace Alexanderurakov\BookLibraryApi\Controllers;

use Alexanderurakov\BookLibraryApi\Models\User;
use Firebase\JWT\JWT;
use Dotenv\Dotenv;

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();

        // Инициализация загрузки переменных из .env файла
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
    }

    /**
     * Регистрация нового пользователя.
     *
     * Пример JSON запроса:
     * {
     *     "username": "new_user",
     *     "password": "user_password",
     *     "password_confirm": "user_password"
     * }
     *
     * @param object $data Данные пользователя (username, password, и password_confirm).
     * @return string JSON ответ с сообщением о результате регистрации.
     */
    public function register($data) {
        // Проверка наличия всех полей
        if (empty($data->username) || empty($data->password) || empty($data->password_confirm)) {
            return json_encode(["message" => "Username, password, and password confirmation are required."]);
        }

        // Проверка совпадения паролей
        if ($data->password !== $data->password_confirm) {
            return json_encode(["message" => "Passwords do not match."]);
        }

        // Создание пользователя
        if ($this->userModel->create($data->username, password_hash($data->password, PASSWORD_DEFAULT))) {
            // Автоматически логиним пользователя после регистрации
            $userId = $this->userModel->getIdByUsername($data->username);
            $token = $this->generateJWT($userId);
            return json_encode(["message" => "User registered successfully.", "token" => $token]);
        }

        return json_encode(["message" => "Registration failed. Username might already exist."]);
    }

    /**
     * Аутентификация пользователя и получение JWT.
     *
     * @param object $data Данные пользователя (username и password).
     * @return string JSON ответ с JWT или сообщением об ошибке.
     */
    public function login($data) {
        // Проверка данных
        if (empty($data->username) || empty($data->password)) {
            return json_encode(["message" => "Username and password are required."]);
        }

        // Аутентификация
        $userId = $this->userModel->login($data->username, $data->password);
        if ($userId) {
            $token = $this->generateJWT($userId);
            return json_encode(["token" => $token]);
        }
        return json_encode(["message" => "Invalid credentials."]);
    }

    /**
     * Генерация JWT для пользователя.
     *
     * @param int $userId ID пользователя.
     * @return string Закодированный JWT.
     */
    private function generateJWT($userId) {
        $key = $_ENV['JWT_SECRET'] ?? null; // Использование переменной окружения

        // Проверка наличия ключа
        if (!$key) {
            throw new \Exception("JWT_SECRET is not set in the environment.");
        }

        $payload = [
            "iat" => time(),
            "exp" => time() + (60 * 60), // 1 час
            "sub" => $userId
        ];

        return JWT::encode($payload, $key, 'HS256');
    }
}