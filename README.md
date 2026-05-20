# Galaxi Foundation

`galaxiNew` is the Galaxy foundation target for the migration.

The current goal is Phase 1: turn this repo from starter scaffolding into a Galaxy-specific application foundation with visible admin information architecture, first live domain entities, and parity-first operational workflows.

## Migration posture

- `galaxiOld` remains the source of truth for business behavior and operator UX
- `galaxiNew` is the Galaxy-focused Laravel monolith replacing it
- parity first, redesign later
- Blade-first admin shell, thin controllers, domain-oriented backend wiring

## Current Phase 1 focus

- make the admin shell visibly Galaxy-specific
- keep branch, cardholder, card, card type, role, reward, rule, and reporting surfaces aligned to the migration map
- land the first safe Galaxy foundation-backed read and write slices without breaking parity-sensitive workflows
- leave a clear checkpoint trail in docs and Git history

## Core Phase 1 references

- `docs/blueprint.md`
- `docs/phase-1-plan.md`
- `docs/progress-log.md`
- `docs/checkpoints/`

## Local development

```bash
cd /home/openclaw/.openclaw/workspace/repos/galaxiNew
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan test
```

## Why this repo exists

This repository is not meant to stay polished scaffold. Each small Phase 1 step should make the project feel more like Galaxy's future operational console and less like framework scaffolding.

## Laravel note

Laravel is the implementation framework here, but the product shape, workflow language, and admin priorities should follow Galaxy migration needs rather than default starter conventions.

## Admin baseline

A minimal admin shell is available as the first post-foundation vertical slice:

- route group in `routes/admin.php`
- `App\Http\Controllers\Admin\DashboardController`
- shared admin layout and partials in `resources/views/admin`
- placeholder dashboard page at `/admin`
- minimal session-based auth entry at `/login`
- admin route protection via `auth` + `can:access-admin`

### Current access behavior

- guests hitting `/admin` are redirected to `/login`
- authenticated users can access `/admin`
- `access-admin` is currently an explicit stub gate that returns `true` for any authenticated user

This is an intentional baseline step, not a full roles / permissions system yet.

## QA / test bootstrap

Минимальный запуск тестов для локального QA:

```bash
cd /home/openclaw/.openclaw/workspace/repos/galaxiNew
cp .env.example .env
composer install
php artisan key:generate
php artisan test
```

Подробности и текущие блокеры окружения: `docs/qa-test-environment.md`

## Checkpoint hygiene

Для Phase 1 checkpoints код, `docs/progress-log.md` и `shared/PROJECT_STATUS.json` должны обновляться вместе.

Перед commit/push можно быстро проверить sync так:

```bash
./scripts/checkpoint-sync.sh
```

Скрипт валидирует `shared/PROJECT_STATUS.json` и показывает, остались ли `docs/progress-log.md` или `shared/PROJECT_STATUS.json` незакоммиченными.
Если checkpoint-файлы dirty, скрипт завершается с non-zero exit code, чтобы его можно было использовать как простой guard перед commit/push.

## Contributing

Keep changes small, safe, and migration-oriented. Prefer visible improvements to Galaxy foundation posture over generic framework cleanup.

## Upstream framework references

- https://laravel.com/docs
- https://laracasts.com
- https://laravel.com/learn

The Galaxy foundation structure should still keep this repo friendly to coding agents, but agent work should stay anchored to the Galaxy migration blueprint rather than generic starter tasks.

```bash
composer require laravel/boost --dev
php artisan boost:install
```

Use Boost only when it helps Laravel implementation work inside the migration plan.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
