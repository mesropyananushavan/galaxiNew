# QA test environment

## Цель
Подготовить минимальное локальное окружение, в котором QA может запускать `php artisan test` для `galaxiNew`.

## Текущее состояние
- `phpunit.xml` уже настроен на `APP_ENV=testing`.
- Для тестов уже задан `DB_CONNECTION=sqlite` и `DB_DATABASE=:memory:`.
- `.env.example` уже подходит как стартовая база.
- Текущий блокер на этом хосте: `composer` отсутствует в PATH, поэтому зависимости Laravel не установлены и `vendor/autoload.php` не существует.

## Минимальные требования
1. PHP 8.3+
2. Composer 2.x
3. Доступ к установке зависимостей из `composer.lock`

## Быстрый запуск
```bash
cd /home/openclaw/.openclaw/workspace/repos/galaxiNew
cp .env.example .env
composer install
php artisan key:generate
php artisan test
```

## Почему этого достаточно
- Тесты уже используют in-memory SQLite через `phpunit.xml`, поэтому отдельный PostgreSQL для базового QA-прогона не нужен.
- `SESSION_DRIVER=array`, `CACHE_STORE=array`, `QUEUE_CONNECTION=sync` уже выставлены для тестового режима.
- Для feature/unit smoke и Phase 1 parity-check scaffolding этого достаточно.

## Если нужен полностью чистый прогон
```bash
cd /home/openclaw/.openclaw/workspace/repos/galaxiNew
rm -rf vendor composer-runtime-api
composer install
php artisan test
```

## Блокеры
- Если `composer` не установлен, QA не сможет даже загрузить Laravel bootstrap.
- Если расширение `pdo_sqlite` выключено, тесты на in-memory SQLite не запустятся.

## Следующая проверка после bootstrap
После установки зависимостей проверить:
```bash
php artisan test
```
Если появятся ошибки доменной логики, это уже прикладные дефекты, а не проблема окружения.
