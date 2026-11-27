-- Patch: user moderation flag is_blocked (schema app)
-- Apply manually against elfi_ros. Idempotent.
-- See docs/backend-product-spec-and-guide.md §4.

SET search_path TO app, public;

ALTER TABLE "user"
    ADD COLUMN IF NOT EXISTS is_blocked BOOLEAN NOT NULL DEFAULT FALSE;

CREATE INDEX IF NOT EXISTS idx_user_is_blocked ON "user"(is_blocked) WHERE is_blocked = true;