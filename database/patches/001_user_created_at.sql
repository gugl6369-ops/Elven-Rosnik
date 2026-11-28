SET search_path TO app, public;

ALTER TABLE app."user"
    ADD COLUMN IF NOT EXISTS created_at TIMESTAMPTZ NOT NULL DEFAULT NOW();

COMMENT ON COLUMN app."user".created_at IS 'Account registration time (no updated_at on this table).';
