<?php

require_once __DIR__ . '/../app/controllers/AttemptController.php';

$controller = new AttemptController();
$attempts = $controller->index();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список попыток</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
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

        .view-btn {
            display: inline-block;
            padding: 8px 12px;
            text-decoration: none;
            color: white;
            background-color: #198754;
        }

        .view-btn:hover {
            background-color: #157347;
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
        <a href="start_attempt.php">Запуск попытки</a>
        <a href="task_sets_list.php">Список наборов</a>
    </div>

    <h1>Список попыток</h1>

    <?php if (empty($attempts)): ?>
        <div class="empty">Попытки пока отсутствуют.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Набор заданий</th>
                    <th>Обучающийся</th>
                    <th>Статус</th>
                    <th>Начало</th>
                    <th>Завершение</th>
                    <th>Действие</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attempts as $attempt): ?>
                    <tr>
                        <td><?= htmlspecialchars($attempt['id']) ?></td>
                        <td><?= htmlspecialchars($attempt['task_set_name']) ?></td>
                        <td><?= htmlspecialchars($attempt['student_name']) ?></td>
                        <td><?= htmlspecialchars($attempt['status']) ?></td>
                        <td><?= htmlspecialchars($attempt['started_at']) ?></td>
                        <td><?= htmlspecialchars($attempt['finished_at'] ?? '—') ?></td>
                        <td>
                            <a href="review_attempt.php?attempt_id=<?= (int)$attempt['id'] ?>" class="view-btn">
                                Проверить ответы
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</body>
</html>