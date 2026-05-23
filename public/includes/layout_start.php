<?php
require_once __DIR__ . '/../../app/auth/Auth.php';

Auth::requireAuth();

$pageTitle = $pageTitle ?? 'Фонд оценочных средств';
$activePage = $activePage ?? '';
$currentUser = Auth::user();

$navigationItems = [
    'tasks' => ['label' => 'Задания', 'href' => 'tasks_list.php', 'icon' => '☰'],
    'sets' => ['label' => 'Наборы заданий', 'href' => 'task_sets_list.php', 'icon' => '▦'],
    'control' => ['label' => 'Запуск контроля', 'href' => 'start_attempt.php', 'icon' => '▶'],
    'attempts' => ['label' => 'Попытки', 'href' => 'attempts_list.php', 'icon' => '◷'],
    'results' => ['label' => 'Результаты', 'href' => 'results_list.php', 'icon' => '◎'],
    'review' => ['label' => 'Проверка ответов', 'href' => 'review_attempts_list.php', 'icon' => '✓'],
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
<div class="app-layout">
    <aside class="app-sidebar">
        <div class="sidebar-brand">
            <p class="sidebar-brand__eyebrow">ВКР</p>
            <p class="sidebar-brand__title">Фонд оценочных средств</p>
        </div>

        <nav class="sidebar-nav" aria-label="Основная навигация">
            <p class="sidebar-nav__label">Навигация</p>
            <?php foreach ($navigationItems as $key => $item): ?>
                <a
                    class="sidebar-nav__link <?= $activePage === $key ? 'is-active' : '' ?>"
                    href="<?= htmlspecialchars($item['href']) ?>"
                >
                    <span class="sidebar-nav__icon" aria-hidden="true"><?= htmlspecialchars($item['icon']) ?></span>
                    <span><?= htmlspecialchars($item['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <?php if ($currentUser): ?>
            <div class="sidebar-user">
                <p class="sidebar-user__name"><?= htmlspecialchars($currentUser['full_name'] ?: $currentUser['login']) ?></p>
                <p class="sidebar-user__role"><?= htmlspecialchars($currentUser['role']) ?></p>
                <a class="sidebar-user__logout" href="logout.php">Выйти</a>
            </div>
        <?php endif; ?>
    </aside>

    <main class="app-main">
