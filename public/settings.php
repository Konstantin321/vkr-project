<?php

require_once __DIR__ . '/../app/auth/Auth.php';
Auth::requireAuth();

$currentUser = Auth::user();
$roleLabels = Auth::roleLabels();

$pageTitle = 'Настройки';
$activePage = 'settings';
require __DIR__ . '/includes/layout_start.php';
?>

    <div class="page-header">
        <div>
            <h1 class="page-title">Настройки</h1>
            <p class="page-subtitle">Профиль текущего пользователя и параметры входа.</p>
        </div>
    </div>

    <div class="card">
        <h2>Профиль</h2>

        <div class="task-detail-row">
            <span class="task-detail-label">Имя</span>
            <div class="task-detail-value"><?= htmlspecialchars($currentUser['full_name'] ?: '—') ?></div>
        </div>

        <div class="task-detail-row">
            <span class="task-detail-label">Логин</span>
            <div class="task-detail-value"><?= htmlspecialchars($currentUser['login'] ?: '—') ?></div>
        </div>

        <div class="task-detail-row">
            <span class="task-detail-label">Email</span>
            <div class="task-detail-value"><?= htmlspecialchars($currentUser['email'] ?: '—') ?></div>
        </div>

        <div class="task-detail-row">
            <span class="task-detail-label">Статус</span>
            <div class="task-detail-value">
                <?= htmlspecialchars($roleLabels[$currentUser['role']] ?? $currentUser['role']) ?>
            </div>
        </div>
    </div>

<?php require __DIR__ . '/includes/layout_end.php'; ?>
