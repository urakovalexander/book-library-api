<?php

namespace Alexanderurakov\BookLibraryApi\Controllers;

use Alexanderurakov\BookLibraryApi\Models\Token;
use Alexanderurakov\BookLibraryApi\Models\User;
use Firebase\JWT\JWT;

/**
 * @OA\PathItem(
 *     path="/users"
 * )
 */
/**
 * @OA\Tag(
 *     name="users",
 *     description="Операции с пользователями"
 * )
 */
class UserController {
    private User $userModel;
    private Token $tokenModel;

    public function __construct() {
        $this->userModel = new User();
        $this->tokenModel = new Token();
    }

    /**
     * Возвращает JSON-ответ.
     */
    private function jsonResponse(array $data): string {
        return json_encode($data);
    }

    /**
     * @OA\Post(
     *     path="/users/register",
     *     tags={"users"},
     *     summary="Регистрация пользователя",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username", "password", "password_confirm"},
     *             @OA\Property(property="username", type="string", description="Имя пользователя"),
     *             @OA\Property(property="password", type="string", description="Пароль"),
     *             @OA\Property(property="password_confirm", type="string", description="Подтверждение пароля")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Пользователь успешно зарегистрирован",
     *     ),
     *     @OA\Response(response=400, description="Пароли не совпадают"),
     *     @OA\Response(response=409, description="Пользователь уже существует")
     * )
     */
    public function register(array $data): string {
        $username = $data['username'];
        $password = $data['password'];
        $passwordConfirm = $data['password_confirm'];

        if ($password !== $passwordConfirm) {
            http_response_code(400);
            return $this->jsonResponse(["message" => "Passwords do not match."]);
        }

        if ($this->userModel->create($username, $password)) {
            $userId = $this->userModel->getIdByUsername($username);
            $token = $this->generateJWT($userId);

            // Устанавливаем время истечения токена
            $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 час
            $this->tokenModel->saveToken($userId, $token, $expiresAt);
            return $this->jsonResponse(["message" => "User created successfully."]);
        }

        http_response_code(409);
        return $this->jsonResponse(["message" => "User already exists."]);
    }

    /**
     * @OA\Post(
     *     path="/users/login",
     *     tags={"users"},
     *     summary="Аутентификация пользователя",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username", "password"},
     *             @OA\Property(property="username", type="string", description="Имя пользователя"),
     *             @OA\Property(property="password", type="string", description="Пароль")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Аутентификация успешна",
     *     ),
     *     @OA\Response(response=401, description="Неверные учетные данные")
     * )
     */
    public function login(array $data): string {
        $username = $data['username'];
        $password = $data['password'];
        $userId = $this->userModel->login($username, $password);

        if ($userId) {
            $token = $this->generateJWT($userId);

            // Устанавливаем время истечения токена
            $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 час
            $this->tokenModel->saveToken($userId, $token, $expiresAt);
            return $this->jsonResponse(["message" => "User logged in successfully."]);
        }

        http_response_code(401);
        return $this->jsonResponse(["message" => "Invalid credentials."]);
    }

    /**
     * @OA\Get(
     *     path="/users",
     *     tags={"users"},
     *     summary="Получение списка всех пользователей",
     *     @OA\Response(
     *         response=200,
     *         description="Список пользователей",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
     *     ),
     *     @OA\Response(response=500, description="Внутренняя ошибка сервера")
     * )
     */
    public function getAllUsers(): array {
        return $this->userModel->getAllUsers();
    }

    /**
     * Создание JWT токена.
     * Нужно заменить на свой домен your-domain.com
     */
    private function generateJWT(int $userId): string {
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600; // 1 час
        $payload = [
            'iss' => 'your-domain.com',
            'sub' => $userId,
            'iat' => $issuedAt,
            'exp' => $expirationTime
        ];

        return JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
    }
}