<?php

class Database
{
    private string $host = 'localhost';
    private string $port = '5432';
    private string $dbname = 'vkr_db';
    private string $username = 'postgres';
    private string $password = 'postgres';

    public function connect(): PDO
    {
        try {
            $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->dbname}";
            $pdo = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            return $pdo;
        } catch (PDOException $e) {
            die('Ошибка подключения к базе данных: ' . $e->getMessage());
        }
    }
}