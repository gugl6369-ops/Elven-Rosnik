SET search_path TO app, public;

ALTER TABLE "user"
    ADD COLUMN IF NOT EXISTS remember_token VARCHAR(100) NULL;
