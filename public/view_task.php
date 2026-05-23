<?php

require_once __DIR__ . '/../app/auth/Auth.php';
Auth::requireAuth();

require_once __DIR__ . '/../app/controllers/TaskController.php';

$controller = new TaskController();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$task = $controller->show($id);

if (!$task) {
    die('Задание не найдено.');
}

$pageTitle = 'Просмотр задания';
$activePage = 'tasks';
require __DIR__ . '/includes/layout_start.php';
?>

    <div class="top-links">
        <a href="tasks_list.php">Назад к списку заданий</a>
        <a href="edit_task.php?id=<?= (int)$task['id'] ?>">Редактировать</a>
        <a href="task_sets_list.php">Наборы заданий</a>
    </div>

    <div class="page-header">
        <div>
            <h1 class="page-title">Просмотр задания</h1>
            <p class="page-subtitle">Основная информация, содержимое и методические сведения.</p>
        </div>
    </div>

    <div class="card">
        <h2>Основная информация</h2>

        <div class="task-detail-row">
            <span class="task-detail-label">ID</span>
            <div class="task-detail-value"><?= htmlspecialchars($task['id']) ?></div>
        </div>

        <div class="task-detail-row">
            <span class="task-detail-label">Название</span>
            <div class="task-detail-value"><?= htmlspecialchars($task['title']) ?></div>
        </div>

        <div class="task-detail-row">
            <span class="task-detail-label">Тип задания</span>
            <div class="task-detail-value"><?= htmlspecialchars($task['task_type_name']) ?></div>
        </div>

        <div class="task-detail-row">
            <span class="task-detail-label">Дисциплина</span>
            <div class="task-detail-value"><?= htmlspecialchars($task['discipline_name']) ?></div>
        </div>

        <div class="task-detail-row">
            <span class="task-detail-label">Папка</span>
            <div class="task-detail-value"><?= htmlspecialchars($task['folder_name'] ?? '—') ?></div>
        </div>

        <div class="task-detail-row">
            <span class="task-detail-label">Автор</span>
            <div class="task-detail-value"><?= htmlspecialchars($task['author_name']) ?></div>
        </div>

        <div class="task-detail-row">
            <span class="task-detail-label">Дата создания</span>
            <div class="task-detail-value"><?= htmlspecialchars($task['created_at']) ?></div>
        </div>
    </div>

    <div class="card">
        <h2>Содержимое задания</h2>

        <div class="task-detail-row">
            <span class="task-detail-label">Текст задания</span>
            <div class="task-detail-value"><?= htmlspecialchars($task['task_text']) ?></div>
        </div>

        <?php if (!empty($task['options'])): ?>
            <div class="task-detail-row">
                <span class="task-detail-label">Варианты ответа</span>
                <ol class="option-list">
                    <?php foreach ($task['options'] as $option): ?>
                        <li class="<?= !empty($option['is_correct']) ? 'correct-option' : '' ?>">
                            <?= htmlspecialchars($option['option_text']) ?>
                            <?= !empty($option['is_correct']) ? ' — правильный' : '' ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2>Методические сведения</h2>

        <div class="task-detail-row">
            <span class="task-detail-label">Сложность</span>
            <div class="task-detail-value"><?= htmlspecialchars($task['difficulty'] ?? '—') ?></div>
        </div>

        <div class="task-detail-row">
            <span class="task-detail-label">Назначение</span>
            <div class="task-detail-value"><?= htmlspecialchars($task['purpose'] ?? '—') ?></div>
        </div>

        <div class="task-detail-row">
            <span class="task-detail-label">Эталонный ответ</span>
            <div class="task-detail-value"><?= htmlspecialchars($task['reference_answer'] ?? '—') ?></div>
        </div>
    </div>

<?php require __DIR__ . '/includes/layout_end.php'; ?>
