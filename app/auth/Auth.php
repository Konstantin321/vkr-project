<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

class Auth
{
    private static string $lastError = '';

    public static function startSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function attempt(string $login, string $password): bool
    {
        self::startSession();
        self::$lastError = '';

        if (self::provider() !== 'local') {
            self::$lastError = 'Настроен внешний провайдер авторизации. Локальный вход отключён.';
            return false;
        }

        try {
            $database = new Database();
            $userModel = new User($database->connect());
            $user = $userModel->findByLogin($login);
        } catch (PDOException $exception) {
            self::$lastError = 'Не удалось проверить пользователя. Проверьте, что применён SQL-скрипт storage/auth_schema.sql.';
            return false;
        }

        if (!$user || empty($user['password_hash'])) {
            return false;
        }

        if (!password_verify($password, $user['password_hash'])) {
            return false;
        }

        self::setUserSession($user);

        return true;
    }

    public static function lastError(): string
    {
        return self::$lastError;
    }

    public static function requireAuth(): void
    {
        self::startSession();

        if (self::check()) {
            return;
        }

        $current = basename($_SERVER['REQUEST_URI'] ?? 'tasks_list.php');

        header('Location: login.php?redirect=' . urlencode($current));
        exit;
    }

    public static function check(): bool
    {
        self::startSession();

        return !empty($_SESSION['user_id']);
    }

    public static function user(): ?array
    {
        self::startSession();

        if (empty($_SESSION['user_id'])) {
            return null;
        }

        return [
            'id' => (int)$_SESSION['user_id'],
            'full_name' => $_SESSION['user_full_name'] ?? '',
            'login' => $_SESSION['user_login'] ?? '',
            'email' => $_SESSION['user_email'] ?? '',
            'role' => $_SESSION['user_role'] ?? 'student',
        ];
    }

    public static function id(): int
    {
        self::startSession();

        return (int)($_SESSION['user_id'] ?? 0);
    }

    public static function role(): string
    {
        self::startSession();

        return (string)($_SESSION['user_role'] ?? '');
    }

    public static function logout(): void
    {
        self::startSession();

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    private static function setUserSession(array $user): void
    {
        session_regenerate_id(true);

        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_full_name'] = $user['full_name'] ?? '';
        $_SESSION['user_login'] = $user['login'] ?? '';
        $_SESSION['user_email'] = $user['email'] ?? '';
        $_SESSION['user_role'] = $user['role'] ?? 'student';
    }

    private static function provider(): string
    {
        return getenv('AUTH_PROVIDER') ?: 'local';
    }
}
