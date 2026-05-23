<?php

require_once __DIR__ . '/../app/auth/Auth.php';
Auth::requireAuth();
Auth::requireRole(['teacher']);

require_once __DIR__ . '/../app/controllers/TaskController.php';

$controller = new TaskController();
$formData = $controller->showCreateForm();

$message = '';
$showCopySection = false;
$selectedCopyTaskId = '';

$old = [
    'title' => '',
    'task_text' => '',
    'difficulty' => '',
    'purpose' => '',
    'reference_answer' => '',
    'task_type_id' => '',
    'discipline_id' => '',
    'folder_id' => '',
    'options' => [
        ['option_text' => '', 'is_correct' => false],
        ['option_text' => '', 'is_correct' => false],
    ],
];

function buildOldOptionsFromPost(array $postData): array
{
    $optionTexts = $postData['option_texts'] ?? [];
    $correctSingle = $postData['correct_option_single'] ?? '';
    $correctMultiple = $postData['correct_options'] ?? [];
    $correctMultiple = is_array($correctMultiple) ? array_map('strval', $correctMultiple) : [];

    if (!is_array($optionTexts)) {
        $optionTexts = [];
    }

    $options = [];

    foreach ($optionTexts as $index => $text) {
        $options[] = [
            'option_text' => (string)$text,
            'is_correct' => (string)$correctSingle === (string)$index || in_array((string)$index, $correctMultiple, true),
        ];
    }

    while (count($options) < 2) {
        $options[] = ['option_text' => '', 'is_correct' => false];
    }

    return $options;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formAction = $_POST['form_action'] ?? '';

    if ($formAction === 'show_copy_section') {
        $showCopySection = true;
    }

    if ($formAction === 'load_copy_task') {
        $showCopySection = true;
        $selectedCopyTaskId = $_POST['copy_task_id'] ?? '';

        $sourceTask = $controller->getTaskDataForCopy((int)$selectedCopyTaskId);

        if ($sourceTask) {
            $old = [
                'title' => $sourceTask['title'] ?? '',
                'task_text' => $sourceTask['task_text'] ?? '',
                'difficulty' => $sourceTask['difficulty'] ?? '',
                'purpose' => $sourceTask['purpose'] ?? '',
                'reference_answer' => $sourceTask['reference_answer'] ?? '',
                'task_type_id' => $sourceTask['task_type_id'] ?? '',
                'discipline_id' => $sourceTask['discipline_id'] ?? '',
                'folder_id' => $sourceTask['folder_id'] ?? '',
                'options' => !empty($sourceTask['options'])
                    ? $sourceTask['options']
                    : [
                        ['option_text' => '', 'is_correct' => false],
                        ['option_text' => '', 'is_correct' => false],
                    ],
            ];

            $message = 'Данные выбранного задания подставлены в форму. При необходимости измените их и сохраните новое задание.';
        } else {
            $message = 'Не удалось загрузить выбранное задание.';
        }
    }

    if ($formAction === 'save_task') {
        $old = [
            'title' => $_POST['title'] ?? '',
            'task_text' => $_POST['task_text'] ?? '',
            'difficulty' => $_POST['difficulty'] ?? '',
            'purpose' => $_POST['purpose'] ?? '',
            'reference_answer' => $_POST['reference_answer'] ?? '',
            'task_type_id' => $_POST['task_type_id'] ?? '',
            'discipline_id' => $_POST['discipline_id'] ?? '',
            'folder_id' => $_POST['folder_id'] ?? '',
            'options' => buildOldOptionsFromPost($_POST),
        ];

        $showCopySection = isset($_POST['show_copy_section_state']) && $_POST['show_copy_section_state'] === '1';
        $selectedCopyTaskId = $_POST['copy_task_id_state'] ?? '';

        $message = $controller->store($_POST);
        $formData = $controller->showCreateForm();

        if ($message === 'Задание успешно сохранено.') {
            $old = [
                'title' => '',
                'task_text' => '',
                'difficulty' => '',
                'purpose' => '',
                'reference_answer' => '',
                'task_type_id' => '',
                'discipline_id' => '',
                'folder_id' => '',
                'options' => [
                    ['option_text' => '', 'is_correct' => false],
                    ['option_text' => '', 'is_correct' => false],
                ],
            ];

            $showCopySection = false;
            $selectedCopyTaskId = '';
        }
    }
}

$pageTitle = 'Создание задания';
$activePage = 'tasks';
require __DIR__ . '/includes/layout_start.php';
?>

    <div class="top-links">
        <a href="tasks_list.php">Назад к списку заданий</a>
        <a href="task_sets_list.php">Наборы заданий</a>
    </div>

    <div class="page-header">
        <div>
            <h1 class="page-title">Создание задания</h1>
            <p class="page-subtitle">Заполните основные сведения, классификацию и методические данные.</p>
        </div>
    </div>

    <form method="POST" class="panel task-form">
        <input type="hidden" name="form_action" value="show_copy_section">
        <button type="submit">Создать на основе существующего</button>
    </form>

    <?php if ($message !== ''): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($showCopySection): ?>
        <div class="copy-box">
            <h2>Создание на основе существующего задания</h2>

            <form method="POST">
                <input type="hidden" name="form_action" value="load_copy_task">

                <label for="copy_task_id">Выберите существующее задание</label>
                <select name="copy_task_id" id="copy_task_id" required>
                    <option value="">Выберите задание</option>
                    <?php foreach ($formData['taskListForCopy'] as $copyTask): ?>
                        <option
                            value="<?= $copyTask['id'] ?>"
                            <?= (string)$selectedCopyTaskId === (string)$copyTask['id'] ? 'selected' : '' ?>
                        >
                            #<?= $copyTask['id'] ?> — <?= htmlspecialchars($copyTask['title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit">Подставить данные</button>
            </form>
        </div>
    <?php endif; ?>

    <form method="POST" class="task-form">
        <input type="hidden" name="form_action" value="save_task">
        <input type="hidden" name="show_copy_section_state" value="<?= $showCopySection ? '1' : '0' ?>">
        <input type="hidden" name="copy_task_id_state" value="<?= htmlspecialchars($selectedCopyTaskId) ?>">

        <div class="form-section">
            <h2>Основные сведения</h2>

            <label for="title">Название задания *</label>
            <input
                type="text"
                name="title"
                id="title"
                required
                value="<?= htmlspecialchars($old['title']) ?>"
            >
            <small class="field-hint">Используйте короткое и понятное название, чтобы задание было легко найти в списке.</small>

            <label for="task_text">Текст задания *</label>
            <textarea
                name="task_text"
                id="task_text"
                rows="6"
                required
            ><?= htmlspecialchars($old['task_text']) ?></textarea>
            <small class="field-hint">Опишите само задание так, как его будет видеть обучающийся.</small>
        </div>

        <div class="form-section">
            <h2>Классификация задания</h2>

            <label for="task_type_id">Тип задания *</label>
            <select name="task_type_id" id="task_type_id" required>
                <option value="">Выберите тип задания</option>
            <?php foreach ($formData['taskTypes'] as $type): ?>
                    <option
                        value="<?= $type['id'] ?>"
                        data-type-name="<?= htmlspecialchars(mb_strtolower($type['name'])) ?>"
                        <?= (string)$old['task_type_id'] === (string)$type['id'] ? 'selected' : '' ?>
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
                        value="<?= $folder['id'] ?>"
                        <?= (string)$old['folder_id'] === (string)$folder['id'] ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($folder['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small class="field-hint">Папка помогает логически группировать задания внутри системы.</small>
        </div>

        <div class="form-section">
            <h2>Методические сведения</h2>

            <label for="difficulty">Сложность</label>
            <input
                type="text"
                name="difficulty"
                id="difficulty"
                value="<?= htmlspecialchars($old['difficulty']) ?>"
            >
            <small class="field-hint">Например: низкая, средняя, высокая.</small>

            <label for="purpose">Назначение</label>
            <input
                type="text"
                name="purpose"
                id="purpose"
                placeholder="Например: итоговая контрольная по САПР"
                value="<?= htmlspecialchars($old['purpose']) ?>"
            >
            <small class="field-hint">Укажите, в каком контексте используется задание: тренировка, контрольная работа, диагностика и т.д.</small>

            <label for="reference_answer">Эталонный ответ</label>
            <textarea
                name="reference_answer"
                id="reference_answer"
                rows="4"
            ><?= htmlspecialchars($old['reference_answer']) ?></textarea>
            <small class="field-hint">Это поле используется преподавателем как ориентир при проверке ответа.</small>
        </div>

        <div class="form-section" id="optionsSection" style="display: none;">
            <h2>Варианты ответа</h2>
            <small class="field-hint">
                Для типа "Один вариант" отметьте один правильный ответ. Для типа "Несколько вариантов" можно отметить несколько.
            </small>

            <div id="optionsContainer" data-next-index="<?= count($old['options']) ?>">
                <?php foreach ($old['options'] as $index => $option): ?>
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

        <div class="form-actions">
            <button type="submit">Сохранить задание</button>
            <a href="tasks_list.php">Отмена</a>
        </div>
    </form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeSelect = document.getElementById('task_type_id');
    const optionsSection = document.getElementById('optionsSection');
    const optionsContainer = document.getElementById('optionsContainer');
    const addOptionButton = document.getElementById('addOptionButton');
    const referenceAnswer = document.getElementById('reference_answer');
    const referenceAnswerLabel = document.querySelector('label[for="reference_answer"]');
    const referenceAnswerHint = referenceAnswer ? referenceAnswer.nextElementSibling : null;

    function getSelectedTypeName() {
        const selectedOption = typeSelect.options[typeSelect.selectedIndex];
        return selectedOption ? selectedOption.dataset.typeName || '' : '';
    }

    function updateOptionsMode() {
        const typeName = getSelectedTypeName();
        const selectedTypeId = typeSelect.value;
        const isOpen = selectedTypeId === '1';
        const isSingle = typeName.includes('один вариант');
        const isMultiple = typeName.includes('несколько вариантов');

        const singleMode = selectedTypeId === '2' || isSingle;
        const multipleMode = selectedTypeId === '3' || isMultiple;

        [referenceAnswerLabel, referenceAnswer, referenceAnswerHint].forEach(function (element) {
            if (!element) {
                return;
            }

            element.style.display = isOpen ? '' : 'none';
        });

        if (referenceAnswer) {
            referenceAnswer.disabled = !isOpen;
        }

        optionsSection.style.display = singleMode || multipleMode ? 'block' : 'none';

        optionsContainer.querySelectorAll('.correct-single').forEach(function (input) {
            input.style.display = singleMode ? 'inline-block' : 'none';
            input.disabled = !singleMode;
        });

        optionsContainer.querySelectorAll('.correct-multiple').forEach(function (input) {
            input.style.display = multipleMode ? 'inline-block' : 'none';
            input.disabled = !multipleMode;
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
