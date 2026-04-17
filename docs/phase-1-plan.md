# Phase 1 Plan

## Objective
Turn `galaxiNew` from generic Laravel starter into Galaxy-specific application foundation.

## Work items
1. Define admin navigation map based on `galaxiOld`
2. Define initial domain entity map
3. Add authorization baseline
4. Add shop-scoped access baseline
5. Add first migrations and model skeletons
6. Align admin shell structure toward old UI map

## Initial entity set
- Shop
- Role
- Permission
- CardHolder
- CardType
- Card

## Definition of done
- core entities exist as Laravel models/migrations
- admin shell reflects target sections
- authorization baseline exists
- project is visibly no longer an empty starter

## Current admin shell structure note
- The Phase 1 admin shell is now intentionally layered so parity-first UI work can keep moving while PHP execution is unavailable.
- The current resource-page composition is documented in `docs/admin-shell-layering.md` and split into base, context, preview, workflow, and closing stacks.
- High-value Galaxy-specific management pages such as `card-types`, `services-rules`, `gifts`, and `roles-permissions` now use the richer workflow layer so the shell is visibly less starter-like before real Laravel writes exist.
- That layering should stay config-driven even when the first real Laravel reads or form flows begin replacing preview-only blocks.
