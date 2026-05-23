<?php

require_once __DIR__ . '/../app/auth/Auth.php';
Auth::requireAuth();

require_once __DIR__ . '/../app/controllers/TaskSetController.php';

$controller = new TaskSetController();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $message = $controller->delete((int)($_POST['delete_id'] ?? 0));
}

$taskSets = $controller->index();

$pageTitle = 'Список наборов заданий';
$activePage = 'sets';
require __DIR__ . '/includes/layout_start.php';
?>

    <div class="page-header">
        <div>
            <h1 class="page-title">Список наборов заданий</h1>
            <p class="page-subtitle">Управление наборами, временем выполнения и составом заданий.</p>
        </div>
        <a href="create_task_set.php" class="btn btn-primary">Создать набор</a>
    </div>

    <div class="top-links">
        <a href="add_task_to_set.php">Добавить задания в набор</a>
        <a href="tasks_list.php">Список заданий</a>
    </div>

    <?php if ($message !== ''): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (empty($taskSets)): ?>
        <div class="empty">Наборы заданий пока не созданы.</div>
    <?php else: ?>
        <div class="card table-card">
            <div class="table-responsive">
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Описание</th>
                        <th>Время (мин)</th>
                        <th>Автор</th>
                        <th>Дата создания</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($taskSets as $set): ?>
                        <tr>
                            <td><?= htmlspecialchars($set['id']) ?></td>
                            <td><?= htmlspecialchars($set['name']) ?></td>
                            <td><?= htmlspecialchars($set['description'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($set['execution_time_minutes']) ?></td>
                            <td><?= htmlspecialchars($set['author_name']) ?></td>
                            <td><?= htmlspecialchars($set['created_at']) ?></td>
                            <td>
                                <div class="actions">
                                    <a href="view_task_set.php?id=<?= (int)$set['id'] ?>" class="view-btn">
                                        Просмотр
                                    </a>

                                    <a href="edit_task_set.php?id=<?= (int)$set['id'] ?>" class="edit-btn">
                                        Редактировать
                                    </a>

                                    <form method="POST" class="delete-form" onsubmit="return confirm('Удалить этот набор заданий?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="delete_id" value="<?= (int)$set['id'] ?>">
                                        <button type="submit" class="delete-btn">Удалить</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

<?php require __DIR__ . '/includes/layout_end.php'; ?>
