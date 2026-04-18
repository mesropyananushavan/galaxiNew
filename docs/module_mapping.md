# Galaxi module mapping

## Rules used here
- **Traceability**: every module links legacy code -> target Laravel area -> data/entities.
- **Parity**: each module records routes, services/actions, validation, auth, known deviations, migration notes.
- Missing information is marked `TBD` explicitly.

---

## M01. Auth and access control
- **Legacy code**: `login.php`, `ajax.php`, `config.php`, `header.php`, `users.php`, `user.php`
- **Laravel target**: `app/Http/Controllers/Auth/*`, `app/Models/User.php`, `routes/web.php` or `routes/admin.php`, policies/gates, role-permission tables
- **Entities / tables**: `users`, `roles`, `permissions`, `role_user`, `permission_role`
- **Routes / endpoints**:
  - legacy: `GET /login.php`, login/logout via `ajax.php`
  - target: `/admin/login`, `/admin/logout`, `/admin/users`, `/admin/users/{user}`
- **Services / actions**:
  - `AuthenticateAdminAction`
  - `LogoutAdminAction`
  - `UpsertUserAction`
  - `ResolveAdminPermissionsAction`
- **Validations**:
  - login: username/password required
  - user upsert: username required, password required on create, shop assignment optional/required by role TBD
- **Auth / permissions**:
  - legacy stores comma-separated capability values per module column on `users`
  - target should normalize to roles + permissions + policies
  - shop-scoped users depend on `users.shop_id`
- **Known deviations**:
  - legacy uses `md5(password)`
  - legacy performs side effects in global include (`config.php`) including `last_activity_date`
- **Migration notes**:
  - preserve mapping from legacy module columns to normalized permissions
  - add import-safe legacy permission snapshot if needed

## M02. Shops
- **Legacy code**: `shops.php`, `config.php`, `index.php`, `checks.php`, `services.php`, `services_group.php`, reports
- **Laravel target**: `ShopController`, `Shop` model, shop policies, media upload handling
- **Entities / tables**: `shops`, referenced by `users`, `clients`, `checks`, `services_group`, `gifts`
- **Routes / endpoints**:
  - legacy: `shops.php`
  - target: `/admin/shops`, CRUD routes
- **Services / actions**:
  - `UpsertShopAction`
  - `EvaluateShopDiscountConfigAction`
- **Validations**:
  - name required
  - blocking flags/rates numeric or boolean
  - logo file/image validation TBD
- **Auth / permissions**:
  - module-level `shops` permission
  - super admin vs shop-scoped visibility
- **Known deviations**:
  - current Laravel foundation only has minimal shop schema
- **Migration notes**:
  - add blocking, block limit, editable discount, percent config, logo fields

## M03. Clients / cardholders
- **Legacy code**: `workers.php`, `profile.php`, `index.php`, `accepted.php`, `removed.php`, `birthdays.php`
- **Laravel target**: `ClientController`, profile tabs, `Client` model or renamed `CardHolder` model
- **Entities / tables**: `clients`, `client_history`, `cards`, `card_type`, `checks`, `clients_services`
- **Routes / endpoints**:
  - legacy: `workers.php`, `profile.php?id={id}`
  - target: `/admin/clients`, `/admin/clients/{client}`
- **Services / actions**:
  - `CreateClientAction`
  - `UpdateClientAction`
  - `DeactivateClientAction`
  - `SendClientActivationMailAction`
- **Validations**:
  - required in legacy add flow: `first_name`, `last_name`, `father_name`, `passport`, `email`, `phone`, `shop_id`, `card_id`, `type_id`
  - unique checks observed: `passport`, `email`
  - phone/birthday formatting existed client-side only
- **Auth / permissions**:
  - legacy module `workers`
  - action-level perms: access/add/edit/delete/attach check
- **Known deviations**:
  - legacy overloads `password` and `status` as activation state markers
  - current Laravel `card_holders` table is far from legacy client schema
- **Migration notes**:
  - decide `clients` vs `card_holders` naming before implementation
  - preserve profile tabs: shopping, job history, card history, services

## M04. Client job history
- **Legacy code**: `profile.php?page=job_history`, `accepted.php`, `removed.php`, `work_history.php`, `ajax.php`
- **Laravel target**: `ClientJobHistoryController`, `ClientHistory` model
- **Entities / tables**: `client_history`, `clients`, `shops`
- **Routes / endpoints**:
  - target: `/admin/clients/{client}/job-history`, `/admin/client-job-history/{history}`
- **Services / actions**:
  - `AddClientJobHistoryAction`
  - `UpdateClientJobHistoryAction`
- **Validations**:
  - `shop_id` or free-text `shop_name` depending on in-group/out-of-group mode
  - `position`, `start_date`, `end_date` optional in legacy, tighten later if needed
- **Auth / permissions**:
  - inherits client edit permissions
- **Known deviations**:
  - zero shop ID represents outside-group employment
- **Migration notes**:
  - model explicit `employment_type` instead of magic `shop_id=0`, if possible without losing parity

## M05. Cards
- **Legacy code**: `card_list.php`, `cards.php`, `busy_cards.php`, `index.php`, `profile.php`, `blocking.php`
- **Laravel target**: `CardController`, `Card` model, card assignment workflow
- **Entities / tables**: `cards`, `clients`, `card_history`, `change_type`
- **Routes / endpoints**:
  - target: `/admin/cards`, `/admin/cards/busy`
- **Services / actions**:
  - `UpsertCardAction`
  - `AssignCardToClientAction`
  - `ReleaseCardAction`
- **Validations**:
  - card number/code required
  - uniqueness of visible card number
  - state transition validation for busy/assigned cards
- **Auth / permissions**:
  - module `card_type` in legacy sidebar also covers card pages
- **Known deviations**:
  - current Laravel cards schema assumes stronger normalized relations than legacy
- **Migration notes**:
  - reconcile `card_id` and `key` semantics with target fields `number`, `status`, `activated_at`

## M06. Card types
- **Legacy code**: `card_type.php`, `history.php`, `change_type.php`, `ajax.php`
- **Laravel target**: `CardTypeController`, `CardType` model
- **Entities / tables**: `card_type`, `cards`, `clients`, `card_history`, `change_type`
- **Routes / endpoints**:
  - target: `/admin/card-types`
- **Services / actions**:
  - `UpsertCardTypeAction`
  - `ResolveCardTypeForSpendAction` (if auto-tier logic is restored)
- **Validations**:
  - type/name required
  - `limitation` numeric
- **Auth / permissions**:
  - legacy module `card_type`
- **Known deviations**:
  - current Laravel `card_types` table lacks legacy `limitation` and maybe other fields
- **Migration notes**:
  - preserve type progression logic referenced in `getCardType()` and commented change flow

## M07. Card history and card change workflow
- **Legacy code**: `history.php`, `profile.php?page=card_history`, `change_type.php`, `ajax.php`
- **Laravel target**: `CardHistoryController`, `CardTypeChangeController`
- **Entities / tables**: `card_history`, `change_type`, `cards`, `card_type`, `clients`
- **Routes / endpoints**:
  - `/admin/card-history`
  - `/admin/card-type-changes`
  - `/admin/clients/{client}/card-change`
- **Services / actions**:
  - `ChangeClientCardAction`
  - `RecordCardHistoryAction`
  - `CompleteCardTypeChangeAction`
- **Validations**:
  - new card must be available
  - new type required
  - comment optional
- **Auth / permissions**:
  - legacy client edit rights + card module access
- **Known deviations**:
  - part of tier-change automation is commented out in legacy
- **Migration notes**:
  - keep both manual change and queued change flows traceable

## M08. Checks / purchases
- **Legacy code**: `profile.php?page=shopping`, `checks.php`, `shopping.php`, `ajax.php`, reports
- **Laravel target**: `CheckController`, `ClientCheckController`, reporting query objects
- **Entities / tables**: `checks`, `clients`, `shops`, `card_type`
- **Routes / endpoints**:
  - `/admin/checks`
  - `/admin/clients/{client}/checks`
  - `DELETE /admin/checks/{check}`
- **Services / actions**:
  - `CreateCheckAction`
  - `ReverseCheckAction`
  - `ComputeDiscountAction`
- **Validations**:
  - `shop_id` required
  - `price` required numeric
  - `count` default 1
  - discount editability depends on shop/user
- **Auth / permissions**:
  - legacy module `workers`, action permission `attach check`
  - shop-scoped visibility applies
- **Known deviations**:
  - discount and total calculations are partly UI-driven in legacy
- **Migration notes**:
  - move all monetary calculations server-side
  - preserve reversible check behavior and deleted-state audit

## M09. Client deactivation / blocking
- **Legacy code**: `blocking.php`, `config.php`, `ajax.php`, `index.php`, `profile.php`
- **Laravel target**: `ClientDeactivationController`, blocking evaluator job/service
- **Entities / tables**: `deactive_cards`, `clients`, `checks`, `shops`
- **Routes / endpoints**:
  - `/admin/client-deactivations`
  - `/admin/clients/{client}/deactivate`
  - `/admin/clients/{client}/activation-mail`
- **Services / actions**:
  - `EvaluateBlockingRulesAction`
  - `DeactivateClientAction`
  - `ReactivateClientAction` or activation mail action
- **Validations**:
  - transition guards only, payload minimal
- **Auth / permissions**:
  - notifications + client edit scope
- **Known deviations**:
  - blocking logic currently runs in shared include on page load
- **Migration notes**:
  - move blocking evaluation out of request bootstrap into scheduled/domain action

## M10. Services, groups, rules, client services
- **Legacy code**: `services.php`, `services_group.php`, `services_rules.php`, `profile.php?page=clients_services`, `config.php`, `ajax.php`
- **Laravel target**: `ServiceController`, `ServiceGroupController`, `ServiceGroupRuleController`, `ClientServiceController`
- **Entities / tables**: `services`, `services_group`, `services_rules`, `clients_services`, `shops`
- **Routes / endpoints**:
  - `/admin/services`
  - `/admin/service-groups`
  - `/admin/service-group-rules`
  - `/admin/clients/{client}/services`
- **Services / actions**:
  - `UpsertServiceAction`
  - `UpsertServiceGroupAction`
  - `UpsertServiceGroupRuleAction`
  - `AttachClientServiceAction`
  - `ResolveAvailableServicesForClientAction`
- **Validations**:
  - service group required where applicable
  - client service requires `service_id`
  - rule guard present in legacy: `group_id !== rule_group_id`
- **Auth / permissions**:
  - legacy module `services`
- **Known deviations**:
  - availability algorithm is embedded in `config.php::getServices()`
- **Migration notes**:
  - extract service compatibility logic into dedicated domain service and tests

## M11. Gifts
- **Legacy code**: `gifts.php`, `print.php`, `report_shop.php`, `report_shops.php`, worker pages
- **Laravel target**: `GiftController`, `GiftPrintController`, report layer
- **Entities / tables**: `gifts`, `gifts_history`, `shops`, `clients`
- **Routes / endpoints**:
  - `/admin/gifts`
  - `/admin/gifts/print`
- **Services / actions**:
  - `UpsertGiftAction`
  - `IssueGiftAction`
  - `PrintGiftAction`
- **Validations**:
  - gift fields TBD
  - issuance quantity/date/eligibility TBD
- **Auth / permissions**:
  - likely report or worker-related permissions, exact legacy mapping TBD
- **Known deviations**:
  - source schema visibility is weak
- **Migration notes**:
  - keep module marked parity-TBD until deeper query extraction is done

## M12. Reports and dashboard
- **Legacy code**: `index.php`, `cardholders.php`, `inactive_cards.php`, `work_history.php`, `the_most_active_clients.php`, `the_most_active_shops.php`, `report_shop.php`, `report_shops.php`, `report_total.php`, `birthdays.php`
- **Laravel target**: `DashboardController`, `ReportController`, reporting query/read model layer
- **Entities / tables**: `clients`, `checks`, `shops`, `cards`, `card_type`, `client_history`, `gifts_history`
- **Routes / endpoints**:
  - `/admin`
  - `/admin/reports/cardholders`
  - `/admin/reports/inactive-cards`
  - `/admin/reports/work-history`
  - `/admin/reports/active-clients`
  - `/admin/reports/active-shops`
  - `/admin/reports/shop`
  - `/admin/reports/shops`
  - `/admin/reports/total`
- **Services / actions**:
  - `BuildDashboardMetricsAction`
  - report-specific query builders
- **Validations**:
  - date/shop filters TBD
- **Auth / permissions**:
  - legacy module `checks` covers reporting menu
  - shop scoping required
- **Known deviations**:
  - multiple legacy pages may collapse into fewer filtered report endpoints
- **Migration notes**:
  - preserve KPI semantics before UX consolidation

## M13. Settings
- **Legacy code**: `settings.php`, `ajax.php`
- **Laravel target**: `SettingsController`, config repository/table
- **Entities / tables**: `settings`
- **Routes / endpoints**:
  - `/admin/settings`
- **Services / actions**:
  - `UpdateSettingsAction`
- **Validations**:
  - mail host/user/pass/port/from fields
  - sensitive data encryption at rest
- **Auth / permissions**:
  - legacy module `settings`
- **Known deviations**:
  - current target repo has config files, not DB-backed business settings parity yet
- **Migration notes**:
  - separate deployment config from admin-editable settings

## M14. Audit logs and notifications
- **Legacy code**: `logs.php`, `logins_history.php`, `notifications_old.php`, `ajax.php`, `config.php`
- **Laravel target**: `AuditLogController`, `LoginHistoryController`, dashboard notification widgets
- **Entities / tables**: `logs`, `logins_history`, `notifications` (TBD), `change_type`, `deactive_cards`
- **Routes / endpoints**:
  - `/admin/audit-logs`
  - `/admin/login-history`
  - `/admin/notifications` (optional/TBD)
- **Services / actions**:
  - `RecordAuditLogAction`
  - `RecordLoginHistoryAction`
  - `BuildAdminNotificationCountersAction`
- **Validations**:
  - mostly internal, no major user input
- **Auth / permissions**:
  - legacy notifications badge depends on activity timestamps and queue deltas
- **Known deviations**:
  - notification model likely replaceable by derived counters
- **Migration notes**:
  - preserve action logging on create/edit/delete/deactivate/change-card flows

---

## Global parity gaps to resolve before implementation
1. Final domain naming: `clients` vs `card_holders`.
2. Full column-level schema extraction from live DB or dump.
3. Exact gift and login history schemas.
4. Whether reports stay page-specific or become filter-based consolidated screens.
5. Whether file uploads remain AJAX endpoints or migrate to standard Laravel upload forms.
