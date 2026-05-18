<?php

require_once __DIR__ . '/../app/controllers/TaskController.php';

$controller = new TaskController();
$message = '';

if (isset($_GET['message'])) {
    $message = $_GET['message'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $message = $controller->delete((int)($_POST['delete_id'] ?? 0));

        header('Location: tasks_list.php?message=' . urlencode($message));
        exit;
    }

    if ($action === 'bulk') {
        $message = $controller->handleBulkAction($_POST);

        header('Location: tasks_list.php?message=' . urlencode($message));
        exit;
    }
}

$tasks = $controller->indexWithFilters($_GET);
$formData = $controller->showCreateForm();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список заданий</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }

        h1 {
            margin-bottom: 20px;
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

        .message {
            margin-bottom: 20px;
            padding: 10px;
            background: #f0f0f0;
            border: 1px solid #dddddd;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #cccccc;
            padding: 10px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f3f3f3;
        }

        .empty {
            padding: 20px;
            background: #f8f8f8;
            border: 1px solid #dddddd;
        }

        .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .delete-form {
            margin: 0;
        }

        .view-btn,
        .edit-btn,
        .delete-btn {
            display: inline-block;
            padding: 8px 12px;
            text-decoration: none;
            color: white;
            border: none;
            cursor: pointer;
        }

        .view-btn {
            background-color: #198754;
        }

        .view-btn:hover {
            background-color: #157347;
        }

        .edit-btn {
            background-color: #0d6efd;
        }

        .edit-btn:hover {
            background-color: #0b5ed7;
        }

        .delete-btn {
            background-color: #c82333;
        }

        .delete-btn:hover {
            background-color: #a71d2a;
        }

        mark {
            background-color: #fff3a3;
            padding: 0 2px;
        }
    </style>
</head>
<body>

    <h1>Список заданий</h1>

    <div class="top-links">
        <a href="create_task.php">Создать новое задание</a>
        <a href="task_sets_list.php">Список наборов заданий</a>
    </div>

    <?php if ($message !== ''): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form id="filterForm" method="GET" style="margin-bottom: 20px; padding: 15px; border: 1px solid #dddddd; background: #fafafa;">
        <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: end;">
            <div>
                <label for="search">Быстрый поиск по названию на странице</label><br>
                <input
                    type="text"
                    name="search"
                    id="search"
                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                    style="padding: 8px; min-width: 220px;"
                    placeholder="Начните вводить название..."
                >
                <small style="display:block; margin-top:5px; color:#666;">
                    Поиск сохраняется при смене фильтров на этой странице.
                </small>
            </div>

            <div>
                <label for="discipline_id">Дисциплина</label><br>
                <select name="discipline_id" id="discipline_id" style="padding: 8px; min-width: 200px;">
                    <option value="">Все дисциплины</option>
                    <?php foreach ($formData['disciplines'] as $discipline): ?>
                        <option
                            value="<?= $discipline['id'] ?>"
                            <?= (string)($_GET['discipline_id'] ?? '') === (string)$discipline['id'] ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($discipline['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="task_type_id">Тип задания</label><br>
                <select name="task_type_id" id="task_type_id" style="padding: 8px; min-width: 200px;">
                    <option value="">Все типы</option>
                    <?php foreach ($formData['taskTypes'] as $type): ?>
                        <option
                            value="<?= $type['id'] ?>"
                            <?= (string)($_GET['task_type_id'] ?? '') === (string)$type['id'] ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($type['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="folder_id">Папка</label><br>
                <select name="folder_id" id="folder_id" style="padding: 8px; min-width: 200px;">
                    <option value="">Все папки</option>
                    <?php foreach ($formData['folders'] as $folder): ?>
                        <option
                            value="<?= $folder['id'] ?>"
                            <?= (string)($_GET['folder_id'] ?? '') === (string)$folder['id'] ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($folder['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="author_id">Автор</label><br>
                <select name="author_id" id="author_id" style="padding: 8px; min-width: 220px;">
                    <option value="">Все авторы</option>
                    <?php foreach ($formData['authors'] as $author): ?>
                        <option
                            value="<?= $author['id'] ?>"
                            <?= (string)($_GET['author_id'] ?? '') === (string)$author['id'] ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($author['full_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="sort">Сортировка</label><br>
                <select name="sort" id="sort" style="padding: 8px; min-width: 220px;">
                    <option value="created_at_desc" <?= ($_GET['sort'] ?? 'created_at_desc') === 'created_at_desc' ? 'selected' : '' ?>>
                        Сначала новые
                    </option>
                    <option value="created_at_asc" <?= ($_GET['sort'] ?? '') === 'created_at_asc' ? 'selected' : '' ?>>
                        Сначала старые
                    </option>
                    <option value="title_asc" <?= ($_GET['sort'] ?? '') === 'title_asc' ? 'selected' : '' ?>>
                        Название А-Я
                    </option>
                    <option value="title_desc" <?= ($_GET['sort'] ?? '') === 'title_desc' ? 'selected' : '' ?>>
                        Название Я-А
                    </option>
                    <option value="author_asc" <?= ($_GET['sort'] ?? '') === 'author_asc' ? 'selected' : '' ?>>
                        Автор А-Я
                    </option>
                    <option value="author_desc" <?= ($_GET['sort'] ?? '') === 'author_desc' ? 'selected' : '' ?>>
                        Автор Я-А
                    </option>
                </select>
            </div>

            <div>
                <a
                    href="tasks_list.php"
                    id="resetFilters"
                    style="display: inline-block; margin-top: 24px;"
                >
                    Сбросить
                </a>
            </div>
        </div>
    </form>
    <form method="POST" id="bulkForm" style="margin-bottom: 20px; padding: 15px; border: 1px solid #dddddd; background: #fafafa;">
        <input type="hidden" name="action" value="bulk">

        <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: end;">
            <div>
                <label for="bulk_action">Массовое действие</label><br>
                <select name="bulk_action" id="bulk_action" style="padding: 8px; min-width: 220px;">
                    <option value="">Выберите действие</option>
                    <option value="delete">Удалить выбранные</option>
                    <option value="move_to_folder">Переместить в папку</option>
                </select>
            </div>

            <div>
                <label for="bulk_folder_id">Папка для перемещения</label><br>
                <select name="bulk_folder_id" id="bulk_folder_id" style="padding: 8px; min-width: 220px;">
                    <option value="">Без папки</option>
                    <?php foreach ($formData['folders'] as $folder): ?>
                        <option value="<?= $folder['id'] ?>">
                            <?= htmlspecialchars($folder['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <button type="submit" style="padding: 8px 14px;">Применить к выбранным</button>
            </div>
        </div>
    </form>
    <?php if (empty($tasks)): ?>
        <div class="empty">Задания пока не добавлены.</div>
    <?php else: ?>
        <table id="tasksTable">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" id="select_all">
                    </th>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Тип</th>
                    <th>Дисциплина</th>
                    <th>Папка</th>
                    <th>Автор</th>
                    <th>Сложность</th>
                    <th>Назначение</th>
                    <th>Дата создания</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task): ?>
                    <tr>
                        <td>
                            <input
                                type="checkbox"
                                name="selected_ids[]"
                                value="<?= (int)$task['id'] ?>"
                                form="bulkForm"
                                class="row-checkbox"
                            >
                        </td>
                        <td><?= htmlspecialchars($task['id']) ?></td>
                        <td><?= htmlspecialchars($task['title']) ?></td>
                        <td><?= htmlspecialchars($task['task_type_name']) ?></td>
                        <td><?= htmlspecialchars($task['discipline_name']) ?></td>
                        <td><?= htmlspecialchars($task['folder_name'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($task['author_name']) ?></td>
                        <td><?= htmlspecialchars($task['difficulty'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($task['purpose'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($task['created_at']) ?></td>
                        <td>
                            <div class="actions">
                                <a href="view_task.php?id=<?= (int)$task['id'] ?>" class="view-btn">
                                    Просмотр
                                </a>

                                <a href="edit_task.php?id=<?= (int)$task['id'] ?>" class="edit-btn">
                                    Редактировать
                                </a>

                                <form method="POST" class="delete-form" onsubmit="return confirm('Удалить это задание?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="delete_id" value="<?= (int)$task['id'] ?>">
                                    <button type="submit" class="delete-btn">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div id="noResultsMessage" class="empty" style="display: none; margin-top: 15px;">
            По вашему запросу ничего не найдено.
        </div>
    <?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search');
    const table = document.getElementById('tasksTable');

    if (!searchInput || !table) {
        return;
    }

    const rows = table.querySelectorAll('tbody tr');
    const noResultsMessage = document.getElementById('noResultsMessage');
    const storageKey = 'tasksLiveSearch';
    const resetLink = document.getElementById('resetFilters');

    rows.forEach(function (row) {
        const titleCell = row.cells[2];

        if (titleCell) {
            titleCell.dataset.originalText = titleCell.textContent;
        }
    });

    function escapeRegExp(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    function highlightText(text, query) {
        if (!query) {
            return text;
        }

        const escapedQuery = escapeRegExp(query);
        const regex = new RegExp('(' + escapedQuery + ')', 'gi');

        return text.replace(regex, '<mark>$1</mark>');
    }

    function applyLiveSearch(query) {
        const normalizedQuery = query.trim().toLowerCase();
        let visibleCount = 0;

        rows.forEach(function (row) {
            const titleCell = row.cells[2];

            if (!titleCell) {
                return;
            }

            const originalText = titleCell.dataset.originalText || titleCell.textContent;
            const lowerText = originalText.toLowerCase();

            if (lowerText.includes(normalizedQuery)) {
                row.style.display = '';
                titleCell.innerHTML = highlightText(originalText, normalizedQuery);
                visibleCount++;
            } else {
                row.style.display = 'none';
                titleCell.innerHTML = originalText;
            }
        });

        if (noResultsMessage) {
            noResultsMessage.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    if (resetLink) {
        resetLink.addEventListener('click', function () {
            sessionStorage.removeItem(storageKey);
        });
    }
    const savedValue = sessionStorage.getItem(storageKey);

    if (savedValue !== null) {
        searchInput.value = savedValue;
        applyLiveSearch(savedValue);
    }

    searchInput.addEventListener('input', function () {
        const value = this.value;
        sessionStorage.setItem(storageKey, value);
        applyLiveSearch(value);
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filterForm');
    const disciplineSelect = document.getElementById('discipline_id');
    const taskTypeSelect = document.getElementById('task_type_id');
    const folderSelect = document.getElementById('folder_id');
    const authorSelect = document.getElementById('author_id');
    const sortSelect = document.getElementById('sort');

    if (!form) {
        return;
    }

    if (disciplineSelect) {
        disciplineSelect.addEventListener('change', function () {
            form.submit();
        });
    }

    if (taskTypeSelect) {
        taskTypeSelect.addEventListener('change', function () {
            form.submit();
        });
    }

    if (folderSelect) {
        folderSelect.addEventListener('change', function () {
            form.submit();
        });
    }

    if (authorSelect) {
        authorSelect.addEventListener('change', function () {
            form.submit();
        });
    }

    if (sortSelect) {
        sortSelect.addEventListener('change', function () {
            form.submit();
        });
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('select_all');
    const checkboxes = document.querySelectorAll('.row-checkbox');

    if (!selectAll) {
        return;
    }

    selectAll.addEventListener('change', function () {
        checkboxes.forEach(function (checkbox) {
            checkbox.checked = selectAll.checked;
        });
    });
});
</script>

</body>
</html>