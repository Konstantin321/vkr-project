<?php

require_once __DIR__ . '/../app/config/database.php';

$database = new Database();
$pdo = $database->connect();

$attemptId = isset($_GET['attempt_id']) ? (int)$_GET['attempt_id'] : 0;

if ($attemptId <= 0) {
    die('Некорректный идентификатор попытки.');
}

$sql = "
    SELECT 
        r.total_score,
        r.grade,
        r.score_breakdown,
        ts.name AS task_set_name
    FROM results r
    JOIN attempts a ON a.id = r.attempt_id
    JOIN task_sets ts ON ts.id = a.task_set_id
    WHERE r.attempt_id = :attempt_id
";

$stmt = $pdo->prepare($sql);
$stmt->execute([':attempt_id' => $attemptId]);

$result = $stmt->fetch();

if (!$result) {
    die('Результат не найден.');
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Результат прохождения</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }

        h1 {
            margin-bottom: 20px;
        }

        .card {
            border: 1px solid #ddd;
            padding: 20px;
            max-width: 700px;
            background: #fafafa;
        }

        .row {
            margin-bottom: 15px;
        }

        .label {
            font-weight: bold;
        }

        .links {
            margin-top: 20px;
        }

        .links a {
            display: inline-block;
            margin-right: 15px;
            text-decoration: none;
            color: #0a58ca;
        }
    </style>
</head>
<body>

<h1>Результат прохождения</h1>

<div class="card">
    <div class="row">
        <span class="label">Набор заданий:</span><br>
        <?= htmlspecialchars($result['task_set_name']) ?>
    </div>

    <div class="row">
        <span class="label">Баллы:</span><br>
        <?= (float)$result['total_score'] ?>
    </div>

    <div class="row">
        <span class="label">Оценка:</span><br>
        <?= htmlspecialchars($result['grade']) ?>
    </div>

    <div class="row">
        <span class="label">Разбаловка по заданиям:</span><br>
        <?= htmlspecialchars($result['score_breakdown'] ?? 'Разбаловка отсутствует') ?>
    </div>
</div>

<div class="links">
    <a href="view_answers.php?attempt_id=<?= $attemptId ?>">Просмотреть ответы</a>
    <a href="start_attempt.php">Запустить новую попытку</a>
    <a href="attempts_list.php">Список попыток</a>
</div>

</body>
</html>