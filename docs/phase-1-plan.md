# Phase 1 Plan

## Objective
Turn `galaxiNew` from scaffold defaults into a Galaxy-specific application foundation.

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

## Entity baseline note
- The human-readable Phase 1 entity map lives in `docs/phase-1-domain-map.md`.
- The implementation-facing baseline for that same set lives in `config/phase-1-domain-map.php`.
- Keep those two files aligned with the dashboard entity-map card when the Phase 1 baseline changes.
- See `docs/phase-1-foundation-seams.md` for the broader map of the small config-backed and doc-backed seams now supporting the Galaxy-specific Phase 1 foundation.
- `config/phase-1-seam-sources.php` now carries the README-level seam-source baseline that ties repo guidance back into the admin/public Phase 1 reference trails.

## Definition of done
- core entities exist as Laravel models/migrations
- admin shell reflects target sections
- authorization baseline exists
- project is visibly no longer an empty scaffold

## Current admin shell structure note
- The Phase 1 admin shell is now intentionally layered so parity-first UI work can keep moving while PHP execution is unavailable.
- The current admin navigation map is documented in `docs/admin-information-architecture.md`.
- The current resource-page composition is documented in `docs/admin-shell-layering.md` and split into base, context, preview, workflow, and closing stacks.
- Config ownership for those shell layers and page surfaces is documented in `docs/admin-shell-config-map.md`.
- The current small config-backed and doc-backed foundation seams are summarized in `docs/phase-1-foundation-seams.md`.
- The README-level seam-source inventory is now part of that same seam map through `config/phase-1-seam-sources.php`, so repo guidance and the live admin/public entry surfaces stay aligned through one explicit Phase 1 seam trail.
- High-value Galaxy-specific management pages such as `card-types`, `services-rules`, `gifts`, and `roles-permissions` now use the richer workflow layer so the shell is visibly less scaffold-like before real Galaxy foundation writes exist.
- That layering should stay config-driven even when the first real Galaxy foundation reads or form flows begin replacing foundation-preview blocks.
