<?php

class Result
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(int $attemptId, float $totalScore, string $grade): bool
    {
        $sql = '
            INSERT INTO results (
                attempt_id,
                total_score,
                grade
            ) VALUES (
                :attempt_id,
                :total_score,
                :grade
            )
        ';

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':attempt_id' => $attemptId,
            ':total_score' => $totalScore,
            ':grade' => $grade,
        ]);
    }

    public function existsByAttemptId(int $attemptId): bool
    {
        $sql = '
            SELECT COUNT(*)
            FROM results
            WHERE attempt_id = :attempt_id
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':attempt_id' => $attemptId]);

        return (int)$stmt->fetchColumn() > 0;
    }

    public function updateTotalScore(int $attemptId, float $totalScore): bool
    {
        $sql = '
            UPDATE results
            SET total_score = :total_score
            WHERE attempt_id = :attempt_id
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':total_score' => $totalScore,
            ':attempt_id' => $attemptId,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function updateResultSummary(int $attemptId, float $totalScore, string $grade, string $scoreBreakdown): bool
    {
        $sql = '
            UPDATE results
            SET
                total_score = :total_score,
                grade = :grade,
                score_breakdown = :score_breakdown
            WHERE attempt_id = :attempt_id
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':total_score' => $totalScore,
            ':grade' => $grade,
            ':score_breakdown' => $scoreBreakdown,
            ':attempt_id' => $attemptId,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function isReviewFinished(int $attemptId): bool
    {
        $sql = '
            SELECT COUNT(*)
            FROM results
            WHERE attempt_id = :attempt_id
            AND grade = :grade
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':attempt_id' => $attemptId,
            ':grade' => 'Проверено',
        ]);

        return (int)$stmt->fetchColumn() > 0;
    }
}