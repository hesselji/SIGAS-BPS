ALTER TABLE users
    ADD COLUMN deleted_at DATETIME NULL AFTER is_active,
    ADD INDEX idx_users_deleted_at (deleted_at);