<?php

require_once __DIR__ . '/../app/controllers/TaskSetController.php';

$controller = new TaskSetController();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $message = $controller->delete((int)($_POST['delete_id'] ?? 0));
}

$taskSets = $controller->index();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список наборов заданий</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }

        h1 {
            margin-bottom: 20px;
        }

        .top-links {
            margin-bottom: 20px;
        }

        .top-links a {
            display: inline-block;
            margin-right: 15px;
            text-decoration: none;
            color: #0a58ca;
        }

        .message {
            margin-bottom: 20px;
            padding: 10px;
            background: #f0f0f0;
            border: 1px solid #dddddd;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #cccccc;
            padding: 10px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f3f3f3;
        }

        .empty {
            padding: 20px;
            background: #f8f8f8;
            border: 1px solid #dddddd;
        }

        .view-btn {
            display: inline-block;
            padding: 8px 12px;
            text-decoration: none;
            color: white;
            background-color: #198754;
            margin-right: 8px;
        }

        .view-btn:hover {
            background-color: #157347;
        }

        .edit-btn {
            display: inline-block;
            padding: 8px 12px;
            text-decoration: none;
            color: white;
            background-color: #0d6efd;
            margin-right: 8px;
        }

        .edit-btn:hover {
            background-color: #0b5ed7;
        }

        .delete-btn {
            display: inline-block;
            padding: 8px 12px;
            text-decoration: none;
            color: white;
            background-color: #c82333;
            border: none;
            cursor: pointer;
        }

        .delete-btn:hover {
            background-color: #a71d2a;
        }

        .delete-form {
            display: inline;
        }
    </style>
</head>
<body>

    <h1>Список наборов заданий</h1>

    <div class="top-links">
        <a href="create_task_set.php">Создать набор заданий</a>
        <a href="add_task_to_set.php">Добавить задания в набор</a>
        <a href="tasks_list.php">Список заданий</a>
    </div>

    <?php if ($message !== ''): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (empty($taskSets)): ?>
        <div class="empty">Наборы заданий пока не созданы.</div>
    <?php else: ?>
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
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</body>
</html>