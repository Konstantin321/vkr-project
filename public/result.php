<?php

require_once __DIR__ . '/../app/auth/Auth.php';
Auth::requireAuth();
Auth::requireRole(['student', 'teacher']);

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

$params = [':attempt_id' => $attemptId];

if (Auth::role() === 'student') {
    $sql .= ' AND a.student_id = :student_id';
    $params[':student_id'] = Auth::id();
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$result = $stmt->fetch();

if (!$result) {
    die('Результат не найден.');
}

$pageTitle = 'Результат прохождения';
$activePage = 'results';
require __DIR__ . '/includes/layout_start.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Результат прохождения</h1>
        <p class="page-subtitle">Итоговые баллы, оценка и разбаловка по заданиям.</p>
    </div>
</div>

<div class="card">
    <h2>Итог</h2>

    <div class="task-detail-row">
        <span class="task-detail-label">Набор заданий</span>
        <div class="task-detail-value"><?= htmlspecialchars($result['task_set_name']) ?></div>
    </div>

    <div class="task-detail-row">
        <span class="task-detail-label">Баллы</span>
        <div class="task-detail-value"><?= (float)$result['total_score'] ?></div>
    </div>

    <div class="task-detail-row">
        <span class="task-detail-label">Оценка</span>
        <div class="task-detail-value"><?= htmlspecialchars($result['grade']) ?></div>
    </div>

    <div class="task-detail-row">
        <span class="task-detail-label">Разбаловка по заданиям</span>
        <div class="task-detail-value"><?= htmlspecialchars($result['score_breakdown'] ?? 'Разбаловка отсутствует') ?></div>
    </div>
</div>

<div class="links">
    <a href="view_answers.php?attempt_id=<?= $attemptId ?>">Просмотреть ответы</a>
    <a href="start_attempt.php">Запустить новую попытку</a>
    <a href="attempts_list.php">Список попыток</a>
</div>

<?php require __DIR__ . '/includes/layout_end.php'; ?>
