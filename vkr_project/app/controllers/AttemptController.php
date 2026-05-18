<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Attempt.php';
require_once __DIR__ . '/../models/TaskSet.php';
require_once __DIR__ . '/../models/Answer.php';
require_once __DIR__ . '/../models/Result.php';
require_once __DIR__ . '/../models/TeacherComment.php';

class AttemptController
{
    private Attempt $attemptModel;
    private TaskSet $taskSetModel;
    private Answer $answerModel;
    private Result $resultModel;
    private TeacherComment $teacherCommentModel;

    public function __construct()
    {
        $database = new Database();
        $pdo = $database->connect();

        $this->attemptModel = new Attempt($pdo);
        $this->taskSetModel = new TaskSet($pdo);
        $this->answerModel = new Answer($pdo);
        $this->resultModel = new Result($pdo);
        $this->teacherCommentModel = new TeacherComment($pdo);
    }

    public function getTaskSets(): array
    {
        return $this->taskSetModel->getAll();
    }

    public function start(array $postData): array
    {
        $taskSetId = (int)($postData['task_set_id'] ?? 0);

        if ($taskSetId <= 0) {
            return [
                'success' => false,
                'message' => 'Выберите набор заданий.',
                'attempt_id' => null,
            ];
        }

        // Пока используем тестового обучающегося с id = 1
        $attemptId = $this->attemptModel->create($taskSetId, 1);

        if ($attemptId === false) {
            return [
                'success' => false,
                'message' => 'Ошибка при запуске попытки.',
                'attempt_id' => null,
            ];
        }

        return [
            'success' => true,
            'message' => 'Попытка успешно создана.',
            'attempt_id' => $attemptId,
        ];
    }

    public function show(int $attemptId): array|false
    {
        if ($attemptId <= 0) {
            return false;
        }

        $data = $this->attemptModel->getAttemptWithTasks($attemptId);

        return !empty($data) ? $data : false;
    }

    public function submitAnswers(int $attemptId, array $postData): string
    {
        if ($attemptId <= 0) {
            return 'Некорректная попытка.';
        }

        $attempt = $this->attemptModel->getById($attemptId);

        if (!$attempt) {
            return 'Попытка не найдена.';
        }

        if (($attempt['status'] ?? '') === 'completed') {
            return 'Эта попытка уже завершена.';
        }

        if (!isset($postData['answers']) || !is_array($postData['answers'])) {
            return 'Ответы не переданы.';
        }

        foreach ($postData['answers'] as $taskId => $answerText) {
            $taskId = (int)$taskId;
            $answerText = trim($answerText);

            if ($taskId <= 0) {
                continue;
            }

            $this->answerModel->save($attemptId, $taskId, $answerText);
        }

        $finished = $this->attemptModel->finish($attemptId);

        if (!$finished) {
            return 'Ответы сохранены, но не удалось завершить попытку.';
        }

        if (!$this->resultModel->existsByAttemptId($attemptId)) {
            $this->resultModel->create($attemptId, 0, 'Не проверено');
        }

        return 'Попытка завершена, ответы сохранены.';
    }

    public function viewAnswers(int $attemptId): array|false
    {
        if ($attemptId <= 0) {
            return false;
        }

        $data = $this->attemptModel->getAttemptAnswers($attemptId);

        return !empty($data) ? $data : false;
    }

    public function index(): array
    {
        return $this->attemptModel->getAllAttempts();
    }

    public function reviewAnswer(array $postData): string
    {
        $attemptId = (int)($postData['attempt_id'] ?? 0);
        $taskId = (int)($postData['task_id'] ?? 0);
        $scoreRaw = $postData['score'] ?? '';
        $commentText = trim($postData['comment_text'] ?? '');

        if ($attemptId <= 0 || $taskId <= 0) {
            return 'Некорректные данные для проверки.';
        }

        if ($this->resultModel->isReviewFinished($attemptId)) {
            return 'Проверка этой попытки уже завершена. Изменения недоступны.';
        }

        if ($scoreRaw === '' || $scoreRaw === null) {
            return 'Укажите балл.';
        }

        $score = (float)$scoreRaw;

        if ($score < 0) {
            return 'Балл не может быть отрицательным.';
        }

        $updated = $this->answerModel->updateScore($attemptId, $taskId, $score);

        if (!$updated) {
            return 'Не удалось обновить балл.';
        }

        if ($commentText !== '') {
            $answer = $this->answerModel->getByAttemptAndTask($attemptId, $taskId);

            if ($answer) {
                $this->teacherCommentModel->updateOrCreate((int)$answer['id'], 1, $commentText);
            }
        }

        return 'Ответ успешно проверен.';
    }

    public function finishReview(int $attemptId): string
    {
        if ($attemptId <= 0) {
            return 'Некорректная попытка.';
        }

        if ($this->resultModel->isReviewFinished($attemptId)) {
            return 'Проверка этой попытки уже завершена.';
        }

        $totalScore = $this->answerModel->getTotalScoreByAttempt($attemptId);
        $breakdown = $this->attemptModel->getScoreBreakdown($attemptId);

        if (empty($breakdown)) {
            return 'Не удалось получить данные по заданиям для итоговой проверки.';
        }

        $parts = [];

        foreach ($breakdown as $item) {
            $parts[] = 'Задание ' . $item['order_number'] . ': ' . $item['score'] . ' из ' . $item['max_score'];
        }

        $breakdownText = implode('; ', $parts);

        $updated = $this->resultModel->updateResultSummary(
            $attemptId,
            $totalScore,
            'Проверено',
            $breakdownText
        );

        return $updated
            ? 'Проверка попытки завершена.'
            : 'Не удалось завершить проверку попытки.';
    }

    public function isReviewFinished(int $attemptId): bool
    {
        if ($attemptId <= 0) {
            return false;
        }

        return $this->resultModel->isReviewFinished($attemptId);
    }
}