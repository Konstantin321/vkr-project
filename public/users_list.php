<?php

require_once __DIR__ . '/../app/auth/Auth.php';
Auth::requireAuth();
Auth::requireRole(['admin']);

require_once __DIR__ . '/../app/controllers/UserController.php';

$controller = new UserController();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_role') {
        $message = $controller->updateRole($_POST);
    }
}

$users = $controller->index();
$roleLabels = Auth::roleLabels();

$pageTitle = 'Пользователи';
$activePage = 'users';
require __DIR__ . '/includes/layout_start.php';
?>

    <div class="page-header">
        <div>
            <h1 class="page-title">Пользователи</h1>
            <p class="page-subtitle">Управление ролями пользователей системы.</p>
        </div>
    </div>

    <?php if ($message !== ''): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (empty($users)): ?>
        <div class="empty">Пользователи пока отсутствуют.</div>
    <?php else: ?>
        <div class="card table-card">
            <div class="table-responsive">
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>ФИО</th>
                        <th>Логин</th>
                        <th>Email</th>
                        <th>Роль</th>
                        <th>Действие</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= (int)$user['id'] ?></td>
                            <td><?= htmlspecialchars($user['full_name'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($user['login'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($user['email'] ?? '—') ?></td>
                            <td>
                                <span class="status-badge <?= $user['role'] === 'admin' ? 'is-success' : '' ?>">
                                    <?= htmlspecialchars($roleLabels[$user['role']] ?? $user['role']) ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" class="inline-form">
                                    <input type="hidden" name="action" value="update_role">
                                    <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">

                                    <select name="role" <?= (int)$user['id'] === Auth::id() ? 'disabled' : '' ?>>
                                        <?php foreach ($roleLabels as $role => $label): ?>
                                            <option value="<?= htmlspecialchars($role) ?>" <?= $user['role'] === $role ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($label) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <?php if ((int)$user['id'] === Auth::id()): ?>
                                        <span class="muted-text">Текущий пользователь</span>
                                    <?php else: ?>
                                        <button type="submit">Сохранить</button>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

<?php require __DIR__ . '/includes/layout_end.php'; ?>
