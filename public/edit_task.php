<?php

require_once __DIR__ . '/../app/auth/Auth.php';
Auth::requireAuth();

require_once __DIR__ . '/../app/controllers/TaskController.php';

$controller = new TaskController();
$message = '';

if (isset($_GET['message'])) {
    $message = $_GET['message'];
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die('Некорректный идентификатор задания.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $controller->update($id, $_POST);

    header('Location: edit_task.php?id=' . $id . '&message=' . urlencode($message));
    exit;
}

$formData = $controller->getEditFormData($id);
$task = $formData['task'];
$options = $formData['options'];

if (!$task) {
    die('Задание не найдено.');
}

if (empty($options)) {
    $options = [
        ['option_text' => '', 'is_correct' => false],
        ['option_text' => '', 'is_correct' => false],
    ];
}

$pageTitle = 'Редактирование задания';
$activePage = 'tasks';
require __DIR__ . '/includes/layout_start.php';
?>

    <div class="top-links">
        <a href="tasks_list.php">Назад к списку заданий</a>
        <a href="view_task.php?id=<?= (int)$task['id'] ?>">Просмотр задания</a>
        <a href="task_sets_list.php">Наборы заданий</a>
    </div>

    <div class="page-header">
        <div>
            <h1 class="page-title">Редактирование задания</h1>
            <p class="page-subtitle">Изменение содержимого, классификации и методических сведений.</p>
        </div>
    </div>

    <?php if ($message !== ''): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" class="task-form">
        <div class="form-section">
            <h2>Основные сведения</h2>
        <label for="title">Название задания *</label>
        <input
            type="text"
            name="title"
            id="title"
            required
            value="<?= htmlspecialchars($task['title']) ?>"
        >
        <small class="field-hint">Используйте короткое и понятное название, чтобы задание было легко найти в списке.</small>

        <label for="task_text">Текст задания *</label>
        <textarea
            name="task_text"
            id="task_text"
            rows="6"
            required
        ><?= htmlspecialchars($task['task_text']) ?></textarea>
        <small class="field-hint">Опишите задание так, как его должен видеть обучающийся.</small>

        </div>

        <div class="form-section">
            <h2>Классификация задания</h2>

        <label for="difficulty">Сложность</label>
        <input
            type="text"
            name="difficulty"
            id="difficulty"
            value="<?= htmlspecialchars($task['difficulty'] ?? '') ?>"
        >
        <small class="field-hint">Например: низкая, средняя, высокая.</small>

        <label for="purpose">Назначение</label>
        <input
            type="text"
            name="purpose"
            id="purpose"
            value="<?= htmlspecialchars($task['purpose'] ?? '') ?>"
        >
        <small class="field-hint">Укажите, где используется задание: тренировка, контрольная, диагностика и т.д.</small>

        <label for="reference_answer">Эталонный ответ</label>
        <textarea
            name="reference_answer"
            id="reference_answer"
            rows="4"
        ><?= htmlspecialchars($task['reference_answer'] ?? '') ?></textarea>
        <small class="field-hint">Эталонный ответ используется преподавателем как ориентир при проверке.</small>

        <label for="task_type_id">Тип задания *</label>
        <select name="task_type_id" id="task_type_id" required>
            <option value="">Выберите тип задания</option>
            <?php foreach ($formData['taskTypes'] as $type): ?>
                <option
                    value="<?= $type['id'] ?>"
                    data-type-name="<?= htmlspecialchars(mb_strtolower($type['name'])) ?>"
                    <?= (int)$task['task_type_id'] === (int)$type['id'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($type['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="discipline_id">Дисциплина *</label>
        <select name="discipline_id" id="discipline_id" required>
            <option value="">Выберите дисциплину</option>
            <?php foreach ($formData['disciplines'] as $discipline): ?>
                <option
                    value="<?= $discipline['id'] ?>"
                    <?= (int)$task['discipline_id'] === (int)$discipline['id'] ? 'selected' : '' ?>
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
                    value="<?= $folder['id'] ?>"
                    <?= (int)($task['folder_id'] ?? 0) === (int)$folder['id'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($folder['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <small class="field-hint">Папка помогает логически группировать задания внутри системы.</small>

        </div>

        <div class="form-section" id="optionsSection" style="display: none;">
            <h2>Варианты ответа</h2>
            <small class="field-hint">
                Для типа "Один вариант" отметьте один правильный ответ. Для типа "Несколько вариантов" можно отметить несколько.
            </small>

            <div id="optionsContainer" data-next-index="<?= count($options) ?>">
                <?php foreach ($options as $index => $option): ?>
                    <div class="option-row">
                        <input
                            type="text"
                            name="option_texts[<?= (int)$index ?>]"
                            value="<?= htmlspecialchars($option['option_text'] ?? '') ?>"
                            placeholder="Текст варианта ответа"
                        >

                        <label class="option-correct">
                            <input
                                type="radio"
                                name="correct_option_single"
                                value="<?= (int)$index ?>"
                                class="correct-single"
                                <?= !empty($option['is_correct']) ? 'checked' : '' ?>
                            >
                            <input
                                type="checkbox"
                                name="correct_options[]"
                                value="<?= (int)$index ?>"
                                class="correct-multiple"
                                <?= !empty($option['is_correct']) ? 'checked' : '' ?>
                            >
                            Правильный
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="button" id="addOptionButton">Добавить ещё вариант</button>
        </div>

        <div class="form-section">
            <h2>Методические сведения</h2>

        </div>

        <div class="form-actions">
            <button type="submit">Сохранить изменения</button>
            <a href="view_task.php?id=<?= (int)$task['id'] ?>">Отмена</a>
        </div>
    </form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeSelect = document.getElementById('task_type_id');
    const optionsSection = document.getElementById('optionsSection');
    const optionsContainer = document.getElementById('optionsContainer');
    const addOptionButton = document.getElementById('addOptionButton');

    function getSelectedTypeName() {
        const selectedOption = typeSelect.options[typeSelect.selectedIndex];
        return selectedOption ? selectedOption.dataset.typeName || '' : '';
    }

    function updateOptionsMode() {
        const typeName = getSelectedTypeName();
        const isSingle = typeName.includes('один вариант');
        const isMultiple = typeName.includes('несколько вариантов');

        optionsSection.style.display = isSingle || isMultiple ? 'block' : 'none';

        optionsContainer.querySelectorAll('.correct-single').forEach(function (input) {
            input.style.display = isSingle ? 'inline-block' : 'none';
            input.disabled = !isSingle;
        });

        optionsContainer.querySelectorAll('.correct-multiple').forEach(function (input) {
            input.style.display = isMultiple ? 'inline-block' : 'none';
            input.disabled = !isMultiple;
        });
    }

    function addOptionRow() {
        const index = parseInt(optionsContainer.dataset.nextIndex, 10);
        optionsContainer.dataset.nextIndex = String(index + 1);

        const row = document.createElement('div');
        row.className = 'option-row';
        row.innerHTML = `
            <input type="text" name="option_texts[${index}]" placeholder="Текст варианта ответа">
            <label class="option-correct">
                <input type="radio" name="correct_option_single" value="${index}" class="correct-single">
                <input type="checkbox" name="correct_options[]" value="${index}" class="correct-multiple">
                Правильный
            </label>
        `;

        optionsContainer.appendChild(row);
        updateOptionsMode();
    }

    if (typeSelect && optionsSection && optionsContainer && addOptionButton) {
        typeSelect.addEventListener('change', updateOptionsMode);
        addOptionButton.addEventListener('click', addOptionRow);
        updateOptionsMode();
    }
});
</script>

<?php require __DIR__ . '/includes/layout_end.php'; ?>
