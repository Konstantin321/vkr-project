<?php

require_once __DIR__ . '/../app/auth/Auth.php';
Auth::requireAuth();

require_once __DIR__ . '/../app/controllers/TaskSetController.php';

$controller = new TaskSetController();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'remove') {
        $message = $controller->removeTaskFromSet($_POST);
    } elseif ($action === 'update_item') {
        $message = $controller->updateSetItem($_POST);
    }
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$data = $controller->show($id);

if (!$data) {
    die('Набор заданий не найден.');
}

$taskSet = $data['taskSet'];
$items = $data['items'];

$pageTitle = 'Просмотр набора заданий';
$activePage = 'sets';
require __DIR__ . '/includes/layout_start.php';
?>

    <div class="top-links">
        <a href="task_sets_list.php">Назад к списку наборов</a>
        <a href="edit_task_set.php?id=<?= (int)$taskSet['id'] ?>">Редактировать набор</a>
        <a href="add_task_to_set.php">Добавить задания в набор</a>
    </div>

    <div class="page-header">
        <div>
            <h1 class="page-title">Просмотр набора заданий</h1>
            <p class="page-subtitle">Сведения о наборе, порядок заданий и баллы.</p>
        </div>
    </div>

    <?php if (!empty($message)): ?>
        <div class="message">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <h2>Основная информация</h2>

        <div class="task-detail-row">
            <span class="task-detail-label">ID</span>
            <div class="task-detail-value"><?= htmlspecialchars($taskSet['id']) ?></div>
        </div>

        <div class="task-detail-row">
            <span class="task-detail-label">Название</span>
            <div class="task-detail-value"><?= htmlspecialchars($taskSet['name']) ?></div>
        </div>

        <div class="task-detail-row">
            <span class="task-detail-label">Описание</span>
            <div class="task-detail-value"><?= htmlspecialchars($taskSet['description'] ?? '—') ?></div>
        </div>

        <div class="task-detail-row">
            <span class="task-detail-label">Время выполнения (минуты)</span>
            <div class="task-detail-value"><?= htmlspecialchars($taskSet['execution_time_minutes']) ?></div>
        </div>

        <div class="task-detail-row">
            <span class="task-detail-label">Автор</span>
            <div class="task-detail-value"><?= htmlspecialchars($taskSet['author_name']) ?></div>
        </div>

        <div class="task-detail-row">
            <span class="task-detail-label">Дата создания</span>
            <div class="task-detail-value"><?= htmlspecialchars($taskSet['created_at']) ?></div>
        </div>
    </div>

    <h2 class="section-title">Задания в наборе</h2>

    <?php if (empty($items)): ?>
        <div class="empty">В этот набор пока не добавлены задания.</div>
    <?php else: ?>
        <div class="card table-card">
            <div class="table-responsive">
                <table>
                    <thead>
                    <tr>
                        <th>Порядок</th>
                        <th>ID задания</th>
                        <th>Название задания</th>
                        <th>Тип</th>
                        <th>Дисциплина</th>
                        <th>Баллы</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <form method="POST" class="inline-form">
                                    <input type="hidden" name="action" value="update_item">
                                    <input type="hidden" name="item_id" value="<?= (int)$item['id'] ?>">

                                    <input
                                        type="number"
                                        name="order_number"
                                        min="1"
                                        value="<?= htmlspecialchars($item['order_number']) ?>"
                                        class="compact-input"
                                        required
                                    >
                            </td>
                            <td><?= htmlspecialchars($item['task_id']) ?></td>
                            <td><?= htmlspecialchars($item['title']) ?></td>
                            <td><?= htmlspecialchars($item['task_type_name']) ?></td>
                            <td><?= htmlspecialchars($item['discipline_name']) ?></td>
                            <td>
                                    <input
                                        type="number"
                                        name="max_score"
                                        min="0"
                                        step="0.5"
                                        value="<?= htmlspecialchars($item['max_score']) ?>"
                                        class="compact-input"
                                        required
                                    >
                            </td>
                            <td>
                                    <button type="submit" class="edit-btn">
                                        Сохранить
                                    </button>
                                </form>

                                <form method="POST" class="delete-form" onsubmit="return confirm('Удалить задание из набора?');">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="item_id" value="<?= (int)$item['id'] ?>">
                                    <button type="submit" class="delete-btn">Удалить</button>
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
