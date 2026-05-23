<?php

require_once __DIR__ . '/../app/auth/Auth.php';
Auth::requireAuth();
Auth::requireRole(['student', 'teacher']);

require_once __DIR__ . '/../app/controllers/AttemptController.php';

$controller = new AttemptController();

$attemptId = isset($_GET['attempt_id']) ? (int)$_GET['attempt_id'] : 0;
$data = $controller->viewAnswers($attemptId);

if (!$data) {
    die('Ответы по данной попытке не найдены.');
}

$attemptInfo = $data[0];

$pageTitle = 'Просмотр ответов';
$activePage = 'results';
require __DIR__ . '/includes/layout_start.php';
?>

    <div class="top-links">
        <a href="result.php?attempt_id=<?= (int)$attemptInfo['attempt_id'] ?>">Результат попытки</a>
        <a href="start_attempt.php">Запуск новой попытки</a>
    </div>

    <div class="page-header">
        <div>
            <h1 class="page-title">Просмотр ответов</h1>
            <p class="page-subtitle">Ответы обучающегося и сведения о попытке.</p>
        </div>
    </div>

    <div class="card">
        <h2>Информация о попытке</h2>

        <div class="task-detail-row">
            <span class="task-detail-label">Набор заданий</span>
            <div class="task-detail-value"><?= htmlspecialchars($attemptInfo['task_set_name']) ?></div>
        </div>

        <div class="task-detail-row">
            <span class="task-detail-label">Статус попытки</span>
            <div class="task-detail-value"><?= htmlspecialchars($attemptInfo['status']) ?></div>
        </div>

        <div class="task-detail-row">
            <span class="task-detail-label">Время начала</span>
            <div class="task-detail-value"><?= htmlspecialchars($attemptInfo['started_at']) ?></div>
        </div>

        <div class="task-detail-row">
            <span class="task-detail-label">Время завершения</span>
            <div class="task-detail-value"><?= htmlspecialchars($attemptInfo['finished_at'] ?? '—') ?></div>
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

<?php require __DIR__ . '/includes/layout_end.php'; ?>
