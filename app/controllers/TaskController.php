<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../auth/Auth.php';

class TaskController
{
    private Task $taskModel;
    private const TASK_TYPE_OPEN = 1;
    private const TASK_TYPE_SINGLE = 2;
    private const TASK_TYPE_MULTIPLE = 3;

    public function __construct()
    {
        $database = new Database();
        $pdo = $database->connect();
        $this->taskModel = new Task($pdo);
    }

    public function showCreateForm(): array
    {
        return [
            'disciplines' => $this->taskModel->getDisciplines(),
            'taskTypes' => $this->taskModel->getTaskTypes(),
            'folders' => $this->taskModel->getFolders(),
            'authors' => $this->taskModel->getAuthors(),
            'taskListForCopy' => $this->taskModel->getTaskListForCopy(),
        ];
    }

    public function store(array $postData): string
    {
        $title = trim($postData['title'] ?? '');
        $taskText = trim($postData['task_text'] ?? '');
        $difficulty = trim($postData['difficulty'] ?? '');
        $purpose = trim($postData['purpose'] ?? '');
        $referenceAnswer = trim($postData['reference_answer'] ?? '');
        $taskTypeId = $postData['task_type_id'] ?? '';
        $disciplineId = $postData['discipline_id'] ?? '';
        $folderId = $postData['folder_id'] ?? '';

        if ($title === '') {
            return 'Укажите название задания.';
        }

        if (mb_strlen($title) < 3) {
            return 'Название задания должно содержать не менее 3 символов.';
        }

        if (mb_strlen($title) > 255) {
            return 'Название задания не должно превышать 255 символов.';
        }

        if ($taskText === '') {
            return 'Укажите текст задания.';
        }

        if (mb_strlen($taskText) < 10) {
            return 'Текст задания должен содержать не менее 10 символов.';
        }

        if ($taskTypeId === '') {
            return 'Выберите тип задания.';
        }

        if ($disciplineId === '') {
            return 'Выберите дисциплину.';
        }

        if ($purpose !== '' && mb_strlen($purpose) > 255) {
            return 'Поле "Назначение" не должно превышать 255 символов.';
        }

        $optionsResult = $this->prepareOptions((int)$taskTypeId, $postData);

        if ($optionsResult['error'] !== '') {
            return $optionsResult['error'];
        }

        $taskId = $this->taskModel->create([
            'title' => $title,
            'task_text' => $taskText,
            'difficulty' => $difficulty,
            'purpose' => $purpose,
            'reference_answer' => $referenceAnswer,
            'task_type_id' => (int)$taskTypeId,
            'discipline_id' => (int)$disciplineId,
            'folder_id' => $folderId,
            'author_id' => Auth::id(),
        ]);

        if ($taskId === false) {
            return 'Ошибка при сохранении задания.';
        }

        if (!$this->taskModel->replaceOptions($taskId, $optionsResult['options'])) {
            return 'Задание сохранено, но не удалось сохранить варианты ответа.';
        }

        return 'Задание успешно сохранено.';
    }

    public function index(): array
    {
        return $this->taskModel->getAll();
    }

    public function getEditFormData(int $id): array
    {
        return [
            'task' => $this->taskModel->getById($id),
            'options' => $this->taskModel->getOptionsByTaskId($id),
            'disciplines' => $this->taskModel->getDisciplines(),
            'taskTypes' => $this->taskModel->getTaskTypes(),
            'folders' => $this->taskModel->getFolders(),
        ];
    }

    public function update(int $id, array $postData): string
    {
        if ($id <= 0) {
            return 'Некорректный идентификатор задания.';
        }

        $title = trim($postData['title'] ?? '');
        $taskText = trim($postData['task_text'] ?? '');
        $difficulty = trim($postData['difficulty'] ?? '');
        $purpose = trim($postData['purpose'] ?? '');
        $referenceAnswer = trim($postData['reference_answer'] ?? '');
        $taskTypeId = $postData['task_type_id'] ?? '';
        $disciplineId = $postData['discipline_id'] ?? '';
        $folderId = $postData['folder_id'] ?? '';

        if ($title === '' || $taskText === '' || $taskTypeId === '' || $disciplineId === '') {
            return 'Заполнены не все обязательные поля.';
        }

        $optionsResult = $this->prepareOptions((int)$taskTypeId, $postData);

        if ($optionsResult['error'] !== '') {
            return $optionsResult['error'];
        }

        $success = $this->taskModel->update($id, [
            'title' => $title,
            'task_text' => $taskText,
            'difficulty' => $difficulty,
            'purpose' => $purpose,
            'reference_answer' => $referenceAnswer,
            'task_type_id' => (int)$taskTypeId,
            'discipline_id' => (int)$disciplineId,
            'folder_id' => $folderId,
        ]);

        if (!$success) {
            return 'Изменения не сохранены.';
        }

        $this->taskModel->replaceOptions($id, $optionsResult['options']);

        return 'Задание успешно обновлено.';
    }

    public function delete(int $id): string
    {
        if ($id <= 0) {
            return 'Некорректный идентификатор задания.';
        }

        if ($this->taskModel->isUsedInTaskSets($id)) {
            return 'Задание нельзя удалить, так как оно используется в одном или нескольких наборах заданий.';
        }

        $success = $this->taskModel->delete($id);

        if ($success) {
            return 'Задание успешно удалено.';
        }

        return 'Задание не найдено или уже было удалено.';
    }

    public function show(int $id): array|false
    {
        if ($id <= 0) {
            return false;
        }

        $task = $this->taskModel->getByIdForView($id);

        if (!$task) {
            return false;
        }

        $task['options'] = $this->taskModel->getOptionsByTaskId($id);

        return $task;
    }

    public function getTaskDataForCopy(int $id): array|false
    {
        if ($id <= 0) {
            return false;
        }

        $task = $this->taskModel->getById($id);

        if (!$task) {
            return false;
        }

        $task['options'] = $this->taskModel->getOptionsByTaskId($id);

        return $task;
    }

    public function indexWithFilters(array $queryParams): array
    {
        $filters = [
            'search' => trim($queryParams['search'] ?? ''),
            'discipline_id' => $queryParams['discipline_id'] ?? '',
            'task_type_id' => $queryParams['task_type_id'] ?? '',
            'folder_id' => $queryParams['folder_id'] ?? '',
            'author_id' => $queryParams['author_id'] ?? '',
            'sort' => $queryParams['sort'] ?? 'created_at_desc',
        ];

        return $this->taskModel->getAllWithFilters($filters);
    }

    public function handleBulkAction(array $postData): string
    {
        $action = $postData['bulk_action'] ?? '';
        $selectedIds = $postData['selected_ids'] ?? [];

        if (!is_array($selectedIds) || empty($selectedIds)) {
            return 'Не выбрано ни одного задания.';
        }

        $selectedIds = array_map('intval', $selectedIds);
        $selectedIds = array_filter($selectedIds, fn($id) => $id > 0);

        if (empty($selectedIds)) {
            return 'Выбраны некорректные задания.';
        }

        if ($action === 'delete') {
            $deletedCount = $this->taskModel->deleteMultiple($selectedIds);

            return $deletedCount > 0
                ? 'Выбранные задания успешно удалены.'
                : 'Не удалось удалить выбранные задания.';
        }

        if ($action === 'move_to_folder') {
            $folderIdRaw = $postData['bulk_folder_id'] ?? '';

            $folderId = $folderIdRaw === '' ? null : (int)$folderIdRaw;

            $updatedCount = $this->taskModel->moveMultipleToFolder($selectedIds, $folderId);

            return $updatedCount > 0
                ? 'Выбранные задания успешно перемещены.'
                : 'Не удалось переместить выбранные задания.';
        }

        return 'Не выбрано корректное массовое действие.';
    }

    private function prepareOptions(int $taskTypeId, array $postData): array
    {
        if ($taskTypeId === self::TASK_TYPE_OPEN) {
            return [
                'options' => [],
                'error' => '',
            ];
        }

        if (!in_array($taskTypeId, [self::TASK_TYPE_SINGLE, self::TASK_TYPE_MULTIPLE], true)) {
            return [
                'options' => [],
                'error' => '',
            ];
        }

        $optionTexts = $postData['option_texts'] ?? [];

        if (!is_array($optionTexts)) {
            $optionTexts = [];
        }

        $correctIndexes = [];

        if ($taskTypeId === self::TASK_TYPE_SINGLE) {
            $singleIndex = $postData['correct_option_single'] ?? '';

            if ($singleIndex !== '') {
                $correctIndexes[] = (string)$singleIndex;
            }
        }

        if ($taskTypeId === self::TASK_TYPE_MULTIPLE) {
            $multipleIndexes = $postData['correct_options'] ?? [];
            $correctIndexes = is_array($multipleIndexes) ? array_map('strval', $multipleIndexes) : [];
        }

        $options = [];
        $hasCorrect = false;

        foreach ($optionTexts as $index => $text) {
            $text = trim((string)$text);

            if ($text === '') {
                continue;
            }

            $isCorrect = in_array((string)$index, $correctIndexes, true);
            $hasCorrect = $hasCorrect || $isCorrect;

            $options[] = [
                'option_text' => $text,
                'is_correct' => $isCorrect,
            ];
        }

        if (count($options) < 2) {
            return [
                'options' => [],
                'error' => 'Укажите минимум два варианта ответа.',
            ];
        }

        if (!$hasCorrect) {
            return [
                'options' => [],
                'error' => 'Отметьте правильный вариант ответа.',
            ];
        }

        if ($taskTypeId === self::TASK_TYPE_SINGLE) {
            $correctCount = 0;

            foreach ($options as $option) {
                if ($option['is_correct']) {
                    $correctCount++;
                }
            }

            if ($correctCount !== 1) {
                return [
                    'options' => [],
                    'error' => 'Для типа "Один вариант" должен быть выбран ровно один правильный вариант.',
                ];
            }
        }

        return [
            'options' => $options,
            'error' => '',
        ];
    }
}
