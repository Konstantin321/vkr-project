<?php

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
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить задания в набор</title>
    <style>
        .top-links {
            margin-bottom: 20px;
        }

        .top-links a {
            display: inline-block;
            margin-right: 15px;
            text-decoration: none;
            color: #0a58ca;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }

        h1 {
            margin-bottom: 20px;
        }

        .message {
            margin-bottom: 20px;
            padding: 10px;
            background: #f0f0f0;
            border: 1px solid #dddddd;
        }

        .form-block {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #dddddd;
            background: #fafafa;
            max-width: 500px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        select {
            width: 100%;
            padding: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #cccccc;
            padding: 10px;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background-color: #f3f3f3;
        }

        input[type="number"] {
            width: 100px;
            padding: 6px;
        }

        .submit-btn {
            margin-top: 20px;
            padding: 10px 18px;
            cursor: pointer;
        }

        .empty {
            padding: 20px;
            background: #f8f8f8;
            border: 1px solid #dddddd;
        }
    </style>
</head>
<body>
    <div class="top-links">
        <a href="task_sets_list.php">← К списку наборов</a>
        <a href="create_task_set.php">Создать набор</a>
        <a href="tasks_list.php">Список заданий</a>
    </div>
    <h1>Добавить задания в набор</h1>

    <?php if ($message !== ''): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (empty($formData['taskSets'])): ?>
        <div class="empty">Сначала создайте хотя бы один набор заданий.</div>
    <?php elseif (empty($formData['tasks'])): ?>
        <div class="empty">В системе пока нет заданий для добавления в набор.</div>
    <?php else: ?>
        <form method="POST">

            <div class="form-block">
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
                                    >
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <button type="submit" class="submit-btn">Добавить выбранные задания в набор</button>
        </form>
    <?php endif; ?>

</body>
</html>