<?php

class Answer
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(int $attemptId, int $taskId, string $text): bool
    {
        $sql = "
            INSERT INTO answers (
                attempt_id,
                task_id,
                answer_text
            ) VALUES (
                :attempt_id,
                :task_id,
                :answer_text
            )
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':attempt_id' => $attemptId,
            ':task_id' => $taskId,
            ':answer_text' => $text,
        ]);
    }

    public function getByAttemptAndTask(int $attemptId, int $taskId): array|false
    {
        $sql = '
            SELECT *
            FROM answers
            WHERE attempt_id = :attempt_id
            AND task_id = :task_id
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':attempt_id' => $attemptId,
            ':task_id' => $taskId,
        ]);

        return $stmt->fetch();
    }

    public function updateScore(int $attemptId, int $taskId, float $score): bool
    {
        $sql = '
            UPDATE answers
            SET score = :score
            WHERE attempt_id = :attempt_id
            AND task_id = :task_id
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':score' => $score,
            ':attempt_id' => $attemptId,
            ':task_id' => $taskId,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function getTotalScoreByAttempt(int $attemptId): float
    {
        $sql = '
            SELECT COALESCE(SUM(score), 0)
            FROM answers
            WHERE attempt_id = :attempt_id
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':attempt_id' => $attemptId,
        ]);

        return (float)$stmt->fetchColumn();
    }
}