ALTER TABLE users ADD COLUMN IF NOT EXISTS password_hash VARCHAR(255);
ALTER TABLE users ADD COLUMN IF NOT EXISTS status VARCHAR(20) NOT NULL DEFAULT 'active';

UPDATE users
SET password_hash = '$2y$10$VcLO8mS6TV3dv87gS4XQlOLBIik7V6PjClVYrx5tai27LdO1LuFiS',
    status = 'active'
WHERE email = 'admin@spap.local';

UPDATE users
SET password_hash = '$2y$10$NHPyEAEuKonsAijbIOXw0erBKAJl50znr/3A6uvvIZ1deV7ksCGkq',
    status = 'active'
WHERE email = 'operator@spap.local';
