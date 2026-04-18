# Galaxi migration plan

## Scope
Architect bootstrap only. No implementation changes are introduced here beyond migration documentation and status normalization.

## Source repositories
- Legacy source: `/home/openclaw/.openclaw/workspace/repos/galaxiOld`
- Target source: `/home/openclaw/.openclaw/workspace/repos/galaxiNew`
- Requested paths in task were missing, so analysis used the actual repos under `workspace/repos`.

## Current repo sync snapshot
- `galaxiOld`: branch `main`, clean, tracks `origin/main`
- `galaxiNew`: branch `architect/bootstrap-migration-docs`, based on `role-backend/init-laravel-foundation`
- `git fetch --all --prune` executed for both repos before analysis
- `galaxiNew/shared/PROJECT_STATUS.json` did not exist and is created in this bootstrap

## Architecture objective
Migrate the legacy PHP admin panel to a Laravel monolith with parity-first behavior.

Target stack:
- Laravel monolith
- Blade-first admin UI
- Service classes for domain actions
- Form Requests / policies for validation and authorization
- MySQL as primary database
- Queue/mail only after parity-critical flows are stable

## Migration principles
1. **Parity first**: preserve legacy operational flows before redesign.
2. **Traceability**: every target module must link back to specific legacy files, tables, and workflows.
3. **Explicit gaps**: unknown behavior is marked `TBD`, never implied.
4. **Incremental domain migration**: foundation, then core CRM/card flows, then checks/services/gifts/reports.
5. **Soft-delete awareness**: many legacy tables use `deleted` flags and status toggles.

## Primary modules and migration order
| Order | Module | Legacy anchor | Laravel target | Status |
|---|---|---|---|---|
| 1 | Auth & access control | `login.php`, `ajax.php`, `config.php`, `header.php`, `users.php` | `app/Http/Controllers/Auth/*`, `app/Policies/*`, `users/roles/permissions` | foundation exists, parity TBD |
| 2 | Shops | `shops.php` | `Shop` model, admin CRUD, shop-scoped policies | partial foundation exists |
| 3 | Cardholders / clients | `workers.php`, `profile.php`, `index.php`, `accepted.php`, `removed.php` | `Client` aggregate (`card_holders` rename decision TBD), profile flows, histories | not implemented |
| 4 | Cards & card types | `card_list.php`, `cards.php`, `card_type.php`, `history.php`, `change_type.php`, `blocking.php` | `Card`, `CardType`, `CardHistory`, change/deactivation workflows | partial schema exists |
| 5 | Checks & purchases | `shopping.php`, `checks.php`, profile shopping tab | `Check` domain, reporting queries, reversal actions | not implemented |
| 6 | Services & rules | `services.php`, `services_group.php`, `services_rules.php`, profile services tab | `Service`, `ServiceGroup`, `ServiceRule`, `ClientService` | not implemented |
| 7 | Gifts | `gifts.php`, `print.php`, reports | `Gift`, `GiftHistory` | not implemented |
| 8 | Reports & dashboard | `index.php`, `cardholders.php`, `inactive_cards.php`, `work_history.php`, `the_most_active_*`, `report_*` | read models / reporting controllers | not implemented |
| 9 | Settings, logs, notifications | `settings.php`, `logs.php`, `logins_history.php`, `notifications_old.php` | system settings, audit logs, admin notifications | TBD |

## Cross-cutting architectural decisions
### 1. Naming and parity
- Legacy uses `clients`; current Laravel foundation uses `card_holders`.
- Final naming decision is **TBD**.
- Until resolved, documentation keeps both terms visible for traceability.

### 2. Authorization model
Legacy permissions are stored directly on `users` columns as comma-separated capability values per sidebar area.

Target:
- normalize into roles + permissions tables
- add Laravel policies / gates per module
- preserve shop scoping (`users.shop_id`) and action-level permissions where needed

### 3. State model conversion
Legacy uses mixed state markers:
- `deleted` soft flag
- `busy` card occupancy
- empty `password` meaning not activated/deactivated in some client flows
- `status` mixed client activation marker

Target:
- replace implicit states with explicit enums/booleans where possible
- keep migration mapping documented before data conversion

### 4. Service actions to isolate
At minimum, target service layer should include:
- authenticate admin user
- create/update/deactivate client
- assign/change card
- add/reverse check
- evaluate block/deactivation rules
- attach/detach client service
- send activation/deactivation mail
- record audit log

## Delivery phases
### Phase A. Domain specification
- finalize schema map
- finalize endpoint map
- finalize module mapping
- identify parity blockers

### Phase B. Foundation alignment
- resolve `clients` vs `card_holders`
- finish auth, roles, permissions, shop scoping
- admin navigation shell aligned to legacy sidebar

### Phase C. Core CRM migration
- shops
- users
- clients/cardholders
- cards/card types/history/change flows

### Phase D. Transactional flows
- checks
- deactivation/blocking
- services
- gifts

### Phase E. Reporting and operational polish
- dashboard metrics
- reports
- logins/logs
- settings

## Traceability rule
For every module, implementation work must preserve a three-way link:
1. legacy PHP file(s)
2. legacy table/entity usage
3. Laravel target controller/model/service/request/policy/route

No module is considered ready unless all three are mapped.

## Parity rule
A Laravel module is only parity-ready when all of the following are specified:
- list/filter view
- create/edit/delete or equivalent state transitions
- validation behavior
- authorization behavior
- audit/log side effects
- notifications/mail side effects
- known deviations explicitly listed

## Known blockers / TBD
- No direct DB dump/schema file found in legacy repo, so several column-level definitions remain inferred from code only.
- `PROJECT_STATUS.json` was absent in target repo and is bootstrapped here.
- Some legacy flows exist only in large procedural PHP pages and require deeper query extraction during implementation phase.
- Reports may depend on production data semantics not fully visible from repository code alone.
