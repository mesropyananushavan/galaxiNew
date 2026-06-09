# Phase 1 Shop-Scoped Access Baseline

## Purpose
Keep the first Galaxy branch-scoped access rules explicit while Phase 1 turns shop visibility into a real foundation boundary instead of a starter-era assumption.

## Source of truth
- readable summary anchor: `docs/phase-1-shop-access-baseline.md`
- implementation baseline anchor: `config/phase-1-shop-access-baseline.php`
- user access seam: `app/Models/User.php`
- shop policy seam: `app/Policies/ShopPolicy.php`
- gate registration seam: `app/Providers/Concerns/RegistersAdminAccessGates.php`
- visible runtime surface: `resources/views/admin/dashboard.blade.php`

## Current baseline
- Bootstrap admins can access any Galaxy branch through `User::canAccessShop(...)`.
- Scoped admins can access only their assigned active branch.
- Users assigned to paused branches do not gain scoped admin access.
- `ShopPolicy::view()` and `ShopPolicy::update()` currently read through that same branch-access seam.
- The `access-shop` gate keeps the same branch-aware rule available at the framework gate layer.

## Current posture
- Shop scope is already present in code, but this document and `config/phase-1-shop-access-baseline.php` keep the branch boundary visible while richer access matrices and wider scoped write flows are still landing.
- Keep this document, the config baseline, and the dashboard shop-access card aligned when Phase 1 branch scope rules change.
