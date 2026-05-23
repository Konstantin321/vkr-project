<?php

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

if (!$task) {
    die('Задание не найдено.');
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование задания</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
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
        }
        .top-links {
            margin-bottom: 20px;
        }
        .top-links a {
            margin-right: 15px;
            text-decoration: none;
            color: #0a58ca;
        }
    </style>
</head>
<body>

    <div class="top-links">
        <a href="tasks_list.php">← Назад к списку заданий</a>
        <a href="view_task.php?id=<?= (int)$task['id'] ?>">Просмотр задания</a>
        <a href="task_sets_list.php">Наборы заданий</a>
    </div>

    <h1>Редактирование задания</h1>

    <?php if ($message !== ''): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
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

        <div class="form-section">
            <h2>Методические сведения</h2>

        </div>

        <div class="form-actions">
            <button type="submit">Сохранить изменения</button>
            <a href="view_task.php?id=<?= (int)$task['id'] ?>" style="margin-left: 15px;">Отмена</a>
        </div>
    </form>

</body>
</html>