# Phase 1 Model Skeletons

## Purpose
Keep the first Galaxy Eloquent models and migration anchors explicit while Phase 1 turns scaffold data structures into a real branch, access, holder, and card foundation.

## Source of truth
- readable summary anchor: `docs/phase-1-model-skeletons.md`
- implementation baseline anchor: `config/phase-1-model-skeletons.php`
- model directory anchor: `app/Models`
- migration directory anchor: `database/migrations`
- visible runtime surface: `resources/views/admin/dashboard.blade.php`

## Current baseline
- `Shop`, `Role`, `Permission`, `CardHolder`, `CardType`, and `Card` already exist as Galaxy-facing Eloquent models.
- The first access and card-domain migrations already exist and give those models a real Phase 1 schema anchor.
- `Role` and `Permission` now also carry explicit review and access-hand-off note fields in the live Galaxy foundation layer, so access review state is no longer only implied by policy wiring.
- `Shop`, `CardHolder`, and `Card` now also carry explicit review and issuance anchors in the live Galaxy foundation layer, so branch, holder, and inventory review state is no longer only implied by shell presence.
- `CardType` now also carries explicit review, activation, and rollout note fields in the live Galaxy foundation layer, so tier review state is no longer just implied by controller copy.
- Later follow-up migrations already add review and activity fields, but this baseline keeps the first skeleton layer readable as one Phase 1 foundation seam.

## Current posture
- The initial model layer is already present in code, but this document and `config/phase-1-model-skeletons.php` keep the core skeleton visible while richer writes and legacy-parity behavior are still landing.
- Keep this document, the config baseline, and the dashboard model-skeleton card aligned when Phase 1 model or migration coverage changes.
