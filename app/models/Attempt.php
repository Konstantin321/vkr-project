<?php

class Attempt
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(int $taskSetId, int $studentId): int|false
    {
        $sql = '
            INSERT INTO attempts (
                task_set_id,
                student_id,
                status
            ) VALUES (
                :task_set_id,
                :student_id,
                :status
            )
            RETURNING id
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':task_set_id' => $taskSetId,
            ':student_id' => $studentId,
            ':status' => 'started',
        ]);

        $result = $stmt->fetch();

        return $result ? (int)$result['id'] : false;
    }

    public function getAttemptWithTasks(int $attemptId): array|false
    {
        $sql = "
            SELECT 
                a.id AS attempt_id,
                a.task_set_id,
                ts.name AS task_set_name,
                t.id AS task_id,
                t.title,
                t.task_text,
                t.task_type_id,
                tt.name AS task_type_name,
                tsi.order_number,
                tsi.max_score
            FROM attempts a
            JOIN task_sets ts ON ts.id = a.task_set_id
            JOIN task_set_items tsi ON tsi.task_set_id = ts.id
            JOIN tasks t ON t.id = tsi.task_id
            JOIN task_types tt ON tt.id = t.task_type_id
            WHERE a.id = :attempt_id
            ORDER BY tsi.order_number
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':attempt_id' => $attemptId]);

        return $stmt->fetchAll();
    }

    public function getOptionsByTaskIds(array $taskIds): array
    {
        $taskIds = array_values(array_unique(array_filter(array_map('intval', $taskIds))));

        if (empty($taskIds)) {
            return [];
        }

        $placeholders = [];
        $params = [];

        foreach ($taskIds as $index => $taskId) {
            $key = ':task_id_' . $index;
            $placeholders[] = $key;
            $params[$key] = $taskId;
        }

        $sql = '
            SELECT
                id,
                task_id,
                option_text,
                CASE WHEN is_correct THEN 1 ELSE 0 END AS is_correct,
                sort_order
            FROM task_options
            WHERE task_id IN (' . implode(', ', $placeholders) . ')
            ORDER BY task_id ASC, sort_order ASC, id ASC
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $grouped = [];

        foreach ($stmt->fetchAll() as $option) {
            $grouped[(int)$option['task_id']][] = $option;
        }

        return $grouped;
    }

    public function finish(int $attemptId): bool
    {
        $sql = '
            UPDATE attempts
            SET
                status = :status,
                finished_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id' => $attemptId,
            ':status' => 'completed',
        ]);

        return $stmt->rowCount() > 0;
    }

    public function getById(int $attemptId): array|false
    {
        $sql = '
            SELECT id, task_set_id, student_id, started_at, finished_at, status
            FROM attempts
            WHERE id = :id
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $attemptId]);

        return $stmt->fetch();
    }

    public function getAttemptAnswers(int $attemptId): array
    {
        $sql = "
            SELECT
                a.id AS attempt_id,
                a.started_at,
                a.finished_at,
                a.status,
                ts.name AS task_set_name,
                t.id AS task_id,
                t.title,
                t.task_text,
                t.task_type_id,
                tt.name AS task_type_name,
                tsi.order_number,
                tsi.max_score,
                ans.answer_text,
                ans.score,
                tc.comment_text
            FROM attempts a
            JOIN task_sets ts ON ts.id = a.task_set_id
            JOIN task_set_items tsi ON tsi.task_set_id = ts.id
            JOIN tasks t ON t.id = tsi.task_id
            JOIN task_types tt ON tt.id = t.task_type_id
            LEFT JOIN answers ans
                ON ans.attempt_id = a.id
            AND ans.task_id = t.id
            LEFT JOIN teacher_comments tc
                ON tc.answer_id = ans.id
            WHERE a.id = :attempt_id
            ORDER BY tsi.order_number ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':attempt_id' => $attemptId]);

        return $stmt->fetchAll();
    }

    public function getAllAttempts(): array
    {
        $sql = "
            SELECT
                a.id,
                a.started_at,
                a.finished_at,
                a.status,
                ts.name AS task_set_name,
                u.full_name AS student_name
            FROM attempts a
            JOIN task_sets ts ON ts.id = a.task_set_id
            JOIN users u ON u.id = a.student_id
            ORDER BY a.id DESC
        ";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
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

    public function getScoreBreakdown(int $attemptId): array
    {
        $sql = "
            SELECT
                tsi.order_number,
                t.title,
                COALESCE(ans.score, 0) AS score,
                tsi.max_score
            FROM attempts a
            JOIN task_sets ts ON ts.id = a.task_set_id
            JOIN task_set_items tsi ON tsi.task_set_id = ts.id
            JOIN tasks t ON t.id = tsi.task_id
            LEFT JOIN answers ans
                ON ans.attempt_id = a.id
            AND ans.task_id = t.id
            WHERE a.id = :attempt_id
            ORDER BY tsi.order_number ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':attempt_id' => $attemptId,
        ]);

        return $stmt->fetchAll();
    }
}
