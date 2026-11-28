SET search_path TO app, public;

ALTER TABLE "user"
    ADD COLUMN IF NOT EXISTS is_blocked BOOLEAN NOT NULL DEFAULT FALSE;

CREATE INDEX IF NOT EXISTS idx_user_is_blocked ON "user"(is_blocked) WHERE is_blocked = true;
