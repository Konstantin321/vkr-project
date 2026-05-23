<?php

require_once __DIR__ . '/../app/auth/Auth.php';
Auth::requireAuth();

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

$pageTitle = 'Список заданий';
$activePage = 'tasks';
require __DIR__ . '/includes/layout_start.php';
?>

    <div class="page-header">
        <div>
            <h1 class="page-title">Список заданий</h1>
            <p class="page-subtitle">Управление заданиями, фильтрами и массовыми действиями.</p>
        </div>
        <a href="create_task.php" class="btn btn-primary">Создать задание</a>
    </div>

    <div class="top-links">
        <a href="task_sets_list.php">Список наборов заданий</a>
    </div>

    <?php if ($message !== ''): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form id="filterForm" method="GET" class="panel filter-panel">
        <div class="toolbar-grid">
            <div class="field">
                <label for="search">Быстрый поиск по названию на странице</label>
                <input
                    type="text"
                    name="search"
                    id="search"
                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                    placeholder="Начните вводить название..."
                >
                <small class="field-hint">
                    Поиск сохраняется при смене фильтров на этой странице.
                </small>
            </div>

            <div class="field">
                <label for="discipline_id">Дисциплина</label>
                <select name="discipline_id" id="discipline_id">
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

            <div class="field">
                <label for="task_type_id">Тип задания</label>
                <select name="task_type_id" id="task_type_id">
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

            <div class="field">
                <label for="folder_id">Папка</label>
                <select name="folder_id" id="folder_id">
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

            <div class="field">
                <label for="author_id">Автор</label>
                <select name="author_id" id="author_id">
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

            <div class="field">
                <label for="sort">Сортировка</label>
                <select name="sort" id="sort">
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

            <div class="field">
                <a
                    href="tasks_list.php"
                    id="resetFilters"
                    class="btn-secondary"
                >
                    Сбросить
                </a>
            </div>
        </div>
    </form>
    <form method="POST" id="bulkForm" class="panel bulk-panel">
        <input type="hidden" name="action" value="bulk">

        <div class="toolbar-grid">
            <div class="field">
                <label for="bulk_action">Массовое действие</label>
                <select name="bulk_action" id="bulk_action">
                    <option value="">Выберите действие</option>
                    <option value="delete">Удалить выбранные</option>
                    <option value="move_to_folder">Переместить в папку</option>
                </select>
            </div>

            <div class="field">
                <label for="bulk_folder_id">Папка для перемещения</label>
                <select name="bulk_folder_id" id="bulk_folder_id">
                    <option value="">Без папки</option>
                    <?php foreach ($formData['folders'] as $folder): ?>
                        <option value="<?= $folder['id'] ?>">
                            <?= htmlspecialchars($folder['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field">
                <button type="submit">Применить к выбранным</button>
            </div>
        </div>
    </form>
    <?php if (empty($tasks)): ?>
        <div class="empty">Задания пока не добавлены.</div>
    <?php else: ?>
        <div class="card table-card">
            <div class="table-responsive">
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
            </div>
        </div>

        <div id="noResultsMessage" class="empty mt-15" style="display: none;">
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

<?php require __DIR__ . '/includes/layout_end.php'; ?>
