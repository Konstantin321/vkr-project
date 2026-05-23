-- Локальная авторизация для разработки и резервного входа.
-- Для кафедрального сервиса этот набор полей можно оставить как локальный профиль пользователя.

ALTER TABLE users
    ADD COLUMN IF NOT EXISTS login VARCHAR(100),
    ADD COLUMN IF NOT EXISTS email VARCHAR(255),
    ADD COLUMN IF NOT EXISTS password_hash VARCHAR(255),
    ADD COLUMN IF NOT EXISTS role VARCHAR(30) NOT NULL DEFAULT 'student';

ALTER TABLE users
    DROP CONSTRAINT IF EXISTS users_role_check;

ALTER TABLE users
    ADD CONSTRAINT users_role_check
    CHECK (role IN ('student', 'teacher', 'admin'));

CREATE UNIQUE INDEX IF NOT EXISTS users_login_unique
    ON users (login)
    WHERE login IS NOT NULL;

CREATE UNIQUE INDEX IF NOT EXISTS users_email_unique
    ON users (email)
    WHERE email IS NOT NULL;

-- Пример настройки тестового преподавателя.
-- Не выполняется автоматически, чтобы в базе не появлялась лишняя тестовая учётная запись.
-- UPDATE users
-- SET login = 'teacher',
--     email = 'teacher@example.local',
--     password_hash = '<hash from password_hash()>',
--     role = 'teacher'
-- WHERE id = 1;

-- Администратор для входа на сайт: login Pavel, пароль BestOfTheBest.
UPDATE users
SET full_name = 'Pavel',
    login = 'Pavel',
    email = 'pavel@example.local',
    password_hash = '$2y$10$Xudxa71w3QGVQVjRNHtlDOqPXWaSM3Ml4plHY5UEbhAZNzNeVOEde',
    role = 'admin'
WHERE login = 'Pavel'
   OR email = 'pavel@example.local';

INSERT INTO users (
    full_name,
    login,
    email,
    password_hash,
    role
)
SELECT
    'Pavel',
    'Pavel',
    'pavel@example.local',
    '$2y$10$Xudxa71w3QGVQVjRNHtlDOqPXWaSM3Ml4plHY5UEbhAZNzNeVOEde',
    'admin'
WHERE NOT EXISTS (
    SELECT 1
    FROM users
    WHERE login = 'Pavel'
       OR email = 'pavel@example.local'
);

-- Test student account: login student, password student123.
UPDATE users
SET full_name = 'Test Student',
    login = 'student',
    email = 'student@example.local',
    password_hash = '$2y$10$M99bTNiq69CPtARCM1Nf7.dsTiOb9V2Eh12.0Umd3rhFagT.F73dO',
    role = 'student'
WHERE login = 'student'
   OR email = 'student@example.local';

INSERT INTO users (
    full_name,
    login,
    email,
    password_hash,
    role
)
SELECT
    'Test Student',
    'student',
    'student@example.local',
    '$2y$10$M99bTNiq69CPtARCM1Nf7.dsTiOb9V2Eh12.0Umd3rhFagT.F73dO',
    'student'
WHERE NOT EXISTS (
    SELECT 1
    FROM users
    WHERE login = 'student'
       OR email = 'student@example.local'
);
