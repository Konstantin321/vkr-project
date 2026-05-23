<?php

require_once __DIR__ . '/../app/auth/Auth.php';

Auth::startSession();

if (Auth::check()) {
    header('Location: tasks_list.php');
    exit;
}

$message = '';
$login = '';
$redirect = $_GET['redirect'] ?? 'tasks_list.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = (string)($_POST['password'] ?? '');
    $redirect = $_POST['redirect'] ?? 'tasks_list.php';

    if ($login === '' || $password === '') {
        $message = 'Введите логин и пароль.';
    } elseif (Auth::attempt($login, $password)) {
        header('Location: ' . (preg_match('/^[a-zA-Z0-9_\-]+\.php(\?.*)?$/', $redirect) ? $redirect : 'tasks_list.php'));
        exit;
    } else {
        $message = Auth::lastError() !== '' ? Auth::lastError() : 'Неверный логин или пароль.';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в систему</title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-card">
            <p class="sidebar-brand__eyebrow">ВКР</p>
            <h1 class="page-title">Вход в систему</h1>
            <p class="page-subtitle">Используйте локальную учётную запись. Позже этот экран можно подключить к кафедральному сервису авторизации.</p>

            <?php if ($message !== ''): ?>
                <div class="message"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">

                <label for="login">Логин или email</label>
                <input
                    type="text"
                    name="login"
                    id="login"
                    value="<?= htmlspecialchars($login) ?>"
                    required
                    autofocus
                >

                <label for="password">Пароль</label>
                <input type="password" name="password" id="password" required>

                <div class="form-actions">
                    <button type="submit">Войти</button>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
