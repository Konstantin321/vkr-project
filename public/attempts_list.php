<?php

require_once __DIR__ . '/../app/auth/Auth.php';
Auth::requireAuth();
Auth::requireRole(['student', 'teacher']);

require_once __DIR__ . '/../app/controllers/AttemptController.php';

$controller = new AttemptController();
$attempts = $controller->index();

$pageTitle = 'Список попыток';
$activePage = 'attempts';
require __DIR__ . '/includes/layout_start.php';
?>

    <div class="top-links">
        <?php if (Auth::hasRole(['student'])): ?>
            <a href="start_attempt.php">Запуск попытки</a>
        <?php endif; ?>
        <?php if (Auth::hasRole(['teacher'])): ?>
            <a href="task_sets_list.php">Список наборов</a>
        <?php endif; ?>
    </div>

    <div class="page-header">
        <div>
            <h1 class="page-title">Список попыток</h1>
            <p class="page-subtitle">Просмотр попыток прохождения и переход к доступным действиям.</p>
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
                            <td><?= htmlspecialchars($attempt['status']) ?></td>
                            <td><?= htmlspecialchars($attempt['started_at']) ?></td>
                            <td><?= htmlspecialchars($attempt['finished_at'] ?? '—') ?></td>
                            <td>
                                <?php if (Auth::hasRole(['teacher'])): ?>
                                    <a href="review_attempt.php?attempt_id=<?= (int)$attempt['id'] ?>" class="view-btn">
                                        Проверить ответы
                                    </a>
                                <?php elseif ($attempt['status'] === 'completed'): ?>
                                    <a href="result.php?attempt_id=<?= (int)$attempt['id'] ?>" class="view-btn">
                                        Открыть результат
                                    </a>
                                <?php else: ?>
                                    <a href="attempt.php?id=<?= (int)$attempt['id'] ?>" class="edit-btn">
                                        Продолжить
                                    </a>
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
