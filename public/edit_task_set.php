<?php

require_once __DIR__ . '/../app/auth/Auth.php';
Auth::requireAuth();
Auth::requireRole(['teacher']);

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

$pageTitle = 'Редактирование набора заданий';
$activePage = 'sets';
require __DIR__ . '/includes/layout_start.php';
?>

    <div class="top-links">
        <a href="task_sets_list.php">Назад к списку наборов</a>
        <a href="view_task_set.php?id=<?= (int)$taskSet['id'] ?>">Просмотр набора</a>
    </div>

    <div class="page-header">
        <div>
            <h1 class="page-title">Редактирование набора заданий</h1>
            <p class="page-subtitle">Изменение названия, описания и времени выполнения.</p>
        </div>
    </div>

    <?php if ($message !== ''): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" class="form-section">
        <h2>Основные сведения</h2>

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

        <div class="form-actions">
            <button type="submit">Сохранить изменения</button>
        </div>
    </form>

<?php require __DIR__ . '/includes/layout_end.php'; ?>
