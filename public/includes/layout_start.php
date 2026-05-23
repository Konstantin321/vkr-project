<?php
require_once __DIR__ . '/../../app/auth/Auth.php';

Auth::requireAuth();

$pageTitle = $pageTitle ?? 'Фонд оценочных средств';
$activePage = $activePage ?? '';
$currentUser = Auth::user();
$displayName = $currentUser ? ($currentUser['full_name'] ?: $currentUser['login']) : '';
$roleLabel = $currentUser ? Auth::roleLabel($currentUser['role']) : '';
$avatarLetter = $displayName !== '' ? mb_strtoupper(mb_substr($displayName, 0, 1)) : 'П';

$navigationItems = [
    'tasks' => ['label' => 'Задания', 'href' => 'tasks_list.php', 'icon' => '☰', 'roles' => ['teacher']],
    'sets' => ['label' => 'Наборы заданий', 'href' => 'task_sets_list.php', 'icon' => '▦', 'roles' => ['teacher']],
    'control' => ['label' => 'Запуск контроля', 'href' => 'start_attempt.php', 'icon' => '▶', 'roles' => ['student']],
    'attempts' => ['label' => 'Попытки', 'href' => 'attempts_list.php', 'icon' => '◷', 'roles' => ['student', 'teacher']],
    'results' => ['label' => 'Результаты', 'href' => 'results_list.php', 'icon' => '◎', 'roles' => ['student', 'teacher']],
    'review' => ['label' => 'Проверка ответов', 'href' => 'review_attempts_list.php', 'icon' => '✓', 'roles' => ['teacher']],
    'users' => ['label' => 'Пользователи', 'href' => 'users_list.php', 'icon' => '◉', 'roles' => ['admin']],
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
                <?php if (!Auth::hasRole($item['roles'])): ?>
                    <?php continue; ?>
                <?php endif; ?>
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
                <div class="sidebar-user__profile">
                    <div class="sidebar-user__avatar" aria-hidden="true">
                        <?= htmlspecialchars($avatarLetter) ?>
                    </div>
                    <div class="sidebar-user__meta">
                        <p class="sidebar-user__name" title="<?= htmlspecialchars($displayName) ?>">
                            <?= htmlspecialchars($displayName) ?>
                        </p>
                        <p class="sidebar-user__role"><?= htmlspecialchars($roleLabel) ?></p>
                    </div>
                </div>

                <div class="sidebar-user__actions">
                    <a class="sidebar-user__action <?= $activePage === 'settings' ? 'is-active' : '' ?>" href="settings.php">
                        <span class="sidebar-user__icon" aria-hidden="true">⚙</span>
                        <span>Настройки</span>
                    </a>
                    <a class="sidebar-user__action sidebar-user__action--danger" href="logout.php">
                        <span class="sidebar-user__icon" aria-hidden="true">⇥</span>
                        <span>Выйти</span>
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </aside>

    <main class="app-main">
