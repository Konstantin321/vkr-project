<?php

require_once __DIR__ . '/../app/auth/Auth.php';
Auth::requireAuth();
Auth::requireRole(['student', 'teacher']);

require_once __DIR__ . '/../app/config/database.php';

$database = new Database();
$pdo = $database->connect();

$sql = "
    SELECT
        a.id AS attempt_id,
        a.status,
        a.started_at,
        a.finished_at,
        ts.name AS task_set_name,
        u.full_name AS student_name,
        r.total_score,
        r.grade
    FROM attempts a
    JOIN task_sets ts ON ts.id = a.task_set_id
    JOIN users u ON u.id = a.student_id
    LEFT JOIN results r ON r.attempt_id = a.id
";

$params = [];

if (Auth::role() === 'student') {
    $sql .= ' WHERE a.student_id = :student_id';
    $params[':student_id'] = Auth::id();
}

$sql .= ' ORDER BY a.id DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$attempts = $stmt->fetchAll();

$pageTitle = 'Результаты';
$activePage = 'results';
require __DIR__ . '/includes/layout_start.php';
?>

    <div class="top-links">
        <a href="attempts_list.php">Список попыток</a>
        <?php if (Auth::hasRole(['student'])): ?>
            <a href="start_attempt.php">Запуск контроля</a>
        <?php endif; ?>
    </div>

    <div class="page-header">
        <div>
            <h1 class="page-title">Результаты</h1>
            <p class="page-subtitle">Список попыток с итоговыми баллами и переходом к подробному результату.</p>
        </div>
    </div>

    <?php if (empty($attempts)): ?>
        <div class="empty">Попытки пока отсутствуют.</div>
    <?php else: ?>
        <div class="card table-card">
            <div class="table-responsive">
                <table>
                    <thead>
                    <tr>
                        <th>ID попытки</th>
                        <th>Набор заданий</th>
                        <th>Обучающийся</th>
                        <th>Статус</th>
                        <th>Баллы</th>
                        <th>Оценка</th>
                        <th>Завершение</th>
                        <th>Действие</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($attempts as $attempt): ?>
                        <?php $hasResult = $attempt['total_score'] !== null || $attempt['grade'] !== null; ?>
                        <tr>
                            <td><?= htmlspecialchars($attempt['attempt_id']) ?></td>
                            <td><?= htmlspecialchars($attempt['task_set_name']) ?></td>
                            <td><?= htmlspecialchars($attempt['student_name']) ?></td>
                            <td>
                                <span class="status-badge <?= $attempt['status'] === 'completed' ? 'is-success' : 'is-muted' ?>">
                                    <?= htmlspecialchars($attempt['status']) ?>
                                </span>
                            </td>
                            <td><?= $attempt['total_score'] !== null ? htmlspecialchars($attempt['total_score']) : '—' ?></td>
                            <td><?= htmlspecialchars($attempt['grade'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($attempt['finished_at'] ?? '—') ?></td>
                            <td>
                                <?php if ($hasResult): ?>
                                    <a href="result.php?attempt_id=<?= (int)$attempt['attempt_id'] ?>" class="view-btn">
                                        Открыть результат
                                    </a>
                                <?php else: ?>
                                    <span class="muted-text">Результат ещё не создан</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

<?php require __DIR__ . '/includes/layout_end.php'; ?>
