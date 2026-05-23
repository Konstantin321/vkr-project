<?php

require_once __DIR__ . '/../app/auth/Auth.php';
Auth::requireAuth();

require_once __DIR__ . '/../app/controllers/TaskSetController.php';

$controller = new TaskSetController();
$message = '';
$selectedTaskSetId = $_POST['task_set_id'] ?? '';
$selectedTasks = $_POST['selected_tasks'] ?? [];
$orderValues = $_POST['order_number'] ?? [];
$scoreValues = $_POST['max_score'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $controller->addMultipleTasksToSet($_POST);

    if ($message === 'Выбранные задания добавлены в набор.') {
        $selectedTaskSetId = '';
        $selectedTasks = [];
        $orderValues = [];
        $scoreValues = [];
    }
}
$formData = $controller->getFormData();

$pageTitle = 'Добавить задания в набор';
$activePage = 'sets';
require __DIR__ . '/includes/layout_start.php';
?>

    <div class="top-links">
        <a href="task_sets_list.php">К списку наборов</a>
        <a href="create_task_set.php">Создать набор</a>
        <a href="tasks_list.php">Список заданий</a>
    </div>

    <div class="page-header">
        <div>
            <h1 class="page-title">Добавить задания в набор</h1>
            <p class="page-subtitle">Выберите набор, задания, порядок и максимальные баллы.</p>
        </div>
    </div>

    <?php if ($message !== ''): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (empty($formData['taskSets'])): ?>
        <div class="empty">Сначала создайте хотя бы один набор заданий.</div>
    <?php elseif (empty($formData['tasks'])): ?>
        <div class="empty">В системе пока нет заданий для добавления в набор.</div>
    <?php else: ?>
        <form method="POST">

            <div class="panel form-block">
                <label for="task_set_id">Выберите набор заданий</label>
                <select name="task_set_id" id="task_set_id" required>
                    <option value="">Выберите набор</option>
                    <?php foreach ($formData['taskSets'] as $set): ?>
                        <option
                            value="<?= $set['id'] ?>"
                            <?= (string)$selectedTaskSetId === (string)$set['id'] ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($set['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="card table-card">
                <div class="table-responsive">
                    <table>
                    <thead>
                        <tr>
                            <th>Выбрать</th>
                            <th>ID</th>
                            <th>Название задания</th>
                            <th>Порядок</th>
                            <th>Баллы</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($formData['tasks'] as $task): ?>
                            <tr>
                                <td>
                                    <input
                                        type="checkbox"
                                        name="selected_tasks[]"
                                        value="<?= (int)$task['id'] ?>"
                                        <?= in_array((string)$task['id'], array_map('strval', $selectedTasks), true) ? 'checked' : '' ?>
                                    >
                                </td>
                                <td><?= (int)$task['id'] ?></td>
                                <td><?= htmlspecialchars($task['title']) ?></td>
                                <td>
                                    <input
                                        type="number"
                                        name="order_number[<?= (int)$task['id'] ?>]"
                                        min="1"
                                        placeholder="Напр. 1"
                                        value="<?= htmlspecialchars($orderValues[$task['id']] ?? '') ?>"
                                        class="compact-input"
                                    >
                                </td>
                                <td>
                                    <input
                                        type="number"
                                        name="max_score[<?= (int)$task['id'] ?>]"
                                        min="0"
                                        step="0.5"
                                        placeholder="Напр. 5"
                                        value="<?= htmlspecialchars($scoreValues[$task['id']] ?? '') ?>"
                                        class="compact-input"
                                    >
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    </table>
                </div>
            </div>

            <button type="submit">Добавить выбранные задания в набор</button>
        </form>
    <?php endif; ?>

<?php require __DIR__ . '/includes/layout_end.php'; ?>
