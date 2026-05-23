<?php

require_once __DIR__ . '/../app/auth/Auth.php';
Auth::requireAuth();
Auth::requireRole(['teacher']);

require_once __DIR__ . '/../app/controllers/TaskSetController.php';

$controller = new TaskSetController();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'remove') {
        $message = $controller->removeTaskFromSet($_POST);
    } elseif ($action === 'update_item') {
        $message = $controller->updateSetItem($_POST);
    } elseif ($action === 'bulk_remove') {
        $message = $controller->removeMultipleTasksFromSet($_POST);
    }
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$data = $controller->show($id);

if (!$data) {
    die('Набор заданий не найден.');
}

$taskSet = $data['taskSet'];
$items = $data['items'];

$pageTitle = 'Просмотр набора заданий';
$activePage = 'sets';
require __DIR__ . '/includes/layout_start.php';
?>

    <div class="top-links">
        <a href="task_sets_list.php">Назад к списку наборов</a>
        <a href="edit_task_set.php?id=<?= (int)$taskSet['id'] ?>">Редактировать набор</a>
        <a href="add_task_to_set.php">Добавить задания в набор</a>
    </div>

    <div class="page-header">
        <div>
            <h1 class="page-title">Просмотр набора заданий</h1>
            <p class="page-subtitle">Сведения о наборе, порядок заданий и баллы.</p>
        </div>
    </div>

    <?php if (!empty($message)): ?>
        <div class="message">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <h2>Основная информация</h2>

        <div class="task-detail-row">
            <span class="task-detail-label">ID</span>
            <div class="task-detail-value"><?= htmlspecialchars($taskSet['id']) ?></div>
        </div>

        <div class="task-detail-row">
            <span class="task-detail-label">Название</span>
            <div class="task-detail-value"><?= htmlspecialchars($taskSet['name']) ?></div>
        </div>

        <div class="task-detail-row">
            <span class="task-detail-label">Описание</span>
            <div class="task-detail-value"><?= htmlspecialchars($taskSet['description'] ?? '—') ?></div>
        </div>

        <div class="task-detail-row">
            <span class="task-detail-label">Время выполнения (минуты)</span>
            <div class="task-detail-value"><?= htmlspecialchars($taskSet['execution_time_minutes']) ?></div>
        </div>

        <div class="task-detail-row">
            <span class="task-detail-label">Автор</span>
            <div class="task-detail-value"><?= htmlspecialchars($taskSet['author_name']) ?></div>
        </div>

        <div class="task-detail-row">
            <span class="task-detail-label">Дата создания</span>
            <div class="task-detail-value"><?= htmlspecialchars($taskSet['created_at']) ?></div>
        </div>
    </div>

    <h2 class="section-title">Задания в наборе</h2>

    <?php if (empty($items)): ?>
        <div class="empty">В этот набор пока не добавлены задания.</div>
    <?php else: ?>
        <form method="POST" id="setItemsBulkForm" class="bulk-actions-panel" aria-live="polite">
            <input type="hidden" name="action" value="bulk_remove">

            <div class="bulk-actions-panel__summary">
                <span class="bulk-actions-panel__count" id="setItemsSelectedCount">Выбрано: 0 заданий</span>
                <button type="button" class="btn-secondary" id="setItemsClearSelectionButton">Снять выбор</button>
            </div>

            <div class="bulk-actions-panel__controls">
                <button type="submit" class="delete-btn">
                    Удалить выбранные из набора
                </button>
            </div>
        </form>

        <div class="card table-card">
            <div class="table-responsive">
                <table id="setItemsTable">
                    <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="set_items_select_all">
                        </th>
                        <th>Порядок</th>
                        <th>ID задания</th>
                        <th>Название задания</th>
                        <th>Тип</th>
                        <th>Дисциплина</th>
                        <th>Баллы</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    name="selected_item_ids[]"
                                    value="<?= (int)$item['id'] ?>"
                                    form="setItemsBulkForm"
                                    class="set-item-row-checkbox"
                                >
                            </td>
                            <td>
                                <form method="POST" class="inline-form">
                                    <input type="hidden" name="action" value="update_item">
                                    <input type="hidden" name="item_id" value="<?= (int)$item['id'] ?>">

                                    <input
                                        type="number"
                                        name="order_number"
                                        min="1"
                                        value="<?= htmlspecialchars($item['order_number']) ?>"
                                        class="compact-input"
                                        required
                                    >
                            </td>
                            <td>
                                <a class="table-link" href="view_task.php?id=<?= (int)$item['task_id'] ?>">
                                    #<?= htmlspecialchars($item['task_id']) ?>
                                </a>
                            </td>
                            <td>
                                <a class="table-link table-link--strong" href="view_task.php?id=<?= (int)$item['task_id'] ?>">
                                    <?= htmlspecialchars($item['title']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($item['task_type_name']) ?></td>
                            <td><?= htmlspecialchars($item['discipline_name']) ?></td>
                            <td>
                                    <input
                                        type="number"
                                        name="max_score"
                                        min="0"
                                        step="0.5"
                                        value="<?= htmlspecialchars($item['max_score']) ?>"
                                        class="compact-input"
                                        required
                                    >
                            </td>
                            <td>
                                    <button type="submit" class="edit-btn">
                                        Сохранить
                                    </button>
                                </form>

                                <form method="POST" class="delete-form" onsubmit="return confirm('Удалить задание из набора?');">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="item_id" value="<?= (int)$item['id'] ?>">
                                    <button type="submit" class="delete-btn">Удалить</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('set_items_select_all');
    const checkboxes = document.querySelectorAll('.set-item-row-checkbox');
    const bulkForm = document.getElementById('setItemsBulkForm');
    const selectedCount = document.getElementById('setItemsSelectedCount');
    const clearButton = document.getElementById('setItemsClearSelectionButton');

    if (!selectAll || !bulkForm) {
        return;
    }

    function formatCount(count) {
        const lastDigit = count % 10;
        const lastTwoDigits = count % 100;
        let word = 'заданий';

        if (lastDigit === 1 && lastTwoDigits !== 11) {
            word = 'задание';
        } else if ([2, 3, 4].includes(lastDigit) && ![12, 13, 14].includes(lastTwoDigits)) {
            word = 'задания';
        }

        return 'Выбрано: ' + count + ' ' + word;
    }

    function getCheckedCount() {
        return Array.from(checkboxes).filter(function (checkbox) {
            return checkbox.checked;
        }).length;
    }

    function updatePanel() {
        const checkedCount = getCheckedCount();
        bulkForm.classList.toggle('is-visible', checkedCount > 0);

        if (selectedCount) {
            selectedCount.textContent = formatCount(checkedCount);
        }

        checkboxes.forEach(function (checkbox) {
            const row = checkbox.closest('tr');
            if (row) {
                row.classList.toggle('is-selected', checkbox.checked);
            }
        });

        selectAll.checked = checkboxes.length > 0 && Array.from(checkboxes).every(function (checkbox) {
            return checkbox.checked;
        });
        selectAll.indeterminate = checkedCount > 0 && !selectAll.checked;
    }

    selectAll.addEventListener('change', function () {
        checkboxes.forEach(function (checkbox) {
            checkbox.checked = selectAll.checked;
        });
        updatePanel();
    });

    checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', updatePanel);
    });

    if (clearButton) {
        clearButton.addEventListener('click', function () {
            checkboxes.forEach(function (checkbox) {
                checkbox.checked = false;
            });
            updatePanel();
        });
    }

    bulkForm.addEventListener('submit', function (event) {
        if (getCheckedCount() === 0 || !confirm('Удалить выбранные задания из набора?')) {
            event.preventDefault();
        }
    });

    updatePanel();
});
</script>

<?php require __DIR__ . '/includes/layout_end.php'; ?>
