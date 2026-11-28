# elfi_ros

Веб-приложение на Laravel, Vue и Inertia. Для работы нужны PHP 8.3+, Composer, Node.js с npm и PostgreSQL.

## Установка

Из корня проекта выполните:

```bash
composer run setup
```

Команда установит PHP- и JS-зависимости, создаст `.env`, сгенерирует `APP_KEY` и соберёт frontend.

В `.env` укажите параметры PostgreSQL: `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`. По умолчанию используется база `elfi_ros`, схема `app`.

Создайте базу, загрузите основную схему и примените SQL-патчи по порядку:

```bash
createdb -h 127.0.0.1 -U postgres elfi_ros
psql -h 127.0.0.1 -U postgres -d elfi_ros -f database/database.sql
for patch in database/patches/00*.sql; do
    psql -h 127.0.0.1 -U postgres -d elfi_ros -f "$patch"
done
```

Команды предполагают PostgreSQL-пользователя `postgres`; измените `-U` и параметры в `.env`, если у вас другие настройки. Скрипты из `database/patches` предназначены для локальной/тестовой базы. Обычный `php artisan migrate` для схемы проекта не используется.

## Запуск

Запустите Laravel и Vite в двух терминалах из корня проекта:

```bash
php artisan serve
```

```bash
npm run dev
```

Откройте адрес, показанный командой `php artisan serve` (обычно `http://127.0.0.1:8000`).

Для production-сборки frontend выполните:

```bash
npm run build
```

## Администратор и демо-данные

Чтобы создать администратора, задайте в `.env` `ADMIN_USERNAME`, `ADMIN_EMAIL` и `ADMIN_PASSWORD`, затем выполните:

```bash
php artisan elfi:ensure-admin
```

Для загрузки демонстрационных растений, рецептов и историй:

```bash
php artisan db:seed
```

Сидеру историй нужен хотя бы один пользователь. Создайте администратора или зарегистрируйте пользователя до запуска сидера.
