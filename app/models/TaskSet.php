<?php

class TaskSet
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(array $data): bool
    {
        $sql = '
            INSERT INTO task_sets (
                name,
                description,
                execution_time_minutes,
                created_by
            ) VALUES (
                :name,
                :description,
                :execution_time_minutes,
                :created_by
            )
        ';

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'],
            ':execution_time_minutes' => $data['execution_time_minutes'],
            ':created_by' => $data['created_by'],
        ]);
    }

    public function addTask(int $taskSetId, int $taskId, int $order, float $score): bool
    {
    $sql = '
        INSERT INTO task_set_items (
            task_set_id,
            task_id,
            order_number,
            max_score
        ) VALUES (
            :task_set_id,
            :task_id,
            :order_number,
            :max_score
        )
    ';

    $stmt = $this->pdo->prepare($sql);

    return $stmt->execute([
        ':task_set_id' => $taskSetId,
        ':task_id' => $taskId,
        ':order_number' => $order,
        ':max_score' => $score,
    ]);
    }

    public function getAllSets(): array
    {
        $stmt = $this->pdo->query('SELECT id, name FROM task_sets ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    public function getAllTasks(): array
    {
        $stmt = $this->pdo->query('SELECT id, title FROM tasks ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    public function existsTaskInSet(int $taskSetId, int $taskId): bool
    {
        $sql = '
            SELECT COUNT(*) 
            FROM task_set_items
            WHERE task_set_id = :task_set_id
            AND task_id = :task_id
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':task_set_id' => $taskSetId,
            ':task_id' => $taskId,
        ]);

        return (int)$stmt->fetchColumn() > 0;
    }

    public function existsOrderInSet(int $taskSetId, int $orderNumber): bool
    {
        $sql = '
            SELECT COUNT(*)
            FROM task_set_items
            WHERE task_set_id = :task_set_id
            AND order_number = :order_number
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':task_set_id' => $taskSetId,
            ':order_number' => $orderNumber,
        ]);

        return (int)$stmt->fetchColumn() > 0;
    }

    public function getAll(): array
    {
        $sql = '
            SELECT
                ts.id,
                ts.name,
                ts.description,
                ts.execution_time_minutes,
                ts.created_at,
                u.full_name AS author_name
            FROM task_sets ts
            JOIN users u ON ts.created_by = u.id
            ORDER BY ts.id DESC
        ';

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false
    {
        $sql = '
            SELECT
                ts.id,
                ts.name,
                ts.description,
                ts.execution_time_minutes,
                ts.created_at,
                u.full_name AS author_name
            FROM task_sets ts
            JOIN users u ON ts.created_by = u.id
            WHERE ts.id = :id
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }

    public function getItemsBySetId(int $taskSetId): array
    {
        $sql = '
            SELECT
                tsi.id,
                tsi.order_number,
                tsi.max_score,
                t.id AS task_id,
                t.title,
                tt.name AS task_type_name,
                d.name AS discipline_name
            FROM task_set_items tsi
            JOIN tasks t ON tsi.task_id = t.id
            JOIN task_types tt ON t.task_type_id = tt.id
            JOIN disciplines d ON t.discipline_id = d.id
            WHERE tsi.task_set_id = :task_set_id
            ORDER BY tsi.order_number ASC, tsi.id ASC
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':task_set_id' => $taskSetId]);

        return $stmt->fetchAll();
    }

    public function removeItem(int $itemId): bool
    {
        $sql = 'DELETE FROM task_set_items WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([':id' => $itemId]);
    }

    public function update(int $id, array $data): bool
    {
        $sql = '
            UPDATE task_sets
            SET
                name = :name,
                description = :description,
                execution_time_minutes = :execution_time_minutes
            WHERE id = :id
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'],
            ':description' => $data['description'],
            ':execution_time_minutes' => $data['execution_time_minutes'],
        ]);

        return $stmt->rowCount() > 0;
    }

    public function updateItem(int $itemId, int $orderNumber, float $maxScore): bool
    {
        $sql = '
            UPDATE task_set_items
            SET
                order_number = :order_number,
                max_score = :max_score
            WHERE id = :id
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id' => $itemId,
            ':order_number' => $orderNumber,
            ':max_score' => $maxScore,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function existsOrderInSetExceptItem(int $taskSetId, int $orderNumber, int $itemId): bool
    {
        $sql = '
            SELECT COUNT(*)
            FROM task_set_items
            WHERE task_set_id = :task_set_id
            AND order_number = :order_number
            AND id <> :id
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':task_set_id' => $taskSetId,
            ':order_number' => $orderNumber,
            ':id' => $itemId,
        ]);

        return (int)$stmt->fetchColumn() > 0;
    }

    public function getItemById(int $itemId): array|false
    {
        $sql = '
            SELECT
                id,
                task_set_id,
                task_id,
                order_number,
                max_score
            FROM task_set_items
            WHERE id = :id
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $itemId]);

        return $stmt->fetch();
    }

    public function delete(int $id): bool
    {
        $sql = 'DELETE FROM task_sets WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->rowCount() > 0;
    }
}