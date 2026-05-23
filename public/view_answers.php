<?php

require_once __DIR__ . '/../app/controllers/AttemptController.php';

$controller = new AttemptController();

$attemptId = isset($_GET['attempt_id']) ? (int)$_GET['attempt_id'] : 0;
$data = $controller->viewAnswers($attemptId);

if (!$data) {
    die('Ответы по данной попытке не найдены.');
}

$attemptInfo = $data[0];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Просмотр ответов</title>
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

        .task {
            border: 1px solid #dddddd;
            background: #ffffff;
            padding: 20px;
            margin-bottom: 20px;
            max-width: 900px;
        }

        .task-title {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .task-text {
            white-space: pre-wrap;
            margin-bottom: 10px;
        }

        .answer {
            margin-top: 15px;
            padding: 15px;
            background: #f6f6f6;
            border: 1px solid #dddddd;
            white-space: pre-wrap;
        }

        .meta {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <div class="top-links">
        <a href="result.php?attempt_id=<?= (int)$attemptInfo['attempt_id'] ?>">Результат попытки</a>
        <a href="start_attempt.php">Запуск новой попытки</a>
    </div>

    <h1>Просмотр ответов</h1>

    <div class="card">
        <div class="row">
            <span class="label">Набор заданий</span>
            <div class="value"><?= htmlspecialchars($attemptInfo['task_set_name']) ?></div>
        </div>

        <div class="row">
            <span class="label">Статус попытки</span>
            <div class="value"><?= htmlspecialchars($attemptInfo['status']) ?></div>
        </div>

        <div class="row">
            <span class="label">Время начала</span>
            <div class="value"><?= htmlspecialchars($attemptInfo['started_at']) ?></div>
        </div>

        <div class="row">
            <span class="label">Время завершения</span>
            <div class="value"><?= htmlspecialchars($attemptInfo['finished_at'] ?? '—') ?></div>
        </div>
    </div>

    <?php foreach ($data as $item): ?>
        <div class="task">
            <div class="task-title">
                Задание <?= (int)$item['order_number'] ?>: <?= htmlspecialchars($item['title']) ?>
            </div>

            <div class="task-text">
                <?= htmlspecialchars($item['task_text']) ?>
            </div>

            <div class="meta">
                Максимум баллов: <?= (float)$item['max_score'] ?>
            </div>

            <div>
                <strong>Ответ обучающегося:</strong>
                <div class="answer">
                    <?= htmlspecialchars($item['answer_text'] ?? 'Ответ отсутствует') ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

</body>
</html>