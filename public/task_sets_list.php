<?php

require_once __DIR__ . '/../app/auth/Auth.php';
Auth::requireAuth();
Auth::requireRole(['teacher']);

require_once __DIR__ . '/../app/controllers/TaskSetController.php';

$controller = new TaskSetController();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $message = $controller->delete((int)($_POST['delete_id'] ?? 0));
    } elseif ($action === 'bulk_delete') {
        $message = $controller->deleteMultiple($_POST);
    }
}

$taskSets = $controller->index();

$pageTitle = 'Список наборов заданий';
$activePage = 'sets';
require __DIR__ . '/includes/layout_start.php';
?>

    <div class="page-header">
        <div>
            <h1 class="page-title">Список наборов заданий</h1>
            <p class="page-subtitle">Управление наборами, временем выполнения и составом заданий.</p>
        </div>
        <a href="create_task_set.php" class="btn btn-primary">Создать набор</a>
    </div>

    <div class="top-links">
        <a href="add_task_to_set.php">Добавить задания в набор</a>
        <a href="tasks_list.php">Список заданий</a>
    </div>

    <?php if ($message !== ''): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (empty($taskSets)): ?>
        <div class="empty">Наборы заданий пока не созданы.</div>
    <?php else: ?>
        <form method="POST" id="setsBulkForm" class="bulk-actions-panel" aria-live="polite">
            <input type="hidden" name="action" value="bulk_delete">

            <div class="bulk-actions-panel__summary">
                <span class="bulk-actions-panel__count" id="setsSelectedCount">Выбрано: 0 наборов</span>
                <button type="button" class="btn-secondary" id="setsClearSelectionButton">Снять выбор</button>
            </div>

            <div class="bulk-actions-panel__controls">
                <button type="submit" class="delete-btn">
                    Удалить выбранные
                </button>
            </div>
        </form>

        <div class="card table-card">
            <div class="table-responsive">
                <table id="taskSetsTable">
                    <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="sets_select_all">
                        </th>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Описание</th>
                        <th>Время (мин)</th>
                        <th>Автор</th>
                        <th>Дата создания</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($taskSets as $set): ?>
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    name="selected_set_ids[]"
                                    value="<?= (int)$set['id'] ?>"
                                    form="setsBulkForm"
                                    class="set-row-checkbox"
                                >
                            </td>
                            <td><?= htmlspecialchars($set['id']) ?></td>
                            <td><?= htmlspecialchars($set['name']) ?></td>
                            <td><?= htmlspecialchars($set['description'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($set['execution_time_minutes']) ?></td>
                            <td><?= htmlspecialchars($set['author_name']) ?></td>
                            <td><?= htmlspecialchars($set['created_at']) ?></td>
                            <td>
                                <div class="actions">
                                    <a href="view_task_set.php?id=<?= (int)$set['id'] ?>" class="view-btn">
                                        Просмотр
                                    </a>

                                    <a href="edit_task_set.php?id=<?= (int)$set['id'] ?>" class="edit-btn">
                                        Редактировать
                                    </a>

                                    <form method="POST" class="delete-form" onsubmit="return confirm('Удалить этот набор заданий?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="delete_id" value="<?= (int)$set['id'] ?>">
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
    <?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('sets_select_all');
    const checkboxes = document.querySelectorAll('.set-row-checkbox');
    const bulkForm = document.getElementById('setsBulkForm');
    const selectedCount = document.getElementById('setsSelectedCount');
    const clearButton = document.getElementById('setsClearSelectionButton');

    if (!selectAll || !bulkForm) {
        return;
    }

    function formatCount(count) {
        const lastDigit = count % 10;
        const lastTwoDigits = count % 100;
        let word = 'наборов';

        if (lastDigit === 1 && lastTwoDigits !== 11) {
            word = 'набор';
        } else if ([2, 3, 4].includes(lastDigit) && ![12, 13, 14].includes(lastTwoDigits)) {
            word = 'набора';
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
        if (getCheckedCount() === 0 || !confirm('Удалить выбранные наборы заданий?')) {
            event.preventDefault();
        }
    });

    updatePanel();
});
</script>

<?php require __DIR__ . '/includes/layout_end.php'; ?>
