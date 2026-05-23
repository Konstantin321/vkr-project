<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Task.php';

class TaskImportService
{
    private const TASK_TYPE_SINGLE = 2;
    private const MAX_FILE_SIZE = 1048576;

    private PDO $pdo;
    private Task $taskModel;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->connect();
        $this->taskModel = new Task($this->pdo);
    }

    public function getFormData(): array
    {
        return [
            'disciplines' => $this->taskModel->getDisciplines(),
            'folders' => $this->taskModel->getFolders(),
        ];
    }

    public function importSingleChoiceText(array $file, array $postData, int $authorId): array
    {
        $disciplineId = (int)($postData['discipline_id'] ?? 0);
        $folderId = $postData['folder_id'] ?? '';
        $difficulty = trim($postData['difficulty'] ?? '');
        $purpose = trim($postData['purpose'] ?? '');

        $errors = $this->validateUpload($file);

        if ($disciplineId <= 0) {
            $errors[] = 'Выберите дисциплину для импортируемых заданий.';
        }

        if ($purpose !== '' && mb_strlen($purpose) > 255) {
            $errors[] = 'Поле "Назначение" не должно превышать 255 символов.';
        }

        if (!empty($errors)) {
            return $this->result(0, 0, $errors);
        }

        $rawContent = file_get_contents($file['tmp_name']);

        if ($rawContent === false || trim($rawContent) === '') {
            return $this->result(0, 0, ['Файл пустой или не удалось прочитать его содержимое.']);
        }

        $content = $this->normalizeEncoding($rawContent);
        $parseResult = $this->parseSingleChoiceText($content);

        if (!empty($parseResult['errors'])) {
            return $this->result(0, count($parseResult['tasks']), $parseResult['errors']);
        }

        $tasks = $parseResult['tasks'];

        if (empty($tasks)) {
            return $this->result(0, 0, ['В файле не найдено ни одного задания.']);
        }

        try {
            $this->pdo->beginTransaction();

            foreach ($tasks as $task) {
                $taskId = $this->taskModel->create([
                    'title' => $this->buildTitle($task['question'], $task['number']),
                    'task_text' => $task['question'],
                    'difficulty' => $difficulty,
                    'purpose' => $purpose,
                    'reference_answer' => '',
                    'task_type_id' => self::TASK_TYPE_SINGLE,
                    'discipline_id' => $disciplineId,
                    'folder_id' => $folderId,
                    'author_id' => $authorId,
                ]);

                if ($taskId === false) {
                    throw new RuntimeException('Не удалось сохранить задание: ' . $task['question']);
                }

                if (!$this->taskModel->replaceOptions($taskId, $task['options'])) {
                    throw new RuntimeException('Не удалось сохранить варианты ответа для задания #' . $taskId . '.');
                }
            }

            $this->pdo->commit();

            return $this->result(count($tasks), count($tasks), []);
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return $this->result(0, count($tasks), ['Импорт отменён: ' . $exception->getMessage()]);
        }
    }

    private function validateUpload(array $file): array
    {
        $errors = [];

        if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return ['Выберите txt-файл для импорта.'];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['Не удалось загрузить файл. Код ошибки: ' . (int)$file['error'] . '.'];
        }

        if (($file['size'] ?? 0) <= 0) {
            $errors[] = 'Файл пустой.';
        }

        if (($file['size'] ?? 0) > self::MAX_FILE_SIZE) {
            $errors[] = 'Файл слишком большой. Максимальный размер: 1 МБ.';
        }

        $extension = mb_strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));

        if ($extension !== 'txt') {
            $errors[] = 'Поддерживается только формат .txt.';
        }

        return $errors;
    }

    private function parseSingleChoiceText(string $content): array
    {
        $content = preg_replace("/^\xEF\xBB\xBF/", '', $content) ?? $content;
        $content = str_replace(["\r\n", "\r"], "\n", $content);
        $lines = explode("\n", $content);

        $blocks = [];
        $currentBlock = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '') {
                if (!empty($currentBlock)) {
                    $blocks[] = $currentBlock;
                    $currentBlock = [];
                }

                continue;
            }

            if ($this->isQuestionLine($line) && !empty($currentBlock)) {
                $blocks[] = $currentBlock;
                $currentBlock = [];
            }

            $currentBlock[] = $line;
        }

        if (!empty($currentBlock)) {
            $blocks[] = $currentBlock;
        }

        $tasks = [];
        $errors = [];

        foreach ($blocks as $blockIndex => $block) {
            $blockNumber = $blockIndex + 1;
            $questionLine = array_shift($block);
            $questionData = $this->parseQuestionLine($questionLine);
            $question = $questionData['text'];
            $number = $questionData['number'];

            if ($question === '') {
                $errors[] = 'Блок ' . $blockNumber . ': не найден текст задания.';
                continue;
            }

            $options = [];
            $correctCount = 0;

            foreach ($block as $optionLine) {
                $isCorrect = false;

                if (preg_match('/^\*\s*(.+)$/u', $optionLine, $matches) === 1) {
                    $isCorrect = true;
                    $optionLine = trim($matches[1]);
                }

                if ($optionLine === '') {
                    continue;
                }

                if ($isCorrect) {
                    $correctCount++;
                }

                $options[] = [
                    'option_text' => $optionLine,
                    'is_correct' => $isCorrect,
                ];
            }

            if (count($options) < 2) {
                $errors[] = 'Блок ' . $blockNumber . ': у задания должно быть минимум два варианта ответа.';
            }

            if ($correctCount !== 1) {
                $errors[] = 'Блок ' . $blockNumber . ': для типа "Один вариант" должен быть ровно один ответ со звёздочкой.';
            }

            if (empty($errors) || $this->isLastBlockValid($errors, $blockNumber)) {
                $tasks[] = [
                    'number' => $number,
                    'question' => $question,
                    'options' => $options,
                ];
            }
        }

        return [
            'tasks' => $tasks,
            'errors' => $errors,
        ];
    }

    private function normalizeEncoding(string $content): string
    {
        if (mb_check_encoding($content, 'UTF-8')) {
            return $content;
        }

        $converted = @mb_convert_encoding($content, 'UTF-8', 'Windows-1251');

        if (is_string($converted) && $converted !== '') {
            return $converted;
        }

        $converted = @iconv('Windows-1251', 'UTF-8//IGNORE', $content);

        return is_string($converted) ? $converted : $content;
    }

    private function isQuestionLine(string $line): bool
    {
        return preg_match('/^Вопрос\s+\d+/iu', $line) === 1;
    }

    private function parseQuestionLine(string $line): array
    {
        if (preg_match('/^Вопрос\s+(\d+)\s*(.*)$/iu', $line, $matches) === 1) {
            return [
                'number' => (int)$matches[1],
                'text' => trim($matches[2]),
            ];
        }

        return [
            'number' => null,
            'text' => trim($line),
        ];
    }

    private function buildTitle(string $question, ?int $number): string
    {
        $prefix = $number !== null ? 'Вопрос ' . $number . ': ' : '';
        $title = $prefix . $question;

        if (mb_strlen($title) > 250) {
            return rtrim(mb_substr($title, 0, 247)) . '...';
        }

        return $title;
    }

    private function isLastBlockValid(array $errors, int $blockNumber): bool
    {
        $prefix = 'Блок ' . $blockNumber . ':';

        foreach ($errors as $error) {
            if (str_starts_with($error, $prefix)) {
                return false;
            }
        }

        return true;
    }

    private function result(int $importedCount, int $parsedCount, array $errors): array
    {
        return [
            'imported_count' => $importedCount,
            'parsed_count' => $parsedCount,
            'errors' => $errors,
            'success' => empty($errors) && $importedCount > 0,
        ];
    }
}
