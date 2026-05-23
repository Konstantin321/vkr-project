<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../auth/Auth.php';

class UserController
{
    private User $userModel;

    public function __construct()
    {
        $database = new Database();
        $this->userModel = new User($database->connect());
    }

    public function index(): array
    {
        return $this->userModel->getAll();
    }

    public function updateRole(array $postData): string
    {
        $userId = (int)($postData['user_id'] ?? 0);
        $role = (string)($postData['role'] ?? '');
        $allowedRoles = array_keys(Auth::roleLabels());

        if ($userId <= 0) {
            return 'Некорректный пользователь.';
        }

        if (!in_array($role, $allowedRoles, true)) {
            return 'Некорректная роль.';
        }

        if ($userId === Auth::id()) {
            return 'Нельзя изменить роль текущего пользователя.';
        }

        $updated = $this->userModel->updateRole($userId, $role);

        return $updated ? 'Роль пользователя обновлена.' : 'Изменения не сохранены.';
    }
}
