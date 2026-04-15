# Galaxi Migration Blueprint

## Short conclusion
`galaxiNew` should become a simple Laravel monolith that reproduces the critical business behavior and operational UX of `galaxiOld` before introducing any non-essential improvements.

## Target product posture
- `galaxiOld` is the source of truth for business behavior and UX patterns.
- `galaxiNew` is the target implementation on Laravel.
- parity first, redesign later.

## Target architecture
- Laravel monolith
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
- The current Phase 1 resource-page shell is intentionally split into base, context, preview, workflow, and closing layers.
- See `docs/admin-shell-layering.md` for the current structure and the bridge role of `config/admin-resource-blocks.php`.
- When real Laravel reads and writes arrive, replace preview-only slices without collapsing that layered shell back into one large inline block list.

## Phase 1
### Goal
Create the real application foundation for Galaxy, not just generic Laravel baseline.

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
