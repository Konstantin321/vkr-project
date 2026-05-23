<?php

class User
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByLogin(string $login): array|false
    {
        $sql = '
            SELECT
                id,
                full_name,
                login,
                email,
                password_hash,
                role
            FROM users
            WHERE login = :login
               OR email = :login
            LIMIT 1
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':login' => $login]);

        return $stmt->fetch();
    }

    public function findById(int $id): array|false
    {
        $sql = '
            SELECT
                id,
                full_name,
                login,
                email,
                role
            FROM users
            WHERE id = :id
            LIMIT 1
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }
}
