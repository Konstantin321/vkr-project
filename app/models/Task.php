<?php

class Task
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getDisciplines(): array
    {
        $stmt = $this->pdo->query('SELECT id, name FROM disciplines ORDER BY name');
        return $stmt->fetchAll();
    }

    public function getTaskTypes(): array
    {
        $stmt = $this->pdo->query('SELECT id, name FROM task_types ORDER BY id');
        return $stmt->fetchAll();
    }

    public function getFolders(): array
    {
        $stmt = $this->pdo->query('SELECT id, name FROM folders ORDER BY id');
        return $stmt->fetchAll();
    }

    public function create(array $data): int|false
    {
        $sql = '
            INSERT INTO tasks (
                title,
                task_text,
                difficulty,
                purpose,
                reference_answer,
                task_type_id,
                discipline_id,
                folder_id,
                author_id
            ) VALUES (
                :title,
                :task_text,
                :difficulty,
                :purpose,
                :reference_answer,
                :task_type_id,
                :discipline_id,
                :folder_id,
                :author_id
            )
        ';

        $stmt = $this->pdo->prepare($sql);

        $success = $stmt->execute([
            ':title' => $data['title'],
            ':task_text' => $data['task_text'],
            ':difficulty' => $data['difficulty'],
            ':purpose' => $data['purpose'],
            ':reference_answer' => $data['reference_answer'],
            ':task_type_id' => $data['task_type_id'],
            ':discipline_id' => $data['discipline_id'],
            ':folder_id' => $data['folder_id'] !== '' ? $data['folder_id'] : null,
            ':author_id' => $data['author_id'],
        ]);

        return $success ? (int)$this->pdo->lastInsertId('tasks_id_seq') : false;
    }

    public function getAll(): array
    {
        $sql = '
            SELECT
                t.id,
                t.title,
                t.task_text,
                t.difficulty,
                t.purpose,
                t.created_at,
                tt.name AS task_type_name,
                d.name AS discipline_name,
                f.name AS folder_name,
                u.full_name AS author_name
            FROM tasks t
            JOIN task_types tt ON t.task_type_id = tt.id
            JOIN disciplines d ON t.discipline_id = d.id
            LEFT JOIN folders f ON t.folder_id = f.id
            JOIN users u ON t.author_id = u.id
            ORDER BY t.id DESC
        ';

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false
    {
        $sql = '
            SELECT
                id,
                title,
                task_text,
                difficulty,
                purpose,
                reference_answer,
                task_type_id,
                discipline_id,
                folder_id
            FROM tasks
            WHERE id = :id
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }

    public function update(int $id, array $data): bool
    {
        $sql = '
            UPDATE tasks
            SET
                title = :title,
                task_text = :task_text,
                difficulty = :difficulty,
                purpose = :purpose,
                reference_answer = :reference_answer,
                task_type_id = :task_type_id,
                discipline_id = :discipline_id,
                folder_id = :folder_id
            WHERE id = :id
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':title' => $data['title'],
            ':task_text' => $data['task_text'],
            ':difficulty' => $data['difficulty'],
            ':purpose' => $data['purpose'],
            ':reference_answer' => $data['reference_answer'],
            ':task_type_id' => $data['task_type_id'],
            ':discipline_id' => $data['discipline_id'],
            ':folder_id' => $data['folder_id'] !== '' ? $data['folder_id'] : null,
        ]);

        return true;
    }

    public function delete(int $id): bool
    {
        $sql = 'DELETE FROM tasks WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->rowCount() > 0;
    }

    public function getByIdForView(int $id): array|false
    {
        $sql = '
            SELECT
                t.id,
                t.title,
                t.task_text,
                t.difficulty,
                t.purpose,
                t.reference_answer,
                t.task_type_id,
                t.created_at,
                tt.name AS task_type_name,
                d.name AS discipline_name,
                f.name AS folder_name,
                u.full_name AS author_name
            FROM tasks t
            JOIN task_types tt ON t.task_type_id = tt.id
            JOIN disciplines d ON t.discipline_id = d.id
            LEFT JOIN folders f ON t.folder_id = f.id
            JOIN users u ON t.author_id = u.id
            WHERE t.id = :id
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }

    public function getOptionsByTaskId(int $taskId): array
    {
        $sql = '
            SELECT
                id,
                task_id,
                option_text,
                CASE WHEN is_correct THEN 1 ELSE 0 END AS is_correct,
                sort_order
            FROM task_options
            WHERE task_id = :task_id
            ORDER BY sort_order ASC, id ASC
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':task_id' => $taskId]);

        return $stmt->fetchAll();
    }

    public function replaceOptions(int $taskId, array $options): bool
    {
        $deleteStmt = $this->pdo->prepare('DELETE FROM task_options WHERE task_id = :task_id');
        $deleteStmt->execute([':task_id' => $taskId]);

        if (empty($options)) {
            return true;
        }

        $sql = '
            INSERT INTO task_options (
                task_id,
                option_text,
                is_correct,
                sort_order
            ) VALUES (
                :task_id,
                :option_text,
                :is_correct,
                :sort_order
            )
        ';

        $stmt = $this->pdo->prepare($sql);

        foreach ($options as $index => $option) {
            $stmt->execute([
                ':task_id' => $taskId,
                ':option_text' => $option['option_text'],
                ':is_correct' => !empty($option['is_correct']),
                ':sort_order' => $index + 1,
            ]);
        }

        return true;
    }

    public function getAllWithFilters(array $filters): array
    {
        $sql = '
            SELECT
                t.id,
                t.title,
                t.task_text,
                t.difficulty,
                t.purpose,
                t.created_at,
                tt.name AS task_type_name,
                d.name AS discipline_name,
                f.name AS folder_name,
                u.full_name AS author_name
            FROM tasks t
            JOIN task_types tt ON t.task_type_id = tt.id
            JOIN disciplines d ON t.discipline_id = d.id
            LEFT JOIN folders f ON t.folder_id = f.id
            JOIN users u ON t.author_id = u.id
            WHERE 1=1
        ';

        $params = [];

        if (!empty($filters['search'])) {
            $sql .= ' AND t.title ILIKE :search';
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['discipline_id'])) {
            $sql .= ' AND t.discipline_id = :discipline_id';
            $params[':discipline_id'] = (int)$filters['discipline_id'];
        }

        if (!empty($filters['task_type_id'])) {
            $sql .= ' AND t.task_type_id = :task_type_id';
            $params[':task_type_id'] = (int)$filters['task_type_id'];
        }

        if (!empty($filters['folder_id'])) {
            $sql .= ' AND t.folder_id = :folder_id';
            $params[':folder_id'] = (int)$filters['folder_id'];
        }

        if (!empty($filters['author_id'])) {
            $sql .= ' AND t.author_id = :author_id';
            $params[':author_id'] = (int)$filters['author_id'];
        }

        $sort = $filters['sort'] ?? 'created_at_desc';

        switch ($sort) {
            case 'title_asc':
                $sql .= ' ORDER BY t.title ASC';
                break;
            case 'title_desc':
                $sql .= ' ORDER BY t.title DESC';
                break;
            case 'created_at_asc':
                $sql .= ' ORDER BY t.created_at ASC';
                break;
            case 'author_asc':
                $sql .= ' ORDER BY u.full_name ASC';
                break;
            case 'author_desc':
                $sql .= ' ORDER BY u.full_name DESC';
                break;
            case 'created_at_desc':
            default:
                $sql .= ' ORDER BY t.created_at DESC';
                break;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function getAuthors(): array
    {
        $sql = "
            SELECT DISTINCT
                u.id,
                u.full_name
            FROM users u
            JOIN tasks t ON t.author_id = u.id
            ORDER BY u.full_name
        ";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function getTaskListForCopy(): array
    {
        $sql = '
            SELECT id, title
            FROM tasks
            ORDER BY id DESC
        ';

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function deleteMultiple(array $ids): int
    {
        if (empty($ids)) {
            return 0;
        }

        $placeholders = [];
        $params = [];

        foreach ($ids as $index => $id) {
            $key = ':id_' . $index;
            $placeholders[] = $key;
            $params[$key] = (int)$id;
        }

        $sql = 'DELETE FROM tasks WHERE id IN (' . implode(', ', $placeholders) . ')';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount();
    }

    public function moveMultipleToFolder(array $ids, ?int $folderId): int
    {
        if (empty($ids)) {
            return 0;
        }

        $placeholders = [];
        $params = [
            ':folder_id' => $folderId,
        ];

        foreach ($ids as $index => $id) {
            $key = ':id_' . $index;
            $placeholders[] = $key;
            $params[$key] = (int)$id;
        }

        $sql = '
            UPDATE tasks
            SET folder_id = :folder_id
            WHERE id IN (' . implode(', ', $placeholders) . ')
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount();
    }

        public function isUsedInTaskSets(int $id): bool
    {
        $sql = '
            SELECT COUNT(*)
            FROM task_set_items
            WHERE task_id = :task_id
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':task_id' => $id,
        ]);

        return (int)$stmt->fetchColumn() > 0;
    }
}
