<?php

require_once __DIR__ . '/../app/auth/Auth.php';
Auth::requireAuth();
Auth::requireRole(['teacher']);

require_once __DIR__ . '/../app/controllers/TaskSetController.php';

$controller = new TaskSetController();
$message = '';
$selectedTaskSetId = $_POST['task_set_id'] ?? '';
$selectedTasks = $_POST['selected_tasks'] ?? [];
$orderValues = $_POST['order_number'] ?? [];
$scoreValues = $_POST['max_score'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $controller->addMultipleTasksToSet($_POST);

    if ($message === 'Выбранные задания добавлены в набор.') {
        $selectedTaskSetId = '';
        $selectedTasks = [];
        $orderValues = [];
        $scoreValues = [];
    }
}
$formData = $controller->getFormData();

$pageTitle = 'Добавить задания в набор';
$activePage = 'sets';
require __DIR__ . '/includes/layout_start.php';
?>

    <div class="top-links">
        <a href="task_sets_list.php">К списку наборов</a>
        <a href="create_task_set.php">Создать набор</a>
        <a href="tasks_list.php">Список заданий</a>
    </div>

    <div class="page-header">
        <div>
            <h1 class="page-title">Добавить задания в набор</h1>
            <p class="page-subtitle">Выберите набор, задания, порядок и максимальные баллы.</p>
        </div>
    </div>

    <?php if ($message !== ''): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (empty($formData['taskSets'])): ?>
        <div class="empty">Сначала создайте хотя бы один набор заданий.</div>
    <?php elseif (empty($formData['tasks'])): ?>
        <div class="empty">В системе пока нет заданий для добавления в набор.</div>
    <?php else: ?>
        <form method="POST" id="addTasksToSetForm">

            <div class="panel form-block">
                <label for="task_set_id">Выберите набор заданий</label>
                <select name="task_set_id" id="task_set_id" required>
                    <option value="">Выберите набор</option>
                    <?php foreach ($formData['taskSets'] as $set): ?>
                        <option
                            value="<?= $set['id'] ?>"
                            <?= (string)$selectedTaskSetId === (string)$set['id'] ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($set['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="bulk-actions-panel" id="addTasksPanel" aria-live="polite">
                <div class="bulk-actions-panel__summary">
                    <span class="bulk-actions-panel__count" id="addTasksSelectedCount">Выбрано: 0 заданий</span>
                    <button type="button" class="btn-secondary" id="addTasksClearSelectionButton">Снять выбор</button>
                </div>

                <div class="bulk-actions-panel__controls">
                    <button type="submit">
                        Добавить выбранные задания в набор
                    </button>
                </div>
            </div>

            <div class="card table-card">
                <div class="table-responsive">
                    <table id="availableTasksTable">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="available_tasks_select_all">
                            </th>
                            <th>ID</th>
                            <th>Название задания</th>
                            <th>Порядок</th>
                            <th>Баллы</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($formData['tasks'] as $task): ?>
                            <tr>
                                <td>
                                    <input
                                    type="checkbox"
                                    name="selected_tasks[]"
                                    value="<?= (int)$task['id'] ?>"
                                    class="available-task-checkbox"
                                    <?= in_array((string)$task['id'], array_map('strval', $selectedTasks), true) ? 'checked' : '' ?>
                                >
                                </td>
                                <td><?= (int)$task['id'] ?></td>
                                <td><?= htmlspecialchars($task['title']) ?></td>
                                <td>
                                    <input
                                        type="number"
                                        name="order_number[<?= (int)$task['id'] ?>]"
                                        min="1"
                                        placeholder="Напр. 1"
                                        value="<?= htmlspecialchars($orderValues[$task['id']] ?? '') ?>"
                                        class="compact-input"
                                    >
                                </td>
                                <td>
                                    <input
                                        type="number"
                                        name="max_score[<?= (int)$task['id'] ?>]"
                                        min="0"
                                        step="0.5"
                                        placeholder="Напр. 5"
                                        value="<?= htmlspecialchars($scoreValues[$task['id']] ?? '') ?>"
                                        class="compact-input"
                                    >
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    </table>
                </div>
            </div>
        </form>
    <?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('available_tasks_select_all');
    const checkboxes = document.querySelectorAll('.available-task-checkbox');
    const panel = document.getElementById('addTasksPanel');
    const selectedCount = document.getElementById('addTasksSelectedCount');
    const clearButton = document.getElementById('addTasksClearSelectionButton');
    const form = document.getElementById('addTasksToSetForm');

    if (!selectAll || !panel || !form) {
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
        panel.classList.toggle('is-visible', checkedCount > 0);

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

    form.addEventListener('submit', function (event) {
        if (getCheckedCount() === 0) {
            event.preventDefault();
        }
    });

    updatePanel();
});
</script>

<?php require __DIR__ . '/includes/layout_end.php'; ?>
