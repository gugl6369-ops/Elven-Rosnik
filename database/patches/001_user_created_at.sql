-- Patch: account creation timestamp on app."user"
-- Apply manually against elfi_ros (schema app). Idempotent.
-- See docs/user-model-vs-db.md in Project store.

SET search_path TO app, public;

ALTER TABLE app."user"
    ADD COLUMN IF NOT EXISTS created_at TIMESTAMPTZ NOT NULL DEFAULT NOW();

COMMENT ON COLUMN app."user".created_at IS 'Account registration time (no updated_at on this table).';