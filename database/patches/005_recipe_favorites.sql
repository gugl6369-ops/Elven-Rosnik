SET search_path TO app, public;

CREATE TABLE IF NOT EXISTS favorite_recipe (
    user_id   BIGINT NOT NULL REFERENCES "user"(id) ON DELETE CASCADE,
    recipe_id BIGINT NOT NULL REFERENCES recipe(id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, recipe_id)
);

CREATE INDEX IF NOT EXISTS idx_favorite_recipe_recipe_id
    ON favorite_recipe(recipe_id);
