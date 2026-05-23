<?php

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
];

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
            ];

            $showCopySection = false;
            $selectedCopyTaskId = '';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Создание задания</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }

        .top-links {
            margin-bottom: 20px;
        }

        .top-links a {
            display: inline-block;
            margin-right: 15px;
            text-decoration: none;
            color: #0a58ca;
        }

        .form-section {
            max-width: 700px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid #dddddd;
            background: #fafafa;
        }

        .form-section h2 {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 22px;
        }

        .field-hint {
            display: block;
            margin-top: 5px;
            color: #666666;
            font-size: 13px;
        }

        .form-actions {
            max-width: 700px;
            margin-top: 20px;
        }

        .copy-box {
            max-width: 700px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid #dddddd;
            background: #f5f9ff;
        }

        .copy-box h2 {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 22px;
        }

        form {
            max-width: 700px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, textarea, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            box-sizing: border-box;
        }

        button {
            margin-top: 20px;
            padding: 10px 20px;
        }

        .message {
            margin-bottom: 20px;
            padding: 10px;
            background: #f0f0f0;
            border: 1px solid #dddddd;
            max-width: 700px;
        }
    </style>
</head>
<body>

    <div class="top-links">
        <a href="tasks_list.php">← Назад к списку заданий</a>
        <a href="task_sets_list.php">Наборы заданий</a>
    </div>

    <h1>Создание задания</h1>

    <form method="POST" style="margin-bottom: 20px;">
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

                <button type="submit" style="margin-top: 15px;">Подставить данные</button>
            </form>
        </div>
    <?php endif; ?>

    <form method="POST">
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

        <div class="form-actions">
            <button type="submit">Сохранить задание</button>
            <a href="tasks_list.php" style="margin-left: 15px;">Отмена</a>
        </div>
    </form>

</body>
</html>