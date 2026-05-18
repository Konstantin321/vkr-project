<?php

class TeacherComment
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(int $answerId, int $teacherId, string $commentText): bool
    {
        $sql = '
            INSERT INTO teacher_comments (
                answer_id,
                teacher_id,
                comment_text
            ) VALUES (
                :answer_id,
                :teacher_id,
                :comment_text
            )
        ';

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':answer_id' => $answerId,
            ':teacher_id' => $teacherId,
            ':comment_text' => $commentText,
        ]);
    }

    public function updateOrCreate(int $answerId, int $teacherId, string $commentText): bool
    {
        $sqlCheck = '
            SELECT id FROM teacher_comments
            WHERE answer_id = :answer_id
        ';

        $stmt = $this->pdo->prepare($sqlCheck);
        $stmt->execute([':answer_id' => $answerId]);

        $existing = $stmt->fetch();

        if ($existing) {
            $sqlUpdate = '
                UPDATE teacher_comments
                SET comment_text = :comment_text
                WHERE answer_id = :answer_id
            ';

            $stmt = $this->pdo->prepare($sqlUpdate);

            return $stmt->execute([
                ':comment_text' => $commentText,
                ':answer_id' => $answerId,
            ]);
        }

        $sqlInsert = '
            INSERT INTO teacher_comments (
                answer_id,
                teacher_id,
                comment_text
            ) VALUES (
                :answer_id,
                :teacher_id,
                :comment_text
            )
        ';

        $stmt = $this->pdo->prepare($sqlInsert);

        return $stmt->execute([
            ':answer_id' => $answerId,
            ':teacher_id' => $teacherId,
            ':comment_text' => $commentText,
        ]);
    }
    }