<?php

require_once __DIR__ . '/../app/auth/Auth.php';
Auth::requireAuth();
Auth::requireRole(['teacher']);

require_once __DIR__ . '/../app/services/TaskImportService.php';

$service = new TaskImportService();
$formData = $service->getFormData();
$result = null;
$old = [
    'discipline_id' => '',
    'folder_id' => '',
    'difficulty' => '',
    'purpose' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = [
        'discipline_id' => $_POST['discipline_id'] ?? '',
        'folder_id' => $_POST['folder_id'] ?? '',
        'difficulty' => $_POST['difficulty'] ?? '',
        'purpose' => $_POST['purpose'] ?? '',
    ];

    $result = $service->importSingleChoiceText($_FILES['tasks_file'] ?? [], $_POST, Auth::id());
}

$pageTitle = 'Импорт заданий';
$activePage = 'tasks';
require __DIR__ . '/includes/layout_start.php';
?>

    <div class="top-links">
        <a href="tasks_list.php">Назад к списку заданий</a>
        <a href="create_task.php">Создать задание вручную</a>
    </div>

    <div class="page-header">
        <div>
            <h1 class="page-title">Импорт заданий</h1>
            <p class="page-subtitle">Загрузка заданий с одним правильным вариантом ответа из txt-файла.</p>
        </div>
    </div>

    <?php if ($result !== null): ?>
        <?php if ($result['success']): ?>
            <div class="message">
                Импорт завершён. Добавлено заданий: <?= (int)$result['imported_count'] ?>.
            </div>
        <?php else: ?>
            <div class="message message--danger">
                Импорт не выполнен. Исправьте файл и повторите загрузку.
            </div>
        <?php endif; ?>

        <?php if (!empty($result['errors'])): ?>
            <div class="card import-report">
                <h2>Ошибки импорта</h2>
                <ul class="import-report__list">
                    <?php foreach ($result['errors'] as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="task-form">
        <div class="form-section">
            <h2>Файл и параметры</h2>

            <label for="tasks_file">Файл с заданиями *</label>
            <input
                type="file"
                name="tasks_file"
                id="tasks_file"
                accept=".txt,text/plain"
                required
            >
            <small class="field-hint">Поддерживается txt-файл до 1 МБ. Один блок задания отделяется от другого пустой строкой.</small>

            <label for="discipline_id">Дисциплина *</label>
            <select name="discipline_id" id="discipline_id" required>
                <option value="">Выберите дисциплину</option>
                <?php foreach ($formData['disciplines'] as $discipline): ?>
                    <option
                        value="<?= (int)$discipline['id'] ?>"
                        <?= (string)$old['discipline_id'] === (string)$discipline['id'] ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($discipline['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="folder_id">Папка</label>
            <select name="folder_id" id="folder_id">
                <option value="">Без папки</option>
                <?php foreach ($formData['folders'] as $folder): ?>
                    <option
                        value="<?= (int)$folder['id'] ?>"
                        <?= (string)$old['folder_id'] === (string)$folder['id'] ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($folder['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="difficulty">Сложность</label>
            <input
                type="text"
                name="difficulty"
                id="difficulty"
                value="<?= htmlspecialchars($old['difficulty']) ?>"
                placeholder="Например: средняя"
            >

            <label for="purpose">Назначение</label>
            <input
                type="text"
                name="purpose"
                id="purpose"
                value="<?= htmlspecialchars($old['purpose']) ?>"
                placeholder="Например: импорт из банка заданий"
            >
        </div>

        <div class="form-section">
            <h2>Шаблон txt</h2>

            <div class="import-template">
                <div>Вопрос 1 Текст задания</div>
                <div>Первый вариант ответа</div>
                <div>* Правильный вариант ответа</div>
                <div>Третий вариант ответа</div>
            </div>

            <small class="field-hint">
                Правильный ответ должен начинаться со звёздочки. Для каждого задания нужен ровно один правильный вариант.
            </small>
        </div>

        <div class="form-actions">
            <button type="submit">Импортировать задания</button>
            <a href="tasks_list.php">Отмена</a>
        </div>
    </form>

<?php require __DIR__ . '/includes/layout_end.php'; ?>
