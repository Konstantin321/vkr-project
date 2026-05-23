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

        .answer-option {
            display: block;
            margin-top: 8px;
            line-height: 1.4;
        }

        .answer-option input {
            margin-right: 8px;
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
                    <?php if (in_array((int)$task['task_type_id'], [2, 3], true) && empty($task['options'])): ?>
                        <div class="meta">
                            Для этого задания варианты ответа ещё не заданы. Введите ответ текстом.
                        </div>
                        <textarea
                            name="answers[<?= (int)$task['task_id'] ?>]"
                            rows="4"
                        ></textarea>
                    <?php elseif ((int)$task['task_type_id'] === 2): ?>
                        <input
                            type="hidden"
                            name="answers[<?= (int)$task['task_id'] ?>]"
                            value=""
                        >
                        <?php foreach ($task['options'] as $option): ?>
                            <label class="answer-option">
                                <input
                                    type="radio"
                                    name="answers[<?= (int)$task['task_id'] ?>]"
                                    value="<?= (int)$option['id'] ?>"
                                >
                                <?= htmlspecialchars($option['option_text']) ?>
                            </label>
                        <?php endforeach; ?>
                    <?php elseif ((int)$task['task_type_id'] === 3): ?>
                        <input
                            type="hidden"
                            name="answers[<?= (int)$task['task_id'] ?>][]"
                            value=""
                        >
                        <?php foreach ($task['options'] as $option): ?>
                            <label class="answer-option">
                                <input
                                    type="checkbox"
                                    name="answers[<?= (int)$task['task_id'] ?>][]"
                                    value="<?= (int)$option['id'] ?>"
                                >
                                <?= htmlspecialchars($option['option_text']) ?>
                            </label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <textarea
                            name="answers[<?= (int)$task['task_id'] ?>]"
                            rows="4"
                        ></textarea>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <button type="submit">
            Завершить попытку
        </button>

    </form>

</body>
</html>
