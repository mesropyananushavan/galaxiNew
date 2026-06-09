# Galaxi Foundation

`galaxiNew` is the Galaxy foundation target for the migration.

The current goal is Phase 1: turn this repo from scaffold defaults into a Galaxy-specific application foundation with visible admin information architecture, first live domain entities, and parity-first operational workflows.

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

Focus: keep the current Galaxy admin map, shell layering, checkpoint trail, and seam-source baseline close while Phase 1 slices are still moving.

Guide: `README.md`, `docs/blueprint.md`, and `docs/phase-1-plan.md` remain the readable anchors for this top-level Phase 1 reference trail.

Source of truth: `README.md`, `docs/blueprint.md`, `docs/phase-1-plan.md`, `docs/phase-1-access-baseline.md`, `config/phase-1-access-baseline.php`, `docs/phase-1-shop-access-baseline.md`, `config/phase-1-shop-access-baseline.php`, `docs/phase-1-model-skeletons.md`, `config/phase-1-model-skeletons.php`, `docs/phase-1-migration-baseline.md`, `config/phase-1-migration-baseline.php` for the readable and implementation anchors around the newer Phase 1 baselines, plus `config/phase-1-reference-docs.php` for the broader admin reference inventory.

Posture: admin reference inventory stays explicit across the live Galaxy dashboard trail.

- `docs/blueprint.md`
- `docs/phase-1-plan.md`
- `docs/phase-1-domain-map.md`
- `docs/phase-1-foundation-seams.md`
- `docs/phase-1-access-baseline.md`
- `config/phase-1-access-baseline.php`
- `docs/phase-1-shop-access-baseline.md`
- `config/phase-1-shop-access-baseline.php`
- `docs/phase-1-model-skeletons.md`
- `config/phase-1-model-skeletons.php`
- `docs/phase-1-migration-baseline.md`
- `config/phase-1-migration-baseline.php`
- `config/landing-foundation.php`
- `config/phase-1-seam-sources.php`
- `docs/admin-information-architecture.md`
- `docs/admin-shell-layering.md`
- `docs/admin-shell-config-map.md`
- `docs/progress-log.md`
- `docs/checkpoints/`

### Phase 1 seam sources

Focus: keep the README-level seam-source inventory visible across repo guidance plus the admin and public Phase 1 entry surfaces.

Guide: `README.md` and `config/phase-1-seam-sources.php` remain the readable and implementation anchors for this seam-source trail.

Source of truth: `README.md` and `config/phase-1-seam-sources.php` remain the readable and implementation anchors for this seam-source trail, including the newer access, shop-scope, model-skeleton, and migration seams now tied into it.

Posture: README-backed seam-source baseline stays explicit across the live Galaxy reference trail.

- `config/phase-1-domain-map.php` keeps the entity baseline aligned with the dashboard
- `config/phase-1-foundation-seams.php` keeps the seam inventory aligned with the dashboard
- `config/phase-1-reference-docs.php` keeps the admin-side Phase 1 reference trail aligned
- `config/phase-1-access-baseline.php` keeps the Phase 1 admin access baseline aligned
- `config/phase-1-shop-access-baseline.php` keeps the Phase 1 branch-boundary baseline aligned
- `config/phase-1-model-skeletons.php` keeps the Phase 1 model skeleton baseline aligned
- `config/phase-1-migration-baseline.php` keeps the Phase 1 schema checkpoint baseline aligned
- `config/landing-docs.php` keeps the public Galaxy migration doc trail aligned
- `config/landing-foundation.php` keeps the public Galaxy landing shell baseline aligned
- `config/phase-1-seam-sources.php` keeps this README-level seam-source inventory aligned, including the newer access, model, migration, and landing-shell baseline seams

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

This repository is not meant to stay polished scaffold. Each small Phase 1 step should make the project feel more like Galaxy's future operational console and less like default framework structure.

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
- bootstrap admins without a shop assignment can access `/admin`
- shop-scoped users can access `/admin` only when their assigned branch is active and they hold at least one permission-bearing role
- shop-scoped access to branch-bound writes still flows through explicit policy and request validation seams

This is still a Phase 1 baseline, but it is no longer a blanket authenticated-user stub.

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

Keep changes small, safe, and migration-oriented. Prefer visible improvements to Galaxy foundation posture over generic cleanup.

## Upstream framework references

- https://laravel.com/docs
- https://laracasts.com
- https://laravel.com/learn

The Galaxy foundation structure should still keep this repo friendly to coding agents, but agent work should stay anchored to the Galaxy migration blueprint rather than scaffold-era tasks.

```bash
composer require laravel/boost --dev
php artisan boost:install
```

Use Boost only when it helps Laravel implementation work inside the migration plan.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
