<?php

$host = "localhost";
$port = "5432";
$dbname = "vkr_db";
$user = "postgres";
$password = "postgres"; // сюда свой пароль

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    echo "Подключение к БД успешно!";
} catch (PDOException $e) {
    echo "Ошибка подключения: " . $e->getMessage();
}