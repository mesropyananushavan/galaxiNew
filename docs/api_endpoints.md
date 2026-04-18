# Galaxi API and route endpoint bootstrap

## Purpose
Map legacy page endpoints and procedural actions to intended Laravel web routes and service actions.

## Route strategy
Legacy application is page-based, not API-first. Target should use:
- Laravel web routes for admin pages
- controller actions for CRUD and workflow transitions
- optional JSON endpoints only where modal/forms benefit from async interaction

## Legacy route inventory
### Auth
- `GET /login.php` - login form
- `POST /ajax.php` with `username`, `password` - authenticate
- `GET /ajax.php?logout=1` - logout

### Dashboard
- `GET /index.php` - dashboard + quick card search + create client modal

### Shops
- `GET /shops.php` - shops CRUD page

### Users
- `GET /users.php` - users list/CRUD
- `GET /user.php` - user detail/edit page (exact behavior TBD)
- `GET /logs.php` - audit logs
- `GET /logins_history.php` - login history

### Clients / cardholders
- `GET /workers.php` - client list
- `GET /profile.php?id={id}&page=shopping|job_history|card_history|clients_services` - client profile and tabs
- `GET /accepted.php` - likely accepted/active clients report
- `GET /removed.php` - likely removed/deactivated clients report
- `GET /birthdays.php` - birthdays view

### Cards
- `GET /card_type.php` - card type CRUD
- `GET /card_list.php` - card list
- `GET /cards.php` - card-linked client/card state view
- `GET /history.php` - card history
- `GET /busy_cards.php` - busy/assigned cards
- `GET /change_type.php` - pending card change requests
- `GET /blocking.php` - deactivation/blocking queue

### Checks and reports
- `GET /checks.php`
- `GET /shopping.php`
- `GET /cardholders.php`
- `GET /inactive_cards.php`
- `GET /work_history.php`
- `GET /the_most_active_clients.php`
- `GET /the_most_active_shops.php`
- `GET /report_shop.php`
- `GET /report_shops.php`
- `GET /report_total.php`

### Services and gifts
- `GET /services.php`
- `GET /services_group.php`
- `GET /services_rules.php`
- `GET /gifts.php`
- `GET /print.php` - gift/report related printable output

### Settings and legacy notifications
- `GET /settings.php`
- `GET /notifications_old.php` - legacy/stale, behavior TBD

### File uploads
- `POST /upload.php` - client photo upload
- `POST /uploadcv.php` - CV upload

## Legacy procedural action endpoint
### `POST /ajax.php`
Observed actions:
- login via `username`, `password`
- `action=add`
- `action=get_edit`
- `action=getDiscount`
- `action=getDiscountService`
- special table `sendActivationMail`
- special table `sendDeactivationMail`
- `action=edit`
- `action=del`
- `action=toggleDeleted`
- `action=changeCard`

Tables/entities addressed via `ajax.php`:
- `clients`
- `client_history`
- `cards`
- `card_type`
- `checks`
- `users`
- `shops`
- `services`
- `services_group`
- `services_rules`
- `clients_services`
- `settings`
- `change_type`
- `logs` (side effect)

## Target Laravel route map

### Auth & session
| Legacy | Target route | Controller / action | Notes |
|---|---|---|---|
| `GET /login.php` | `GET /admin/login` | `Auth\AuthenticatedSessionController@create` | parity required |
| login post to `ajax.php` | `POST /admin/login` | `Auth\AuthenticatedSessionController@store` | replace md5 with Laravel hashing during migration |
| `GET /ajax.php?logout=1` | `POST /admin/logout` | `Auth\AuthenticatedSessionController@destroy` | CSRF-safe |

### Dashboard
| Legacy | Target route | Controller / action | Notes |
|---|---|---|---|
| `GET /index.php` | `GET /admin` | `Admin\DashboardController@index` | include quick card lookup and summary counters |
| quick search by QR/card/key | `GET /admin/search/card` | `Admin\SearchController@card` | legacy behavior redirects to profile when active |

### Shops
| Legacy | Target route | Controller / action | Services |
|---|---|---|---|
| `shops.php` | `GET /admin/shops` | `Admin\ShopController@index` | list/filter |
| add/edit/delete via `ajax.php` | `POST /admin/shops`, `PUT /admin/shops/{shop}`, `DELETE /admin/shops/{shop}` | `ShopController@store/update/destroy` | `UpsertShopAction` |

### Users & access
| Legacy | Target route | Controller / action | Services |
|---|---|---|---|
| `users.php` | `GET /admin/users` | `Admin\UserController@index` | |
| add/edit/delete | `POST/PUT/DELETE /admin/users...` | `UserController@store/update/destroy` | `UpsertUserAction` |
| `user.php` | `GET /admin/users/{user}` | `UserController@show` | exact parity TBD |
| `logs.php` | `GET /admin/audit-logs` | `AuditLogController@index` | |
| `logins_history.php` | `GET /admin/login-history` | `LoginHistoryController@index` | TBD |

### Clients / cardholders
| Legacy | Target route | Controller / action | Services |
|---|---|---|---|
| `workers.php` | `GET /admin/clients` | `ClientController@index` | dense table + filters |
| add client modal on `index.php` / workers | `POST /admin/clients` | `ClientController@store` | `CreateClientAction` |
| `profile.php?id=...` | `GET /admin/clients/{client}` | `ClientController@show` | tabs server-rendered |
| edit client | `PUT /admin/clients/{client}` | `ClientController@update` | `UpdateClientAction` |
| activation mail | `POST /admin/clients/{client}/activation-mail` | `ClientActivationController@send` | `SendClientActivationMailAction` |
| deactivation | `POST /admin/clients/{client}/deactivate` | `ClientActivationController@deactivate` | `DeactivateClientAction` |
| quick edit fetch | `GET /admin/clients/{client}/edit-data` | `ClientController@editData` | only if async modal retained |
| `accepted.php` | `GET /admin/clients/accepted` | `ClientReportController@accepted` | semantics TBD |
| `removed.php` | `GET /admin/clients/removed` | `ClientReportController@removed` | semantics TBD |
| `birthdays.php` | `GET /admin/clients/birthdays` | `ClientReportController@birthdays` | |

### Client histories and card changes
| Legacy | Target route | Controller / action | Services |
|---|---|---|---|
| add/edit `client_history` via profile | `POST /admin/clients/{client}/job-history` | `ClientJobHistoryController@store` | `AddClientJobHistoryAction` |
| edit history row | `PUT /admin/client-job-history/{history}` | `ClientJobHistoryController@update` | |
| `action=changeCard` | `POST /admin/clients/{client}/card-change` | `ClientCardController@change` | `ChangeClientCardAction` |
| `change_type.php` queue | `GET /admin/card-type-changes` | `CardTypeChangeController@index` | pending workflow |

### Cards & card types
| Legacy | Target route | Controller / action | Services |
|---|---|---|---|
| `card_type.php` | `GET /admin/card-types` | `CardTypeController@index` | |
| CRUD | `POST/PUT/DELETE /admin/card-types...` | `CardTypeController@store/update/destroy` | `UpsertCardTypeAction` |
| `card_list.php` | `GET /admin/cards` | `CardController@index` | |
| card CRUD | `POST/PUT/DELETE /admin/cards...` | `CardController@store/update/destroy` | `UpsertCardAction` |
| `history.php` | `GET /admin/card-history` | `CardHistoryController@index` | |
| `busy_cards.php` | `GET /admin/cards/busy` | `CardController@busy` | |
| `blocking.php` | `GET /admin/client-deactivations` | `ClientDeactivationController@index` | queue/list |

### Checks / purchases
| Legacy | Target route | Controller / action | Services |
|---|---|---|---|
| add check from profile | `POST /admin/clients/{client}/checks` | `ClientCheckController@store` | `CreateCheckAction` |
| reverse/delete check | `DELETE /admin/checks/{check}` | `CheckController@destroy` | `ReverseCheckAction` |
| `checks.php` / `shopping.php` | `GET /admin/checks` | `CheckController@index` | |
| report pages | `GET /admin/reports/...` | `ReportController@...` | report-specific read models |

### Services
| Legacy | Target route | Controller / action | Services |
|---|---|---|---|
| `services.php` | `GET /admin/services` | `ServiceController@index` | |
| `services_group.php` | `GET /admin/service-groups` | `ServiceGroupController@index` | |
| `services_rules.php` | `GET /admin/service-group-rules` | `ServiceGroupRuleController@index` | |
| attach client service | `POST /admin/clients/{client}/services` | `ClientServiceController@store` | `AttachClientServiceAction` |
| update client service | `PUT /admin/client-services/{clientService}` | `ClientServiceController@update` | |
| delete client service | `DELETE /admin/client-services/{clientService}` | `ClientServiceController@destroy` | |
| get service discount | `GET /admin/service-discounts/{shop}` or client/service context endpoint | `ServicePricingController@show` | exact API TBD |

### Gifts
| Legacy | Target route | Controller / action | Notes |
|---|---|---|---|
| `gifts.php` | `GET /admin/gifts` | `GiftController@index` | |
| gift workflows | `POST/PUT/DELETE /admin/gifts...` | `GiftController@store/update/destroy` | exact parity TBD |
| printable gift flow | `GET /admin/gifts/print` | `GiftPrintController@show` | map from `print.php` |

### Settings / system
| Legacy | Target route | Controller / action | Notes |
|---|---|---|---|
| `settings.php` | `GET /admin/settings` | `SettingsController@edit` | mail config and site settings |
| update settings | `PUT /admin/settings` | `SettingsController@update` | sensitive fields encrypted |
| notifications old | `GET /admin/notifications` | `NotificationController@index` | may be postponed |

### Uploads
| Legacy | Target route | Controller / action | Notes |
|---|---|---|---|
| `upload.php` | `POST /admin/uploads/client-photo` | `UploadController@clientPhoto` | storage normalization |
| `uploadcv.php` | `POST /admin/uploads/client-cv` | `UploadController@clientCv` | validation required |

## Validation expectations
Use Laravel Form Requests for at least:
- login
- shop create/update
- user create/update
- client create/update
- card/card type create/update
- check create/reverse
- client history create/update
- client service create/update
- settings update
- upload endpoints

## Authorization expectations
Use policies/gates for:
- module visibility parity with legacy sidebar permissions
- shop-scoped reads/writes
- action-level rights: add, edit, delete, attach check, activate/deactivate

## Known TBD
- Some report endpoints may collapse into a smaller number of Laravel routes with filters instead of one-page-per-report.
- `notifications_old.php` may be retired if its behavior is fully covered by dashboard counters and queue views.
- Exact JSON modal endpoints are optional; server-rendered modals remain acceptable if faster for parity.
