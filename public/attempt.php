<?php

require_once __DIR__ . '/../app/controllers/AttemptController.php';

$controller = new AttemptController();

$attemptId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $controller->submitAnswers($attemptId, $_POST);

    if ($message === 'Попытка завершена, ответы сохранены.') {
        header('Location: result.php?attempt_id=' . $attemptId);
        exit;
    }
}

$data = $controller->show($attemptId);

if (!$data) {
    die('Попытка не найдена.');
}

$taskSetName = $data[0]['task_set_name'];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Прохождение теста</title>
    <style>
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
            background: #eeeeee;
            border: 1px solid #dddddd;
            max-width: 900px;
        }

        .task {
            border: 1px solid #dddddd;
            padding: 20px;
            margin-bottom: 20px;
            background: #fafafa;
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

        .meta {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }

        textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        button {
            margin-top: 20px;
            padding: 10px 20px;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <h1>Прохождение набора: <?= htmlspecialchars($taskSetName) ?></h1>

    <?php if ($message !== ''): ?>
        <div class="message">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <?php foreach ($data as $task): ?>
            <div class="task">
                <div class="task-title">
                    Задание <?= (int)$task['order_number'] ?>: <?= htmlspecialchars($task['title']) ?>
                </div>

                <div class="task-text">
                    <?= htmlspecialchars($task['task_text']) ?>
                </div>

                <div class="meta">
                    Максимум баллов: <?= (float)$task['max_score'] ?>
                </div>

                <div>
                    <label>Ваш ответ:</label><br>
                    <textarea
                        name="answers[<?= (int)$task['task_id'] ?>]"
                        rows="4"
                    ></textarea>
                </div>
            </div>
        <?php endforeach; ?>

        <button type="submit">
            Завершить попытку
        </button>

    </form>

</body>
</html>