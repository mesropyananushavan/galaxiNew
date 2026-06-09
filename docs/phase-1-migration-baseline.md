# Phase 1 Migration Baseline

## Purpose
Keep the first Galaxy schema anchors explicit while Phase 1 turns starter-era tables into a branch, access, holder, and card foundation.

## Source of truth
- readable summary anchor: `docs/phase-1-migration-baseline.md`
- implementation baseline anchor: `config/phase-1-migration-baseline.php`
- migration directory anchor: `database/migrations`
- visible runtime surface: `resources/views/admin/dashboard.blade.php`

## Current baseline
- The first access and card-domain migrations already exist in `database/migrations`.
- Follow-up Phase 1 migrations already add review, activation, rollout, access-note, assignment-note, and issuance anchors.
- This baseline keeps those schema checkpoints readable as one explicit Galaxy migration seam instead of leaving them implicit inside the migration directory.

## Current posture
- The initial schema layer is already present in code, but this document and `config/phase-1-migration-baseline.php` keep the migration anchors visible while richer writes and later parity behavior are still landing.
- Keep this document, the config baseline, and the dashboard migration-baseline card aligned when Phase 1 schema coverage changes.
