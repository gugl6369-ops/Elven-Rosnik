-- Patch: Laravel "remember me" token on app."user"
-- Apply manually against elfi_ros (schema app). Idempotent.
-- Fortify/session auth updates this column when remember=true.

SET search_path TO app, public;

ALTER TABLE "user"
    ADD COLUMN IF NOT EXISTS remember_token VARCHAR(100) NULL;