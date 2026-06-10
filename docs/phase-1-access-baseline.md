# Phase 1 Access Baseline

## Purpose
Keep the first Galaxy authorization gates and policy mappings explicit while Phase 1 moves admin access and shop scope away from starter-era assumptions.

## Source of truth
- readable summary anchor: `docs/phase-1-access-baseline.md`
- implementation baseline anchor: `config/phase-1-access-baseline.php`
- gate registration: `app/Providers/Concerns/RegistersAdminAccessGates.php`
- policy registration: `app/Providers/Concerns/RegistersAdminPolicies.php`
- permission policy seam: `app/Policies/PermissionPolicy.php`
- admin route enforcement: `routes/admin.php`
- visible runtime surface: `resources/views/admin/dashboard.blade.php`

## Current baseline
- `access-admin` keeps the Phase 1 admin workspace behind authenticated Galaxy staff access.
- `access-shop` keeps shop-scoped access explicit for branch-aware review and later writes.
- The current mapped policies cover `Shop`, `CardHolder`, `Card`, `Role`, `Permission`, and `CardType`.
- `routes/admin.php` applies the `auth` and `can:access-admin` guardrail before policy-specific resource routes run.
- The access baseline now also tracks five first-live Phase 1 admin route trios as explicit policy-backed guardrail entries plus the dedicated `admin.card-types.toggle-status` action route: `admin.shops.index` / `store` / `update`, `admin.cardholders.index` / `store` / `update`, `admin.cards.index` / `store` / `update`, `admin.card-types.index` / `store` / `update`, and `admin.roles-permissions.index` / `store` / `update`.
- The same baseline now also tracks the live `admin.checks-points.index`, `admin.services-rules.index`, `admin.gifts.index`, and `admin.reports.index` shell routes under the shared `auth` + `can:access-admin` barrier, so the dashboard reflects both policy-backed resource lanes and the still-shell-guarded Galaxy operational lanes.
- The dashboard access card resolves those guardrails through Laravel's router so the live baseline shows the current HTTP method and URI contract, not only route names.
- That dashboard card now carries controller-shaped intro notes and metrics for the tracked gates, grouped route lanes, and mapped policies, including an explicit split between policy-backed and shared-shell route guardrails, while still grouping the live guardrails by workflow family and keeping those families in a controller-owned branch → holder → card → tier → access-shell → receipt → rules → rewards → reporting order so the access map stays stable and readable as it grows.
- The shop routes keep branch-catalog review and first-write entry points behind the same `ShopPolicy` read/create/update checks already enforced in `routes/admin.php`.
- The cardholder routes keep holder-catalog review and first-write entry points behind the same `CardHolderPolicy` read/create/update checks already enforced in `routes/admin.php`.
- The card routes keep card-catalog review and first-write entry points behind the same `CardPolicy` read/create/update checks already enforced in `routes/admin.php`.
- The card-type routes keep tier-catalog review, first-write entry points, and the dedicated status-toggle action behind the same `CardTypePolicy` read/create/update checks already enforced in `routes/admin.php`.
- The shared `roles-permissions` review route requires both `RolePolicy::viewAny` and `PermissionPolicy::viewAny`, so access-shell review and permission-vocabulary review stay under the same explicit Phase 1 guardrail.
- The access-shell create and update routes keep the first live access-shell write entry points behind the same bootstrap-only role creation and update guards already enforced in `routes/admin.php`.

## Current posture
- Authorization is present in code today, but this document and `config/phase-1-access-baseline.php` keep the baseline visible while richer roles, permission matrices, and stricter shop scoping are still landing.
- Keep this document, the config baseline, and the dashboard access card aligned when Phase 1 access coverage changes.
