<?php
require_once __DIR__ . '/../../app/auth/Auth.php';

$activePage = $activePage ?? '';
require __DIR__ . '/layout_start.php';
?>

    <div class="page-header">
        <div>
            <h1 class="page-title">Доступ запрещён</h1>
            <p class="page-subtitle">У вашей роли нет прав для открытия этой страницы.</p>
        </div>
    </div>

    <div class="card">
        <h2>Что можно сделать</h2>
        <p class="muted-text">Вернитесь в доступный раздел через левое меню или войдите под другой учётной записью.</p>
    </div>

<?php require __DIR__ . '/layout_end.php'; ?>
