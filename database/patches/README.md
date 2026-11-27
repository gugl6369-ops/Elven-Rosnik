# SQL patches (schema `app`)

Apply **only against local/dev** `elfi_ros`. Do not run blindly on production.

```bash
# Example (adjust connection per your .env):
psql "$DATABASE_URL" -f database/patches/004_story.sql
```

| File | Purpose |
|------|---------|
| `001_user_created_at.sql` | `created_at` on `app.user` |
| `004_story.sql` | `app.story` table for home feed |

After `004_story.sql`, seed demo data:

```bash
php artisan db:seed --class=PlantCatalogSeeder
php artisan db:seed --class=StoryDemoSeeder
```

`StoryDemoSeeder` requires at least one row in `app.user`. Re-run it when stories expire to refresh `active_until`.