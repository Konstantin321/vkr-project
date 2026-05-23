<?php

require_once __DIR__ . '/../app/controllers/TaskSetController.php';

$controller = new TaskSetController();
$message = '';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die('Некорректный идентификатор набора.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $controller->update($id, $_POST);
}

$data = $controller->show($id);

if (!$data) {
    die('Набор заданий не найден.');
}

$taskSet = $data['taskSet'];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование набора заданий</title>
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

        form {
            max-width: 700px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            box-sizing: border-box;
        }

        button {
            margin-top: 20px;
            padding: 10px 20px;
        }

        .message {
            margin-bottom: 20px;
            padding: 10px;
            background: #f0f0f0;
            border: 1px solid #dddddd;
        }
    </style>
</head>
<body>

    <div class="top-links">
        <a href="task_sets_list.php">← Назад к списку наборов</a>
        <a href="view_task_set.php?id=<?= (int)$taskSet['id'] ?>">Просмотр набора</a>
    </div>

    <h1>Редактирование набора заданий</h1>

    <?php if ($message !== ''): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="name">Название *</label>
        <input
            type="text"
            name="name"
            id="name"
            required
            value="<?= htmlspecialchars($taskSet['name']) ?>"
        >

        <label for="description">Описание</label>
        <textarea
            name="description"
            id="description"
            rows="5"
        ><?= htmlspecialchars($taskSet['description'] ?? '') ?></textarea>

        <label for="execution_time_minutes">Время выполнения (минуты) *</label>
        <input
            type="number"
            name="execution_time_minutes"
            id="execution_time_minutes"
            min="1"
            required
            value="<?= htmlspecialchars($taskSet['execution_time_minutes']) ?>"
        >

        <button type="submit">Сохранить изменения</button>
    </form>

</body>
</html>