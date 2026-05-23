<?php

require_once __DIR__ . '/../app/controllers/TaskSetController.php';

$controller = new TaskSetController();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $controller->store($_POST);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Создание набора заданий</title>
    <style>
        body { font-family: Arial; margin: 40px; }
        input, textarea { width: 100%; padding: 8px; margin-top: 5px; }
        label { margin-top: 15px; display: block; font-weight: bold; }
        button { margin-top: 20px; padding: 10px 15px; }
        .message { margin-bottom: 15px; padding: 10px; background: #eee; }
    </style>
</head>
<body>

<h1>Создание набора заданий</h1>

<?php if ($message): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<form method="POST">

    <label>Название *</label>
    <input type="text" name="name" required>

    <label>Описание</label>
    <textarea name="description"></textarea>

    <label>Время выполнения (минуты) *</label>
    <input type="number" name="execution_time_minutes" required>

    <button type="submit">Создать набор</button>

</form>

</body>
</html>