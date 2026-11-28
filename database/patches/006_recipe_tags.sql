SET search_path TO app, public;

CREATE TABLE IF NOT EXISTS recipe_tag (
    recipe_id BIGINT NOT NULL REFERENCES recipe(id) ON DELETE CASCADE,
    tag_id    BIGINT NOT NULL REFERENCES tag(id) ON DELETE CASCADE,
    PRIMARY KEY (recipe_id, tag_id)
);

CREATE INDEX IF NOT EXISTS idx_recipe_tag_tag_id
    ON recipe_tag(tag_id);
