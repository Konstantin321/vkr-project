<?php

require_once __DIR__ . '/../app/controllers/AttemptController.php';

$controller = new AttemptController();
$message = '';
$attemptId = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $controller->start($_POST);

    if ($result['success']) {
        header('Location: attempt.php?id=' . $result['attempt_id']);
        exit;
    }

    $message = $result['message'];
}
$taskSets = $controller->getTaskSets();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Запуск попытки прохождения</title>
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
            max-width: 700px;
        }

        .form-block {
            max-width: 700px;
            padding: 20px;
            border: 1px solid #dddddd;
            background: #fafafa;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
        }

        button {
            padding: 10px 18px;
            cursor: pointer;
        }

        .empty {
            padding: 20px;
            background: #f8f8f8;
            border: 1px solid #dddddd;
            max-width: 700px;
        }
    </style>
</head>
<body>

    <div class="top-links">
        <a href="task_sets_list.php">Список наборов заданий</a>
        <a href="tasks_list.php">Список заданий</a>
    </div>

    <h1>Запуск попытки прохождения</h1>

    <?php if ($message !== ''): ?>
        <div class="message">
            <?= htmlspecialchars($message) ?>
            <?php if ($attemptId !== null): ?>
                <br><br>
                ID созданной попытки: <?= (int)$attemptId ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (empty($taskSets)): ?>
        <div class="empty">Нет доступных наборов заданий для запуска.</div>
    <?php else: ?>
        <form method="POST" class="form-block">
            <label for="task_set_id">Выберите набор заданий</label>
            <select name="task_set_id" id="task_set_id" required>
                <option value="">Выберите набор</option>
                <?php foreach ($taskSets as $set): ?>
                    <option value="<?= (int)$set['id'] ?>">
                        <?= htmlspecialchars($set['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Начать попытку</button>
        </form>
    <?php endif; ?>

</body>
</html>