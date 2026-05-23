<?php

require_once __DIR__ . '/../app/controllers/AttemptController.php';

$controller = new AttemptController();
$message = '';

$attemptId = isset($_GET['attempt_id']) ? (int)$_GET['attempt_id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_review') {
        $message = $controller->reviewAnswer($_POST);
    } elseif ($action === 'finish_review') {
        $message = $controller->finishReview((int)($_POST['attempt_id'] ?? 0));
    }
}

$data = $controller->viewAnswers($attemptId);

if (!$data) {
    die('Попытка не найдена.');
}

$attemptInfo = $data[0];
$isReviewFinished = $controller->isReviewFinished($attemptId);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Проверка ответов</title>
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
            background: #eeeeee;
            border: 1px solid #dddddd;
            max-width: 900px;
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
            margin-top: 10px;
            margin-bottom: 15px;
            padding: 15px;
            background: #f6f6f6;
            border: 1px solid #dddddd;
            white-space: pre-wrap;
        }

        .saved-score {
            margin-bottom: 15px;
            padding: 10px;
            background: #f6f6f6;
            border: 1px solid #dddddd;
        }

        .saved-comment {
            margin-bottom: 15px;
            padding: 10px;
            background: #eef6ff;
            border: 1px solid #cfe2ff;
            white-space: pre-wrap;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input, textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            margin-top: 5px;
        }

        button {
            margin-top: 15px;
            padding: 10px 18px;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <div class="top-links">
        <a href="attempts_list.php">Список попыток</a>
        <a href="view_answers.php?attempt_id=<?= (int)$attemptInfo['attempt_id'] ?>">Просмотр ответов</a>
        <a href="result.php?attempt_id=<?= (int)$attemptInfo['attempt_id'] ?>">Результат попытки</a>
    </div>

    <h1>Проверка ответов по попытке #<?= (int)$attemptInfo['attempt_id'] ?></h1>

    <?php if ($isReviewFinished): ?>
        <div class="message" style="background: #e8f5e9; border: 1px solid #c8e6c9;">
            Проверка этой попытки уже завершена. Редактирование недоступно.
        </div>
    <?php endif; ?>

    <?php if ($message !== ''): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php foreach ($data as $item): ?>
        <div class="task">
            <div class="task-title">
                Задание <?= (int)$item['order_number'] ?>: <?= htmlspecialchars($item['title']) ?>
            </div>

            <div class="task-text">
                <?= htmlspecialchars($item['task_text']) ?>
            </div>

            <div class="answer">
                <strong>Ответ обучающегося:</strong><br>
                <?= htmlspecialchars($item['answer_text'] ?? 'Ответ отсутствует') ?>
            </div>

            <div class="saved-score">
                <strong>Текущий сохранённый балл:</strong>
                <?= $item['score'] !== null ? htmlspecialchars($item['score']) : 'ещё не выставлен' ?>
            </div>

            <div class="saved-comment">
                <strong>Комментарий преподавателя:</strong><br>
                <?= htmlspecialchars($item['comment_text'] ?? 'Комментарий отсутствует') ?>
            </div>

            <form method="POST">
                <input type="hidden" name="action" value="save_review">
                <input type="hidden" name="attempt_id" value="<?= (int)$attemptInfo['attempt_id'] ?>">
                <input type="hidden" name="task_id" value="<?= (int)$item['task_id'] ?>">

                <label>Балл</label>
                <input
                    type="number"
                    name="score"
                    min="0"
                    step="0.5"
                    required
                    value="<?= htmlspecialchars($item['score'] ?? '') ?>"
                    <?= $isReviewFinished ? 'readonly' : '' ?>
                >

                <label>Комментарий преподавателя</label>
                <textarea
                    name="comment_text"
                    rows="4"
                    <?= $isReviewFinished ? 'readonly' : '' ?>
                ><?= htmlspecialchars($item['comment_text'] ?? '') ?></textarea>

                <?php if (!$isReviewFinished): ?>
                    <button type="submit">Сохранить проверку</button>
                <?php endif; ?>
            </form>
        </div>
    <?php endforeach; ?>

    <?php if (!$isReviewFinished): ?>
        <form method="POST" style="max-width: 900px; margin-top: 30px;">
            <input type="hidden" name="action" value="finish_review">
            <input type="hidden" name="attempt_id" value="<?= (int)$attemptInfo['attempt_id'] ?>">

            <button type="submit" style="padding: 12px 20px; font-size: 16px;">
                Завершить проверку
            </button>
        </form>
    <?php endif; ?>

</body>
</html>