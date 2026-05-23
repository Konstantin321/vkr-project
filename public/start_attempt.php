<?php

require_once __DIR__ . '/../app/auth/Auth.php';
Auth::requireAuth();

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

$pageTitle = 'Запуск попытки прохождения';
$activePage = 'control';
require __DIR__ . '/includes/layout_start.php';
?>

    <div class="top-links">
        <a href="task_sets_list.php">Список наборов заданий</a>
        <a href="tasks_list.php">Список заданий</a>
    </div>

    <div class="page-header">
        <div>
            <h1 class="page-title">Запуск попытки прохождения</h1>
            <p class="page-subtitle">Выберите набор заданий и начните контроль.</p>
        </div>
    </div>

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
        <form method="POST" class="form-section">
            <h2>Параметры запуска</h2>

            <label for="task_set_id">Выберите набор заданий</label>
            <select name="task_set_id" id="task_set_id" required>
                <option value="">Выберите набор</option>
                <?php foreach ($taskSets as $set): ?>
                    <option value="<?= (int)$set['id'] ?>">
                        <?= htmlspecialchars($set['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="form-actions">
                <button type="submit">Начать попытку</button>
            </div>
        </form>
    <?php endif; ?>

<?php require __DIR__ . '/includes/layout_end.php'; ?>
