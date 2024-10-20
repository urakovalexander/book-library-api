<?php

namespace Alexanderurakov\BookLibraryApi\Controllers;

use Alexanderurakov\BookLibraryApi\Models\Token;

/**
 * @OA\PathItem(
 *     path="/tokens"
 * )
 */
/**
 * @OA\Tag(
 *     name="tokens",
 *     description="Операции с токенами"
 * )
 */
class TokenController {
    private Token $tokenModel;

    public function __construct() {
        $this->tokenModel = new Token();
    }

    /**
     * @OA\Post(
     *     path="/tokens/save",
     *     tags={"tokens"},
     *     summary="Сохранение токена для пользователя",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "token"},
     *             @OA\Property(property="user_id", type="integer", description="ID пользователя, которому принадлежит токен"),
     *             @OA\Property(property="token", type="string", description="JWT токен")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Токен успешно сохранен",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Token saved successfully.")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Не удалось сохранить токен")
     * )
     */
    public function saveToken(array $data): array {
        $userId = $data['user_id'];
        $token = $data['token'];

        if ($this->tokenModel->saveToken($userId, $token)) {
            return ['status' => 'success', 'message' => 'Token saved successfully.'];
        }
        return ['status' => 'error', 'message' => 'Failed to save token.'];
    }

    /**
     * @OA\Delete(
     *     path="/tokens/{userId}",
     *     tags={"tokens"},
     *     summary="Удаление токена пользователя",
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="ID пользователя, чей токен нужно удалить",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Токен успешно удален",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Token deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Не удалось удалить токен")
     * )
     */
    public function deleteToken(int $userId): array {
        if ($this->tokenModel->deleteToken($userId)) {
            return ['status' => 'success', 'message' => 'Token deleted successfully.'];
        }
        return ['status' => 'error', 'message' => 'Failed to delete token.'];
    }

    /**
     * @OA\Get(
     *     path="/tokens/{userId}",
     *     tags={"tokens"},
     *     summary="Проверка существования токена для пользователя",
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="ID пользователя для проверки токена",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Токен существует",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Token exists.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Токен не существует",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Token does not exist.")
     *         )
     *     )
     * )
     */
    public function tokenExists(int $userId): array {
        if ($this->tokenModel->tokenExists($userId)) {
            return ['status' => 'success', 'message' => 'Token exists.'];
        }
        return ['status' => 'error', 'message' => 'Token does not exist.'];
    }
}