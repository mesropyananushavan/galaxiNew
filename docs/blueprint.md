# Galaxi Migration Blueprint

## Short conclusion
`galaxiNew` should become a Galaxy-focused Laravel monolith that reproduces the critical business behavior and operational UX of `galaxiOld` before introducing any non-essential improvements.

## Target product posture
- `galaxiOld` is the source of truth for business behavior and UX patterns.
- `galaxiNew` is the target implementation in the Galaxy foundation.
- parity first, redesign later.

## Target architecture
- Galaxy-focused Laravel monolith
- Blade-first UI
- Alpine.js for lightweight interactions
- Livewire only if a specific admin workflow clearly benefits from it
- domain/application services for business logic
- thin controllers
- Eloquent models with policies/scopes for access control

## Core domain areas
1. Auth and access
2. Shops and scoped visibility
3. Cardholders / workers / client entity model
4. Cards and card types
5. Checks / purchases / points
6. Gifts
7. Services / groups / rules
8. Reports and histories

## UI parity principles
- preserve dark sidebar + top bar admin shell
- preserve dense operational tables
- preserve filters, date ranges, status toggles
- preserve dashboard as operational console
- keep workflows fast, avoid excessive page hopping
- keep the Phase 1 admin shell layered and config-driven while preview screens stand in for unavailable PHP-backed flows

## Admin shell implementation note
- The current admin navigation map is documented in `docs/admin-information-architecture.md`.
- The current Phase 1 resource-page shell is intentionally split into base, context, preview, workflow, and closing layers.
- See `docs/admin-shell-layering.md` for the current structure and the bridge role of `config/admin-resource-blocks.php`.
- Config ownership for those shell layers and page surfaces is documented in `docs/admin-shell-config-map.md`.
- The current small config-backed and doc-backed foundation seams are summarized in `docs/phase-1-foundation-seams.md`.
- The README-level seam-source inventory now belongs to that same seam map through `config/phase-1-seam-sources.php`, so repo guidance and live entry surfaces keep sharing one explicit Phase 1 seam trail.
- When real Galaxy foundation reads and writes arrive, replace foundation-preview slices without collapsing that layered shell back into one large inline block list.

## Phase 1
### Goal
Create the real application foundation for Galaxy, not just a placeholder baseline.

### Scope
- define target admin information architecture
- define core entity map
- add roles/permissions baseline
- add shop-scoped access baseline
- create skeleton domain models/migrations for:
  - shops
  - roles/permissions linkage
  - cardholders
  - cards
  - card_types
- create admin navigation shell aligned to old structure

### Deliverables
- documented IA/menu map
- documented Phase 1 domain map (`docs/phase-1-domain-map.md` + `config/phase-1-domain-map.php`) with explicit focus on branch, access, holder, card, and tier-aware building blocks, and those same files remain the readable and implementation source-of-truth anchors for the mapped entity inventory
- documented Phase 1 foundation seams (`docs/phase-1-foundation-seams.md`)
- documented seam-source baseline (`config/phase-1-seam-sources.php` + README seam-source trail)
- initial migrations for core entities
- model skeletons
- admin layout updated toward old parity
- baseline authorization structure

## Phase 2
- cardholder and card CRUD
- type change/history flows
- activation/deactivation
- shop management screens

## Phase 3
- checks/purchases
- points accrual logic
- fiscal search

## Phase 4
- gifts
- services and rules

## Phase 5
- reporting and operational analytics

## What to postpone
- over-polished UI refresh
- secondary reports
- advanced settings UI
- complex client-side architecture

## Main risks
- hidden legacy business logic in PHP pages
- ambiguous domain naming in old system
- broken shop-level permissions
- reproducing screens without reproducing logic

## Immediate next move
Start with Phase 1 information architecture + core entity skeleton and commit it as the first implementation-oriented foundation.
