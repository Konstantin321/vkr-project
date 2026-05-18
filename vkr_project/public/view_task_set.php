<?php

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
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Просмотр набора заданий</title>
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
            margin-right: 15px;
            text-decoration: none;
            color: #0a58ca;
        }

        .message {
            margin-bottom: 15px;
            padding: 10px;
            background: #eeeeee;
            border: 1px solid #dddddd;
        }

        .card {
            border: 1px solid #dddddd;
            background: #fafafa;
            padding: 20px;
            margin-bottom: 25px;
            max-width: 900px;
        }

        .row {
            margin-bottom: 15px;
        }

        .label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .value {
            white-space: pre-wrap;
            line-height: 1.5;
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

        .delete-btn {
            background: #c82333;
            color: white;
            border: none;
            padding: 6px 10px;
            cursor: pointer;
        }

        .delete-btn:hover {
            background: #a71d2a;
        }

        .delete-form {
            margin: 0;
        }
    </style>
</head>
<body>

    <div class="top-links">
        <a href="task_sets_list.php">← Назад к списку наборов</a>
        <a href="edit_task_set.php?id=<?= (int)$taskSet['id'] ?>">Редактировать набор</a>
        <a href="add_task_to_set.php">Добавить задания в набор</a>
    </div>

    <h1>Просмотр набора заданий</h1>

    <?php if (!empty($message)): ?>
        <div class="message">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="row">
            <span class="label">ID</span>
            <div class="value"><?= htmlspecialchars($taskSet['id']) ?></div>
        </div>

        <div class="row">
            <span class="label">Название</span>
            <div class="value"><?= htmlspecialchars($taskSet['name']) ?></div>
        </div>

        <div class="row">
            <span class="label">Описание</span>
            <div class="value"><?= htmlspecialchars($taskSet['description'] ?? '—') ?></div>
        </div>

        <div class="row">
            <span class="label">Время выполнения (минуты)</span>
            <div class="value"><?= htmlspecialchars($taskSet['execution_time_minutes']) ?></div>
        </div>

        <div class="row">
            <span class="label">Автор</span>
            <div class="value"><?= htmlspecialchars($taskSet['author_name']) ?></div>
        </div>

        <div class="row">
            <span class="label">Дата создания</span>
            <div class="value"><?= htmlspecialchars($taskSet['created_at']) ?></div>
        </div>
    </div>

    <h2>Задания в наборе</h2>

    <?php if (empty($items)): ?>
        <div class="empty">В этот набор пока не добавлены задания.</div>
    <?php else: ?>
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
                            <form method="POST" style="margin: 0; display: inline;">
                                <input type="hidden" name="action" value="update_item">
                                <input type="hidden" name="item_id" value="<?= (int)$item['id'] ?>">

                                <input
                                    type="number"
                                    name="order_number"
                                    min="1"
                                    value="<?= htmlspecialchars($item['order_number']) ?>"
                                    style="width: 80px; padding: 5px;"
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
                                    style="width: 90px; padding: 5px;"
                                    required
                                >
                        </td>
                        <td style="white-space: nowrap;">
                                <button
                                    type="submit"
                                    style="background: #0d6efd; color: white; border: none; padding: 6px 10px; cursor: pointer; margin-right: 8px;"
                                >
                                    Сохранить
                                </button>
                            </form>

                            <form method="POST" class="delete-form" style="display: inline;" onsubmit="return confirm('Удалить задание из набора?');">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="item_id" value="<?= (int)$item['id'] ?>">
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