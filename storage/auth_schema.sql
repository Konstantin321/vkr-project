-- Локальная авторизация для разработки и резервного входа.
-- Для кафедрального сервиса этот набор полей можно оставить как локальный профиль пользователя.

ALTER TABLE users
    ADD COLUMN IF NOT EXISTS login VARCHAR(100),
    ADD COLUMN IF NOT EXISTS email VARCHAR(255),
    ADD COLUMN IF NOT EXISTS password_hash VARCHAR(255),
    ADD COLUMN IF NOT EXISTS role VARCHAR(30) NOT NULL DEFAULT 'student';

CREATE UNIQUE INDEX IF NOT EXISTS users_login_unique
    ON users (login)
    WHERE login IS NOT NULL;

CREATE UNIQUE INDEX IF NOT EXISTS users_email_unique
    ON users (email)
    WHERE email IS NOT NULL;

-- Пример настройки тестового преподавателя: login teacher, пароль admin123.
-- После проверки лучше заменить пароль на свой.
UPDATE users
SET login = 'teacher',
    email = 'teacher@example.local',
    password_hash = '$2y$10$JVdYca7QqAomiI8DJVS7TuZe57QIXxrRKusALlehOyx/0408sCbpS',
    role = 'teacher'
WHERE id = 1;

-- Администратор для входа на сайт: login Pavel, пароль BestOfTheBest.
INSERT INTO users (
    full_name,
    login,
    email,
    password_hash,
    role
) VALUES (
    'Pavel',
    'Pavel',
    'pavel@example.local',
    '$2y$10$Xudxa71w3QGVQVjRNHtlDOqPXWaSM3Ml4plHY5UEbhAZNzNeVOEde',
    'admin'
)
ON CONFLICT (login) DO UPDATE
SET full_name = EXCLUDED.full_name,
    email = EXCLUDED.email,
    password_hash = EXCLUDED.password_hash,
    role = EXCLUDED.role;
