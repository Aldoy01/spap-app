ALTER TABLE users ADD COLUMN IF NOT EXISTS password_hash VARCHAR(255);
ALTER TABLE users ADD COLUMN IF NOT EXISTS status VARCHAR(20) NOT NULL DEFAULT 'active';

UPDATE users
SET password_hash = '$2y$10$XjdRzaG9nJAORl4ek5m3LuLXJpCaSW29f3niYRrSH2ObViR8rIqa2',
    status = 'active'
WHERE email = 'admin@spap.local';

UPDATE users
SET password_hash = '$2y$10$CynqGjenPLMJnhh33m.nLepJRQvpo/BuJZr60ZWo5dj5WERsIUXqe',
    status = 'active'
WHERE email = 'operator@spap.local';
