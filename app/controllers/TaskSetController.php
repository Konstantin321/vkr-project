<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/TaskSet.php';
require_once __DIR__ . '/../auth/Auth.php';

class TaskSetController
{
    private TaskSet $taskSetModel;

    public function __construct()
    {
        $database = new Database();
        $pdo = $database->connect();
        $this->taskSetModel = new TaskSet($pdo);
    }

    public function store(array $postData): string
    {
        $name = trim($postData['name'] ?? '');
        $description = trim($postData['description'] ?? '');
        $time = (int)($postData['execution_time_minutes'] ?? 0);

        if ($name === '' || $time <= 0) {
            return 'Заполните название и время выполнения.';
        }

        $success = $this->taskSetModel->create([
            'name' => $name,
            'description' => $description,
            'execution_time_minutes' => $time,
            'created_by' => Auth::id(),
        ]);

        return $success ? 'Набор заданий создан.' : 'Ошибка при создании.';
    }

    public function addTaskToSet(array $postData): string
    {
        $taskSetId = (int)($postData['task_set_id'] ?? 0);
        $taskId = (int)($postData['task_id'] ?? 0);
        $order = (int)($postData['order_number'] ?? 0);
        $score = (float)($postData['max_score'] ?? 0);

        if ($taskSetId <= 0 || $taskId <= 0) {
            return 'Заполните все обязательные поля.';
        }

        if ($order <= 0) {
            return 'Порядок должен быть положительным числом.';
        }

        if ($score < 0) {
            return 'Количество баллов не может быть отрицательным.';
        }

        $success = $this->taskSetModel->addTask($taskSetId, $taskId, $order, $score);

        return $success ? 'Задание добавлено в набор.' : 'Ошибка при добавлении.';
    }

    public function getFormData(): array
    {
        $taskSets = $this->taskSetModel->getAllSets();
        $tasks = $this->taskSetModel->getAllTasks();

        return [
            'taskSets' => $taskSets,
            'tasks' => $tasks,
        ];
    }

    public function addMultipleTasksToSet(array $postData): string
    {
        $taskSetId = (int)($postData['task_set_id'] ?? 0);

        if ($taskSetId <= 0) {
            return 'Выберите набор заданий.';
        }

        $selectedTasks = $postData['selected_tasks'] ?? [];

        if (empty($selectedTasks)) {
            return 'Выберите хотя бы одно задание.';
        }

        foreach ($selectedTasks as $taskId) {
            $taskId = (int)$taskId;

            $order = (int)($postData['order_number'][$taskId] ?? 0);

            $scoreRaw = $postData['max_score'][$taskId] ?? '';

            if ($scoreRaw === '' || $scoreRaw === null) {
                return 'Для каждого выбранного задания необходимо указать количество баллов.';
            }

            $score = (float)$scoreRaw;

            if ($order <= 0) {
                return 'Для каждого выбранного задания порядок должен быть положительным числом.';
            }

            if ($score < 0) {
                return 'Количество баллов не может быть отрицательным.';
            }

            if ($this->taskSetModel->existsTaskInSet($taskSetId, $taskId)) {
                return 'Одно из выбранных заданий уже добавлено в этот набор.';
            }

            if ($this->taskSetModel->existsOrderInSet($taskSetId, $order)) {
                return 'Указанный порядок уже используется в выбранном наборе.';
            }

            $success = $this->taskSetModel->addTask($taskSetId, $taskId, $order, $score);

            if (!$success) {
                return 'Ошибка при добавлении одного из заданий в набор.';
            }
        }

        return 'Выбранные задания добавлены в набор.';
    }

    public function index(): array
    {
        return $this->taskSetModel->getAll();
    }

    public function show(int $id): array|false
    {
        if ($id <= 0) {
            return false;
        }

        $taskSet = $this->taskSetModel->getById($id);

        if (!$taskSet) {
            return false;
        }

        $items = $this->taskSetModel->getItemsBySetId($id);

        return [
            'taskSet' => $taskSet,
            'items' => $items,
        ];
    }

    public function removeTaskFromSet(array $postData): string
    {
        $itemId = (int)($postData['item_id'] ?? 0);

        if ($itemId <= 0) {
            return 'Некорректный идентификатор.';
        }

        $success = $this->taskSetModel->removeItem($itemId);

        return $success ? 'Задание удалено из набора.' : 'Ошибка при удалении.';
    }

    public function update(int $id, array $postData): string
    {
        if ($id <= 0) {
            return 'Некорректный идентификатор набора.';
        }

        $name = trim($postData['name'] ?? '');
        $description = trim($postData['description'] ?? '');
        $time = (int)($postData['execution_time_minutes'] ?? 0);

        if ($name === '' || $time <= 0) {
            return 'Заполните название и время выполнения.';
        }

        $success = $this->taskSetModel->update($id, [
            'name' => $name,
            'description' => $description,
            'execution_time_minutes' => $time,
        ]);

        return $success ? 'Набор заданий успешно обновлён.' : 'Изменения не сохранены.';
    }

    public function updateSetItem(array $postData): string
    {
        $itemId = (int)($postData['item_id'] ?? 0);
        $orderNumber = (int)($postData['order_number'] ?? 0);

        $scoreRaw = $postData['max_score'] ?? '';

        if ($itemId <= 0) {
            return 'Некорректный идентификатор элемента набора.';
        }

        if ($orderNumber <= 0) {
            return 'Порядок должен быть положительным числом.';
        }

        if ($scoreRaw === '' || $scoreRaw === null) {
            return 'Укажите количество баллов.';
        }

        $maxScore = (float)$scoreRaw;

        if ($maxScore < 0) {
            return 'Количество баллов не может быть отрицательным.';
        }

        $item = $this->taskSetModel->getItemById($itemId);

        if (!$item) {
            return 'Элемент набора не найден.';
        }

        $taskSetId = (int)$item['task_set_id'];

        if ($this->taskSetModel->existsOrderInSetExceptItem($taskSetId, $orderNumber, $itemId)) {
            return 'Указанный порядок уже используется в этом наборе.';
        }

        $success = $this->taskSetModel->updateItem($itemId, $orderNumber, $maxScore);

        return $success ? 'Параметры задания в наборе обновлены.' : 'Изменения не сохранены.';
    }

    public function delete(int $id): string
    {
        if ($id <= 0) {
            return 'Некорректный идентификатор набора.';
        }

        $success = $this->taskSetModel->delete($id);

        return $success ? 'Набор заданий успешно удалён.' : 'Набор не найден или уже был удалён.';
    }
}
