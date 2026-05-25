# Phase 1 Domain Map

## Purpose
Keep the first Galaxy foundation entities explicit while Phase 1 replaces scaffold defaults with branch, access, holder, card, and tier-aware building blocks.

## Source of truth
- human-readable summary: this file
- implementation-facing baseline: `config/phase-1-domain-map.php`
- migration intent: `docs/phase-1-plan.md` and `docs/blueprint.md`

## Initial entity set

### Shop
- table: `shops`
- role in foundation: branch scope, activation state, and operator anchoring

### Role
- table: `roles`
- role in foundation: access shell identity for Galaxy staff flows

### Permission
- table: `permissions`
- role in foundation: permission vocabulary attached to live access shells

### CardHolder
- table: `card_holders`
- role in foundation: branch-aware holder identity and status review

### CardType
- table: `card_types`
- role in foundation: tier catalog identity and active-state baseline

### Card
- table: `cards`
- role in foundation: card shell inventory linked to holders, tiers, and branches

## Phase 1 posture
- this map is intentionally small and stable
- new entities should land only when the migration blueprint clearly pulls them into the current slice
- keep the dashboard summary, this note, and `config/phase-1-domain-map.php` aligned when the entity baseline changes
