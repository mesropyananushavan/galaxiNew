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
- The access baseline now also tracks the first live `roles-permissions` route trio as explicit guardrail entries: `admin.roles-permissions.index`, `admin.roles-permissions.store`, and `admin.roles-permissions.update`.
- The shared `roles-permissions` review route requires both `RolePolicy::viewAny` and `PermissionPolicy::viewAny`, so access-shell review and permission-vocabulary review stay under the same explicit Phase 1 guardrail.
- The create and update routes keep the first live access-shell write entry points behind the same bootstrap-only role creation and update guards already enforced in `routes/admin.php`.

## Current posture
- Authorization is present in code today, but this document and `config/phase-1-access-baseline.php` keep the baseline visible while richer roles, permission matrices, and stricter shop scoping are still landing.
- Keep this document, the config baseline, and the dashboard access card aligned when Phase 1 access coverage changes.
