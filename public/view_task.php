<?php

require_once __DIR__ . '/../app/controllers/TaskController.php';

$controller = new TaskController();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$task = $controller->show($id);

if (!$task) {
    die('Задание не найдено.');
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Просмотр задания</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            max-width: 900px;
        }

        h1 {
            margin-bottom: 20px;
        }

        h2 {
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 22px;
        }

        .top-links {
            margin-bottom: 20px;
        }

        .top-links a {
            margin-right: 15px;
            text-decoration: none;
            color: #0a58ca;
        }

        .card {
        border: 1px solid #dddddd;
        padding: 20px;
        background: #fafafa;
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

        .option-list {
            margin: 0;
            padding-left: 20px;
        }

        .correct-option {
            font-weight: bold;
            color: #157347;
        }
    </style>
</head>
<body>

    <div class="top-links">
        <a href="tasks_list.php">← Назад к списку заданий</a>
        <a href="edit_task.php?id=<?= (int)$task['id'] ?>">Редактировать</a>
        <a href="task_sets_list.php">Наборы заданий</a>
    </div>

    <h1>Просмотр задания</h1>

    <div class="card">
        <h2>Основная информация</h2>

        <div class="row">
            <span class="label">ID</span>
            <div class="value"><?= htmlspecialchars($task['id']) ?></div>
        </div>

        <div class="row">
            <span class="label">Название</span>
            <div class="value"><?= htmlspecialchars($task['title']) ?></div>
        </div>

        <div class="row">
            <span class="label">Тип задания</span>
            <div class="value"><?= htmlspecialchars($task['task_type_name']) ?></div>
        </div>

        <div class="row">
            <span class="label">Дисциплина</span>
            <div class="value"><?= htmlspecialchars($task['discipline_name']) ?></div>
        </div>

        <div class="row">
            <span class="label">Папка</span>
            <div class="value"><?= htmlspecialchars($task['folder_name'] ?? '—') ?></div>
        </div>

        <div class="row">
            <span class="label">Автор</span>
            <div class="value"><?= htmlspecialchars($task['author_name']) ?></div>
        </div>

        <div class="row">
            <span class="label">Дата создания</span>
            <div class="value"><?= htmlspecialchars($task['created_at']) ?></div>
        </div>
    </div>

    <div class="card">
        <h2>Содержимое задания</h2>

        <div class="row">
            <span class="label">Текст задания</span>
            <div class="value"><?= htmlspecialchars($task['task_text']) ?></div>
        </div>

        <?php if (!empty($task['options'])): ?>
            <div class="row">
                <span class="label">Варианты ответа</span>
                <ol class="option-list">
                    <?php foreach ($task['options'] as $option): ?>
                        <li class="<?= !empty($option['is_correct']) ? 'correct-option' : '' ?>">
                            <?= htmlspecialchars($option['option_text']) ?>
                            <?= !empty($option['is_correct']) ? ' — правильный' : '' ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2>Методические сведения</h2>

        <div class="row">
            <span class="label">Сложность</span>
            <div class="value"><?= htmlspecialchars($task['difficulty'] ?? '—') ?></div>
        </div>

        <div class="row">
            <span class="label">Назначение</span>
            <div class="value"><?= htmlspecialchars($task['purpose'] ?? '—') ?></div>
        </div>

        <div class="row">
            <span class="label">Эталонный ответ</span>
            <div class="value"><?= htmlspecialchars($task['reference_answer'] ?? '—') ?></div>
        </div>
    </div>

</body>
</html>
