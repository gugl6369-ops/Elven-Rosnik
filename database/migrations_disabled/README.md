# Disabled starter-kit migrations

These files came with Laravel Vue Starter Kit. They are **not** loaded by
`php artisan migrate` (only `database/migrations/` is).

## Why disabled

Live DB `elfi_ros` already has the product schema in Postgres schema `app`
(source of truth: `database/database.sql`):

| app schema | starter migration conflict |
|------------|----------------------------|
| `user` (username, pole_id, …) | `create_users_table` → `users` + sessions + password_reset_tokens |
| (none for Laravel cache/jobs) | cache / jobs tables would land in `app` via `search_path` |
| (none) | passkeys + 2FA columns assume starter `users` |

Running them would pollute `app` with foreign tables or fight future Eloquent
models mapped to `app.user`.

## What to do instead

1. Apply / evolve domain DDL via `database/database.sql` (or deliberate SQL).
2. When you need Laravel-only tables (sessions, cache, jobs), either switch
   drivers to `file`/`sync` (see `.env.example`) or write **new** migrations
   that create them in `public` explicitly.
3. Do **not** move these files back into `database/migrations/` without a plan.
