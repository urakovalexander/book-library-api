<?php

namespace Alexanderurakov\BookLibraryApi\Controllers;

use Alexanderurakov\BookLibraryApi\Models\UserLibraryAccess;

/**
 * @OA\PathItem(
 *     path="/user-library-access"
 * )
 */
/**
 * @OA\Tag(
 *     name="user-library-access",
 *     description="Операции с доступом к библиотеке пользователей"
 * )
 */
class UserLibraryAccessController {
    private UserLibraryAccess $userLibraryAccessModel;

    public function __construct() {
        $this->userLibraryAccessModel = new UserLibraryAccess();
    }

    /**
     * @OA\Post(
     *     path="/user-library-access",
     *     tags={"user-library-access"},
     *     summary="Предоставление доступа к библиотеке другому пользователю",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"owner_user_id", "access_user_id"},
     *             @OA\Property(property="owner_user_id", type="integer", description="ID владельца библиотеки"),
     *             @OA\Property(property="access_user_id", type="integer", description="ID пользователя, которому предоставляется доступ")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Доступ успешно предоставлен",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Access granted successfully.")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Не удалось предоставить доступ")
     * )
     */
    public function grantAccess(array $data): array {
        $ownerUserId = $data['owner_user_id'];
        $accessUserId = $data['access_user_id'];

        if ($this->userLibraryAccessModel->grantAccess($ownerUserId, $accessUserId)) {
            return ['status' => 'success', 'message' => 'Access granted successfully.'];
        }
        return ['status' => 'error', 'message' => 'Failed to grant access.'];
    }

    /**
     * @OA\Get(
     *     path="/user-library-access/{owner_user_id}",
     *     tags={"user-library-access"},
     *     summary="Получение списка пользователей с доступом к библиотеке",
     *     @OA\Parameter(
     *         name="owner_user_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID владельца библиотеки"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список пользователей с доступом",
     *         @OA\JsonContent(type="array", @OA\Items(
     *             @OA\Property(property="id", type="integer", description="ID пользователя"),
     *             @OA\Property(property="username", type="string", description="Имя пользователя")
     *         ))
     *     ),
     *     @OA\Response(response=404, description="Пользователи с доступом не найдены"),
     *     @OA\Response(response=500, description="Ошибка сервера")
     * )
     */
    public function getAccessList(int $ownerUserId): array {
        return $this->userLibraryAccessModel->getAccessList($ownerUserId);
    }
}