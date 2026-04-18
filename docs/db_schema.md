# Galaxi database schema bootstrap

## Purpose
Baseline schema specification inferred from legacy PHP code. This is a migration planning document, not a guaranteed full production schema dump.

## Existing Laravel foundation tables
Already present in `galaxiNew` migrations:
- `users`
- `shops`
- `roles`
- `permissions`
- `permission_role`
- `role_user`
- `card_holders`
- `card_types`
- `cards`

## Naming parity note
Legacy domain uses `clients`; Laravel foundation currently uses `card_holders`.

**Decision TBD**:
- either rename target `card_holders` to `clients` for parity
- or keep `card_holders` and provide explicit field mapping from legacy `clients`

## Legacy table inventory
Observed in code:
- `users`
- `shops`
- `clients`
- `client_history`
- `cards`
- `card_type`
- `card_history`
- `change_type`
- `checks`
- `deactive_cards`
- `services`
- `services_group`
- `services_rules`
- `clients_services`
- `gifts`
- `gifts_history`
- `settings`
- `logs`
- `logins_history`
- `notifications`
- `categories` (observed, low confidence / TBD)

## Entity map

### 1. users
**Legacy usage**
- files: `ajax.php`, `config.php`, `header.php`, `users.php`, `user.php`, `logs.php`, `profile.php`
- auth lookup by `username` + `md5(password)` + `deleted=0`
- contains module permission columns and `shop_id`
- `last_activity_date` updated on each request in `config.php`

**Target Laravel**
- model: `App\Models\User`
- tables: `users`, `role_user`, `roles`, `permissions`, `permission_role`

**Observed/inferred columns**
- `id`
- `username`
- `password`
- `shop_id`
- `deleted`
- `last_activity_date`
- permission columns: `dashboard`, `shops`, `services`, `workers`, `notifications`, `birthdays`, `checks`, `users`, `card_type`, `settings`
- additional profile fields: TBD

### 2. shops
**Legacy usage**
- files: `shops.php`, `config.php`, `index.php`, `checks.php`, `services*.php`, reports
- used for discount behavior, blocking thresholds, logo display, user scoping

**Observed/inferred columns**
- `id`
- `name`
- `logo`
- `deleted`
- `blocking`
- `block_limit`
- `editable_discount`
- `percent` (JSON-like discount mapping inferred from `getDiscountService`)
- additional metadata: TBD

**Laravel target**
- existing target table `shops`
- likely additions required: discount config, blocking settings, logo/media fields

### 3. clients (target alias: card_holders?)
**Legacy usage**
- files: `workers.php`, `profile.php`, `index.php`, `accepted.php`, `removed.php`, `birthdays.php`, reports
- core CRM entity

**Observed/inferred columns**
- `id`
- `first_name`
- `last_name`
- `father_name`
- `passport`
- `email`
- `sex`
- `phone`
- `phone2`
- `birthday`
- `shop_id`
- `card_id`
- `type_id`
- `photo`
- `cv`
- `password`
- `activation_key`
- `status`
- `deleted`
- `create_date`
- `total_score` (commented/low confidence)
- additional fields: TBD

**Business meaning**
- empty `password` is used as inactive/deactivated marker in several flows
- `status` differentiates deactivated vs not yet activated states

### 4. client_history
**Legacy usage**
- files: `profile.php`, `accepted.php`, `removed.php`, `work_history.php`, `ajax.php`
- employment / affiliation history for a client across shops or outside-group locations

**Observed/inferred columns**
- `id`
- `client_id`
- `shop_id`
- `shop_name` (when outside group)
- `position`
- `start_date`
- `end_date`
- `create_date`
- `deleted` (TBD, not confirmed)

### 5. card_type
**Legacy usage**
- files: `card_type.php`, `ajax.php`, `history.php`, `change_type.php`, reports

**Observed/inferred columns**
- `id`
- `type`
- `limitation`
- `deleted`
- other discount/points fields: TBD

**Target Laravel**
- existing target `card_types` table has only `name`, `slug`, `points_rate`, `is_active`
- parity gap remains large

### 6. cards
**Legacy usage**
- files: `card_list.php`, `cards.php`, `index.php`, `profile.php`, `blocking.php`, `busy_cards.php`

**Observed/inferred columns**
- `id`
- `card_id` (human-visible card code)
- `key` (QR / activation search key)
- `busy`
- `deleted`
- `create_date`
- other lifecycle fields: TBD

**Target Laravel**
- existing target `cards` table contains `shop_id`, `card_holder_id`, `card_type_id`, `number`, `status`, `activated_at`
- mapping needed:
  - `card_id` -> `number` or dedicated `external_number`
  - `busy` -> derive from assignment/state or keep explicit flag
  - legacy card may not actually belong to shop directly, TBD

### 7. card_history
**Legacy usage**
- files: `profile.php`, `history.php`, `cards.php`, `ajax.php`

**Observed/inferred columns**
- `id`
- `card_id`
- `type_id`
- `client_id`
- `comment`
- `create_date`

### 8. change_type
**Legacy usage**
- files: `change_type.php`, `ajax.php`, `config.php`
- notification/work queue around card replacement or tier change

**Observed/inferred columns**
- `id`
- `client_id`
- `card_type_id`
- `new_type_id`
- `new_card_id`
- `given`
- `create_date`
- possible comments/status fields: TBD

### 9. checks
**Legacy usage**
- files: `profile.php`, `checks.php`, `shopping.php`, `index.php`, reports, `ajax.php`

**Observed/inferred columns**
- `id`
- `client_id`
- `shop_id`
- `card_type_id` (in forms, exact persistence TBD)
- `price`
- `count`
- `discount`
- `total_price`
- `comment`
- `deleted`
- `create_date`

### 10. deactive_cards
**Legacy usage**
- files: `blocking.php`, `config.php`, `ajax.php`
- stores clients auto-flagged for deactivation/blocking

**Observed/inferred columns**
- `id`
- `client_id`
- `create_date`
- additional reason/status fields: TBD

### 11. services_group
**Legacy usage**
- files: `services_group.php`, `services.php`, `services_rules.php`, `profile.php`

**Observed/inferred columns**
- `id`
- `shop_id`
- `title` or `name` (TBD)
- `deleted`

### 12. services
**Legacy usage**
- files: `services.php`, `profile.php`, `config.php`, `ajax.php`

**Observed/inferred columns**
- `id`
- `services_group_id`
- `title`
- `deleted`
- price/discount-related fields: TBD

### 13. services_rules
**Legacy usage**
- files: `services_rules.php`, `config.php`, `ajax.php`
- prevents incompatible combinations between service groups

**Observed/inferred columns**
- `id`
- `group_id`
- `rule_group_id`
- `deleted` (TBD)

### 14. clients_services
**Legacy usage**
- files: `profile.php`, `config.php`
- attachment of services to a client

**Observed/inferred columns**
- `id`
- `client_id`
- `service_id`
- `discount`
- `pay_day`
- `expire_date`
- `deactivated`
- `deleted`
- `create_date`

### 15. gifts
**Legacy usage**
- files: `gifts.php`, `print.php`, `report_shop.php`, `report_shops.php`

**Observed/inferred columns**
- `id`
- `shop_id`
- title/name/count/status fields: TBD

### 16. gifts_history
**Legacy usage**
- files: `print.php`, reports, worker pages

**Observed/inferred columns**
- `id`
- `gift_id`
- `client_id`
- `shop_id`
- `create_date`
- quantity/comment fields: TBD

### 17. settings
**Legacy usage**
- files: `settings.php`, `ajax.php`
- mail sender configuration used by `sendMail`

**Observed/inferred columns**
- `id`
- `host`
- `username`
- `pass`
- `port`
- `from_email`
- `from_name`
- additional site config: TBD

### 18. logs
**Legacy usage**
- files: `ajax.php`, `logs.php`
- manual audit log of admin actions

**Observed/inferred columns**
- `id`
- `user_id`
- `action`
- `section`
- `create_date`

### 19. logins_history
**Legacy usage**
- file: `logins_history.php`

**Observed/inferred columns**
- exact columns TBD
- likely `user_id`, login timestamp, IP/user-agent or success state

### 20. notifications
**Legacy usage**
- file: `notifications_old.php`
- status unclear, likely legacy/stale

**Observed/inferred columns**
- TBD

## Relationship summary
- `users.shop_id -> shops.id`
- `clients.shop_id -> shops.id` (legacy current shop / registration shop, semantics TBD)
- `clients.card_id -> cards.id`
- `clients.type_id -> card_type.id`
- `client_history.client_id -> clients.id`
- `client_history.shop_id -> shops.id`
- `card_history.client_id -> clients.id`
- `card_history.card_id -> cards.id`
- `card_history.type_id -> card_type.id`
- `change_type.client_id -> clients.id`
- `change_type.card_type_id -> card_type.id`
- `change_type.new_type_id -> card_type.id`
- `change_type.new_card_id -> cards.id`
- `checks.client_id -> clients.id`
- `checks.shop_id -> shops.id`
- `deactive_cards.client_id -> clients.id`
- `services_group.shop_id -> shops.id`
- `services.services_group_id -> services_group.id`
- `services_rules.group_id -> services_group.id`
- `services_rules.rule_group_id -> services_group.id`
- `clients_services.client_id -> clients.id`
- `clients_services.service_id -> services.id`
- `gifts.shop_id -> shops.id`
- `gifts_history.gift_id -> gifts.id`
- `gifts_history.client_id -> clients.id`
- `gifts_history.shop_id -> shops.id`
- `logs.user_id -> users.id`

## Target schema recommendations
1. Add explicit tables missing from current Laravel foundation:
   - `clients` or rename `card_holders`
   - `client_histories`
   - `card_histories`
   - `card_type_changes`
   - `checks`
   - `client_deactivations`
   - `services`, `service_groups`, `service_group_rules`, `client_services`
   - `gifts`, `gift_histories`
   - `settings`
   - `audit_logs`
   - `login_histories`

2. Normalize implicit booleans/statuses:
   - replace `deleted` with `deleted_at` where feasible
   - replace blank password activation semantics with explicit activation fields
   - replace `busy` with assignment/state rules unless legacy import requires it

3. Preserve import fields needed for traceability:
   - legacy IDs
   - original card number/key
   - legacy timestamps
   - source status fields during transition

## Confidence levels
- High confidence: tables and relations observed directly in queries
- Medium confidence: column names visible in forms and action handlers
- Low confidence / TBD: report-only fields, notification internals, category usage, some settings/gifts columns
