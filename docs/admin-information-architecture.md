# Admin Information Architecture

Related docs:
- `docs/phase-1-plan.md`
- `docs/blueprint.md`
- `docs/admin-shell-layering.md`
- `docs/admin-shell-config-map.md`
- `config/admin-navigation.php`
- `routes/admin.php`

## Why this exists
Phase 1 calls for a documented admin navigation map and a visible Galaxy-specific information architecture.
This note captures the current sidebar groups, route targets, and the live-vs-preview posture behind each admin surface.

## Current sidebar groups

### Operations
| Surface | Route | Current posture |
| --- | --- | --- |
| Dashboard | `admin.dashboard` | Live dashboard shell and migration map |
| Cardholders | `admin.cardholders.index` | Live catalog reads plus live create/update form flow |
| Cards | `admin.cards.index` | Live catalog reads plus live create/update form flow |
| Checks & Points | `admin.checks-points.index` | Foundation-preview review shell |

### Catalog
| Surface | Route | Current posture |
| --- | --- | --- |
| Card Types | `admin.card-types.index` | Live catalog reads plus live create/update/toggle flow |
| Services & Rules | `admin.services-rules.index` | Foundation-preview review shell |
| Gifts | `admin.gifts.index` | Foundation-preview review shell |

### Administration
| Surface | Route | Current posture |
| --- | --- | --- |
| Shops | `admin.shops.index` | Live catalog reads plus live create/update flow |
| Roles & Permissions | `admin.roles-permissions.index` | Live catalog reads plus live create/update flow |
| Reports | `admin.reports.index` | Foundation-preview review shell |

## Current write-enabled admin routes
These are the current Phase 1 surfaces with real Laravel-backed mutation entry points.

- `POST /admin/shops`
- `PATCH /admin/shops/{shop}`
- `POST /admin/cardholders`
- `PATCH /admin/cardholders/{cardholder}`
- `POST /admin/cards`
- `PATCH /admin/cards/{card}`
- `POST /admin/card-types`
- `PATCH /admin/card-types/{cardType}`
- `PATCH /admin/card-types/{cardType}/toggle-status`
- `POST /admin/roles-permissions`
- `PATCH /admin/roles-permissions/{role}`

## Current preview-only admin routes
These surfaces are intentionally still config-backed review shells in Phase 1.

- `GET /admin/checks-points`
- `GET /admin/services-rules`
- `GET /admin/gifts`
- `GET /admin/reports`

## Authorization posture
- Every admin route still passes through `auth` plus `can:access-admin`.
- Live management catalogs with real data also use explicit policy middleware such as `can:viewAny`, `can:create`, and `can:update`.
- `Shops`, `Roles & Permissions`, and `Card Types` keep bootstrap-only mutation posture in Phase 1.
- `Cards` and `Cardholders` allow broader admin entry, but still enforce shop-aware policy and request validation seams.

## Rule of thumb for next slices
- If a surface already has a live create or update route, prefer the next thin behavior-safe improvement there.
- If a surface is still preview-only, preserve the layered shell and replace one review block at a time when real Galaxy foundation reads or writes are ready.
- Keep this map in sync with `config/admin-navigation.php` and `routes/admin.php` whenever a new admin surface or mutation entry point lands.
