<?php

require_once __DIR__ . '/../app/auth/Auth.php';
Auth::requireAuth();
Auth::requireRole(['teacher']);

require_once __DIR__ . '/../app/controllers/AttemptController.php';

$controller = new AttemptController();
$message = '';

$attemptId = isset($_GET['attempt_id']) ? (int)$_GET['attempt_id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_review') {
        $message = $controller->reviewAnswer($_POST);
    } elseif ($action === 'finish_review') {
        $message = $controller->finishReview((int)($_POST['attempt_id'] ?? 0));
    }
}

$data = $controller->viewAnswers($attemptId);

if (!$data) {
    die('Попытка не найдена.');
}

$attemptInfo = $data[0];
$isReviewFinished = $controller->isReviewFinished($attemptId);

$pageTitle = 'Проверка ответов';
$activePage = 'review';
require __DIR__ . '/includes/layout_start.php';
?>

    <div class="top-links">
        <a href="attempts_list.php">Список попыток</a>
        <a href="view_answers.php?attempt_id=<?= (int)$attemptInfo['attempt_id'] ?>">Просмотр ответов</a>
        <a href="result.php?attempt_id=<?= (int)$attemptInfo['attempt_id'] ?>">Результат попытки</a>
    </div>

    <div class="page-header">
        <div>
            <h1 class="page-title">Проверка ответов</h1>
            <p class="page-subtitle">Попытка #<?= (int)$attemptInfo['attempt_id'] ?>: выставление баллов и комментариев.</p>
        </div>
    </div>

    <?php if ($isReviewFinished): ?>
        <div class="message">
            Проверка этой попытки уже завершена. Редактирование недоступно.
        </div>
    <?php endif; ?>

    <?php if ($message !== ''): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php foreach ($data as $item): ?>
        <div class="task">
            <div class="task-title">
                Задание <?= (int)$item['order_number'] ?>: <?= htmlspecialchars($item['title']) ?>
            </div>

            <div class="task-text">
                <?= htmlspecialchars($item['task_text']) ?>
            </div>

            <div class="answer">
                <strong>Ответ обучающегося:</strong><br>
                <?= htmlspecialchars($item['answer_text'] ?? 'Ответ отсутствует') ?>
            </div>

            <div class="saved-score">
                <strong>Текущий сохранённый балл:</strong>
                <?= $item['score'] !== null ? htmlspecialchars($item['score']) : 'ещё не выставлен' ?>
            </div>

            <div class="saved-comment">
                <strong>Комментарий преподавателя:</strong><br>
                <?= htmlspecialchars($item['comment_text'] ?? 'Комментарий отсутствует') ?>
            </div>

            <form method="POST">
                <input type="hidden" name="action" value="save_review">
                <input type="hidden" name="attempt_id" value="<?= (int)$attemptInfo['attempt_id'] ?>">
                <input type="hidden" name="task_id" value="<?= (int)$item['task_id'] ?>">

                <label>Балл</label>
                <input
                    type="number"
                    name="score"
                    min="0"
                    step="0.5"
                    required
                    value="<?= htmlspecialchars($item['score'] ?? '') ?>"
                    <?= $isReviewFinished ? 'readonly' : '' ?>
                >

                <label>Комментарий преподавателя</label>
                <textarea
                    name="comment_text"
                    rows="4"
                    <?= $isReviewFinished ? 'readonly' : '' ?>
                ><?= htmlspecialchars($item['comment_text'] ?? '') ?></textarea>

                <?php if (!$isReviewFinished): ?>
                    <button type="submit">Сохранить проверку</button>
                <?php endif; ?>
            </form>
        </div>
    <?php endforeach; ?>

    <?php if (!$isReviewFinished): ?>
        <form method="POST" class="form-section">
            <h2>Завершение проверки</h2>
            <input type="hidden" name="action" value="finish_review">
            <input type="hidden" name="attempt_id" value="<?= (int)$attemptInfo['attempt_id'] ?>">

            <button type="submit">
                Завершить проверку
            </button>
        </form>
    <?php endif; ?>

<?php require __DIR__ . '/includes/layout_end.php'; ?>
