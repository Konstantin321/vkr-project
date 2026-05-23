<?php

require_once __DIR__ . '/../app/auth/Auth.php';
Auth::requireAuth();
Auth::requireRole(['teacher']);

require_once __DIR__ . '/../app/controllers/AttemptController.php';

$controller = new AttemptController();
$attempts = $controller->index();

$pageTitle = 'Проверка ответов';
$activePage = 'review';
require __DIR__ . '/includes/layout_start.php';
?>

    <div class="top-links">
        <a href="attempts_list.php">Список попыток</a>
        <a href="results_list.php">Результаты</a>
    </div>

    <div class="page-header">
        <div>
            <h1 class="page-title">Проверка ответов</h1>
            <p class="page-subtitle">Выберите завершённую попытку, чтобы выставить баллы и комментарии.</p>
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
                        <th>ID</th>
                        <th>Набор заданий</th>
                        <th>Обучающийся</th>
                        <th>Статус</th>
                        <th>Начало</th>
                        <th>Завершение</th>
                        <th>Действие</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($attempts as $attempt): ?>
                        <tr>
                            <td><?= htmlspecialchars($attempt['id']) ?></td>
                            <td><?= htmlspecialchars($attempt['task_set_name']) ?></td>
                            <td><?= htmlspecialchars($attempt['student_name']) ?></td>
                            <td>
                                <span class="status-badge <?= $attempt['status'] === 'completed' ? 'is-success' : 'is-muted' ?>">
                                    <?= htmlspecialchars($attempt['status']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($attempt['started_at']) ?></td>
                            <td><?= htmlspecialchars($attempt['finished_at'] ?? '—') ?></td>
                            <td>
                                <?php if ($attempt['status'] === 'completed'): ?>
                                    <a href="review_attempt.php?attempt_id=<?= (int)$attempt['id'] ?>" class="view-btn">
                                        Проверить ответы
                                    </a>
                                <?php else: ?>
                                    <span class="muted-text">Попытка ещё не завершена</span>
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
