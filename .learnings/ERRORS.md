# Errors

Command failures and integration errors.

---

## [ERR-20260521-001] focused-tier-test-neighbor-expectation

**Logged**: 2026-05-21T02:21:00Z
**Priority**: medium
**Status**: resolved
**Area**: tests

### Summary
A focused card-types assertion update initially missed a neighboring expectation on the same management snapshot.

### Error
```
Expected response HTML to contain: Active Galaxy tiers
at tests/Feature/AdminDashboardTest.php:9656
```

### Context
- Command attempted: `php artisan test --filter='test_authenticated_user_can_access_card_types_management_preview'`
- A metric label had been updated to `Active-state Galaxy tiers`, but another assertion in the same focused test still expected `Active Galaxy tiers`.
- The failure was narrow and local to the same card-types surface.

### Suggested Fix
When renaming a visible metric label, grep the focused test file for the old string and sync all same-surface expectations before rerunning the slice.

### Metadata
- Reproducible: yes
- Related Files: tests/Feature/AdminDashboardTest.php, app/Http/Controllers/Admin/ResourceIndexController.php, config/admin-pages.php

---
## [ERR-20260428-001] dashboard migration-map test expectation mismatch

**Logged**: 2026-04-28T08:47:00+00:00
**Priority**: medium
**Status**: pending
**Area**: tests

### Summary
A dashboard assertion expected 9 planned surfaces, but `config/admin-navigation.php` actually defines 10 items.

### Error
```
Expected response content to contain: The migration map already spans 3 grouped sections and 9 planned surfaces...
```

### Context
- Operation attempted: targeted dashboard feature test run after adding migration-map handoff copy
- Root cause: hard-coded expectation drifted from config-backed navigation count
- Relevant surface: dashboard migration map summary assertions

### Suggested Fix
Prefer deriving dashboard count expectations from the config-backed navigation map, or verify counts before hard-coding them in new assertions.

### Metadata
- Reproducible: yes
- Related Files: tests/Feature/AdminDashboardTest.php, config/admin-navigation.php

---
## [ERR-20260513-001] cardholders_selected_review_filter

**Logged**: 2026-05-13T03:10:00Z
**Priority**: medium
**Status**: resolved
**Area**: tests

### Summary
Focused cardholders selected-review filter exposed a stale paused-branch expectation in the active-unlinked-holder test.

### Error
```
Focused `php artisan test --filter='test_cardholders_page_supports_selected_active_linked_holder_review_context|test_cardholders_page_supports_selected_active_unlinked_holder_review_context|test_cardholders_page_supports_selected_inactive_linked_holder_review_context'` returned 1 failed, 2 passed. The failing assertion expected paused-branch copy for an active-shop holder fixture.
```

### Context
- Command/operation attempted: focused cardholders selected-review filter after wrapping `cardholdersSelectedReviewActivityDisabledReason()` behind a summary helper.
- The code change preserved behavior.
- The failing test fixture creates `Galaxy Active Unlinked Branch` with `is_active => true`, but still expected paused-branch holder posture/evidence copy.

### Suggested Fix
Align the test expectations with the active-shop fixture semantics instead of paused-branch copy.

### Metadata
- Reproducible: yes
- Related Files: app/Http/Controllers/Admin/ResourceIndexController.php, tests/Feature/AdminDashboardTest.php

### Resolution
- **Resolved**: 2026-05-13T03:12:00Z
- **Commit/PR**: pending
- **Notes**: Updated the active-unlinked-holder expectations to match the active-shop Galaxy review copy.

---
## [ERR-20260513-002] edit_exact_match_mismatch

**Logged**: 2026-05-13T12:10:00Z
**Priority**: low
**Status**: resolved
**Area**: docs

### Summary
A precise `edit` replacement failed because the helper body text no longer matched the assumed casing and output exactly.

### Context
- Operation attempted: rename `cardholdersLaravelStatus()` to a label-named helper during Phase 1 helper cleanup
- Root cause: the replacement block assumed different exact text than the current file contents
- Impact: no repo state damage, just required a quick read-before-edit retry

### Suggested Fix
When repeating narrow helper refactors in this file, re-read the exact local block immediately before an exact-text `edit` call.

### Metadata
- Reproducible: yes
- Related Files: app/Http/Controllers/Admin/ResourceIndexController.php

### Resolution
- **Resolved**: 2026-05-13T12:11:00Z
- **Commit/PR**: n/a
- **Notes**: Re-read the current helper block and continued with an exact-match edit.

---

## [ERR-20260516-001] php-artisan-test-database-seeder

**Logged**: 2026-05-16T21:38:00Z
**Priority**: medium
**Status**: resolved
**Area**: tests

### Summary
New DatabaseSeeder test failed because the seeder called `modelKeys()` on a base support collection.

### Error
```
BadMethodCallException: Method Illuminate\\Support\\Collection::modelKeys does not exist.
```

### Context
- Operation attempted: `php artisan test tests/Feature/DatabaseSeederTest.php tests/Unit/FoundationModelCastsTest.php tests/Unit/CardTypeModelTest.php`
- Root cause: permission records were gathered in a support collection produced by `collect(...)->map(...)`, not an Eloquent collection
- Impact: seeded permission sync failed before the new baseline seeder assertions could complete

### Suggested Fix
Use `pluck('id')->all()` (or wrap the models in an Eloquent collection) before syncing role permissions.

### Metadata
- Reproducible: yes
- Related Files: database/seeders/DatabaseSeeder.php, tests/Feature/DatabaseSeederTest.php

### Resolution
- **Resolved**: 2026-05-16T21:39:00Z
- **Commit/PR**: pending
- **Notes**: Replaced `modelKeys()` with `pluck('id')->all()` and re-ran the focused tests.

---

## [ERR-20260517-001] dashboard_entry_copy_followup_assertion

**Logged**: 2026-05-17T13:27:00Z
**Priority**: low
**Status**: pending
**Area**: tests

### Summary
A focused dashboard copy-alignment change updated the top-level shops entry label, but one supporting dashboard assertion still expected the older explanatory sentence fragment.

### Error
```
Expected response content to contain: start with review live shops.
```

### Context
- Command/operation attempted: focused `php artisan test --filter='test_authenticated_user_can_access_admin_dashboard|test_dashboard_latest_live_work_shortcuts_respect_shop_scope|test_dashboard_latest_live_work_shortcuts_fall_back_to_shared_review_without_assigned_shop|test_authenticated_user_can_access_shops_management_preview'` after renaming the dashboard shops entry label.
- Root cause: the dashboard summary text assertion still referenced the previous top-level entry wording even though the visible action label had changed.
- Relevant surface: admin dashboard entry coverage/focus copy.

### Suggested Fix
When aligning dashboard entry labels, grep for nearby explanatory assertions and update the supporting prose in the same slice before finalizing the test run.

### Metadata
- Reproducible: yes
- Related Files: app/Http/Controllers/Admin/DashboardController.php, tests/Feature/AdminDashboardTest.php

---
## [ERR-20260517-002] shops_surface_metric_label_drift

**Logged**: 2026-05-17T19:58:00Z
**Priority**: medium
**Status**: pending
**Area**: tests

### Summary
A focused shops surface test failed after a summary-copy change because the config-backed metric label still used older generic wording while the assertion already expected Galaxy-specific branch wording.

### Error
```
Expected response content to contain: Active Galaxy branches
```

### Context
- Operation attempted: focused `php artisan test --filter='test_authenticated_user_can_access_shops_operational_index_shape|test_shops_catalog_actions_reflect_saved_branch_readiness|test_shops_page_surfaces_selected_shop_context_from_laravel_data|test_shop_scoped_admin_sees_branch_creation_actions_disabled_in_shops_workspace'`
- Root cause: `config/admin-pages.php` still exposed `Active shops` on the shops surface while the targeted test expected `Active Galaxy branches`
- Impact: no behavior issue, but a real wording pocket remained on the live admin shell

### Suggested Fix
When aligning summary copy on a surface, quickly verify neighboring metric labels on that same surface for older generic wording before rerunning the focused slice.

### Metadata
- Reproducible: yes
- Related Files: config/admin-pages.php, tests/Feature/AdminDashboardTest.php

---
## [ERR-20260518-002] cardholders_linked_cards_metric_assertion_drift

**Logged**: 2026-05-18T01:28:00Z
**Priority**: medium
**Status**: pending
**Area**: tests

### Summary
A focused cardholders metric wording change updated the config-backed label, but one operational index assertion still expected the older `Linked cards` phrasing.

### Error
```
Expected response content to contain: Linked cards
```

### Context
- Operation attempted: focused `php artisan test --filter='test_authenticated_user_can_access_cardholders_operational_index_shape|test_cardholders_page_surfaces_selected_holder_context_from_laravel_data|test_cardholders_page_supports_selected_active_linked_holder_review_context|test_shop_scoped_admin_sees_cardholder_creation_actions_disabled_in_cardholders_workspace'`
- Root cause: `config/admin-pages.php` was updated to `Linked Galaxy card shells`, but the operational index test still asserted the older label
- Impact: no behavior issue, but the same surface had one stale wording assertion left behind

### Suggested Fix
When aligning a metric label on a surface, grep all assertions for that exact label on the same surface before rerunning the focused slice.

### Metadata
- Reproducible: yes
- Related Files: config/admin-pages.php, tests/Feature/AdminDashboardTest.php

---
## [ERR-20260518-001] shops_assigned_manager_metric_assertion_drift

**Logged**: 2026-05-18T00:58:00Z
**Priority**: medium
**Status**: pending
**Area**: tests

### Summary
A focused shops metric wording change updated the config-backed label, but one operational index assertion still expected the older `Assigned managers` phrasing.

### Error
```
Expected response content to contain: Assigned managers
```

### Context
- Operation attempted: focused `php artisan test --filter='test_authenticated_user_can_access_shops_operational_index_shape|test_shops_page_surfaces_selected_shop_context_from_laravel_data|test_resource_summary_metrics_ignore_malformed_metric_entries|test_shop_scoped_admin_sees_branch_creation_actions_disabled_in_shops_workspace'`
- Root cause: `config/admin-pages.php` was updated to `Assigned branch managers`, but the operational index test still asserted the older label
- Impact: no behavior issue, but the same surface had one stale wording assertion left behind

### Suggested Fix
When aligning a metric label on a surface, grep all assertions for that exact label on the same surface before rerunning the focused slice.

### Metadata
- Reproducible: yes
- Related Files: config/admin-pages.php, tests/Feature/AdminDashboardTest.php

---
## [ERR-20260517-003] cards_surface_metric_label_drift

**Logged**: 2026-05-17T20:29:00Z
**Priority**: medium
**Status**: pending
**Area**: tests

### Summary
A focused cards surface test failed after a summary-copy change because the config-backed metric label still used older generic wording while the assertion already expected Galaxy-specific card-shell wording.

### Error
```
Expected response content to contain: Active Galaxy card shells
```

### Context
- Operation attempted: focused `php artisan test --filter='test_authenticated_user_can_access_cards_operational_index_shape|test_cards_page_surfaces_selected_card_context_from_laravel_data|test_cards_page_supports_selected_active_holder_linked_card_review_context|test_shop_scoped_admin_sees_card_creation_actions_disabled_in_cards_workspace'`
- Root cause: `config/admin-pages.php` still exposed `Active cards` on the cards surface while the targeted test expected `Active Galaxy card shells`
- Impact: no behavior issue, but a real wording pocket remained on the live admin shell

### Suggested Fix
When aligning summary copy on a surface, quickly verify neighboring metric labels on that same surface for older generic wording before rerunning the focused slice.

### Metadata
- Reproducible: yes
- Related Files: config/admin-pages.php, tests/Feature/AdminDashboardTest.php

---
## [ERR-20260518-003] php_artisan_test_filter_miss

**Logged**: 2026-05-18T11:27:00Z
**Priority**: medium
**Status**: pending
**Area**: tests

### Summary
A guessed `php artisan test --filter=...` pattern matched no tests during a focused verification run.

### Error
```
INFO  No tests found.
```

### Context
- Command/operation attempted: `php artisan test --filter='test_authenticated_user_can_access_shop_management_page|test_authenticated_user_can_access_card_management_page|test_authenticated_user_can_access_cardholder_management_page|test_authenticated_user_can_access_roles_permissions_page'`
- Goal: verify wording-only changes on selected management pages after title-copy alignment.
- Environment detail: focused filter names must match actual PHPUnit test method names in `tests/Feature/AdminDashboardTest.php`.

### Suggested Fix
Look up the exact test method names with `grep` before composing multi-name `--filter` expressions instead of guessing likely names.

### Metadata
- Reproducible: yes
- Related Files: tests/Feature/AdminDashboardTest.php
- See Also: ERR-20260518-002

---

## [ERR-20260518-004] grep-pattern-range

**Logged**: 2026-05-18T12:27:00Z
**Priority**: low
**Status**: pending
**Area**: docs

### Summary
A grep -E pattern with a character class/range failed during wording scan, so fixed-string or simpler alternation searches are safer for these copy-only sweeps.

### Error
```
grep: Invalid range end
```

### Context
- Command/operation attempted: grep -RInE across ResourceIndexController.php and AdminDashboardTest.php
- Input or parameters used: pattern included `The current Laravel [a-z- ]+note says`
- Environment details: host grep interpreted the `-` inside the bracket expression as an invalid range

### Suggested Fix
Prefer simpler fixed-string searches or move `-` to the start/end of bracket expressions when building ad-hoc grep patterns.

### Metadata
- Reproducible: yes
- Related Files: app/Http/Controllers/Admin/ResourceIndexController.php, tests/Feature/AdminDashboardTest.php
- See Also: ERR-20260518-001

---
## [ERR-20260518-006] phpunit-filter-miss-services-rules

**Logged**: 2026-05-18T19:12:00Z
**Priority**: medium
**Status**: pending
**Area**: tests

### Summary
Guessed services-rules PHPUnit filter matched no tests during a wording sweep.

### Error
```
INFO  No tests found.
```

### Context
- Command attempted: `php artisan test --filter='test_services_rules_page_shows_rule_workflow_shell'`
- Environment details: targeted wording sweep in `config/admin-pages.php`
- The guessed filter did not match an existing test name.

### Suggested Fix
Search nearby services-rules assertions in `tests/Feature/AdminDashboardTest.php` first, then rerun using the exact discovered method name.

### Metadata
- Reproducible: yes
- Related Files: tests/Feature/AdminDashboardTest.php, config/admin-pages.php
- See Also: ERR-20260518-005

---
## [ERR-20260518-007] phpunit-filter-miss-role-form

**Logged**: 2026-05-18T19:57:00Z
**Priority**: medium
**Status**: pending
**Area**: tests

### Summary
Guessed role-form PHPUnit filter matched no tests during a wording sweep.

### Error
```
INFO  No tests found.
```

### Context
- Command attempted: `php artisan test --filter='test_role_form_persists_identity_fields'`
- Environment details: targeted wording sweep in `config/admin-pages.php` and `tests/Feature/AdminDashboardTest.php`
- The guessed filter did not match an existing test name.

### Suggested Fix
Search the surrounding roles assertions in `tests/Feature/AdminDashboardTest.php` first, then rerun using the exact discovered method name.

### Metadata
- Reproducible: yes
- Related Files: tests/Feature/AdminDashboardTest.php, config/admin-pages.php
- See Also: ERR-20260518-005, ERR-20260518-006

---
## [ERR-20260518-008] phpunit-filter-miss-cards-index

**Logged**: 2026-05-18T20:42:00Z
**Priority**: medium
**Status**: pending
**Area**: tests

### Summary
Guessed cards index PHPUnit filter matched no tests during a wording sweep.

### Error
```
INFO  No tests found.
```

### Context
- Command attempted: `php artisan test --filter='test_cards_page_replaces_preview_rows_with_model_backed_index_data'`
- Environment details: targeted wording sweep in `config/admin-pages.php` and `tests/Feature/AdminDashboardTest.php`
- The guessed filter did not match an existing test name.

### Suggested Fix
Search the surrounding cards assertions in `tests/Feature/AdminDashboardTest.php` first, then rerun using the exact discovered method name.

### Metadata
- Reproducible: yes
- Related Files: tests/Feature/AdminDashboardTest.php, config/admin-pages.php
- See Also: ERR-20260518-005, ERR-20260518-006, ERR-20260518-007

---
## [ERR-20260518-009] cards-index-assertion-drift

**Logged**: 2026-05-18T20:43:00Z
**Priority**: medium
**Status**: pending
**Area**: tests

### Summary
The cards model-backed inventory slice failed on a pre-existing assertion unrelated to the wording change.

### Error
```
Expected response to contain: Issued holder-linked Galaxy card shells
```

### Context
- Command attempted: `php artisan test --filter='test_cards_page_replaces_preview_rows_with_model_backed_inventory_data'`
- Environment details: targeted wording sweep for the cards write-path description
- Failure occurred before reaching the newly added wording assertion, indicating unrelated drift in the broader cards inventory expectations.

### Suggested Fix
Use a narrower cards slice already known to be stable for copy-only wording sweeps, or inspect the cards inventory metrics before relying on the broader model-backed index test.

### Metadata
- Reproducible: yes
- Related Files: tests/Feature/AdminDashboardTest.php, config/admin-pages.php
- See Also: ERR-20260518-008

---
## [ERR-20260518-010] roles-preview-assertion-drift-galaxy-status

**Logged**: 2026-05-18T23:27:00Z
**Priority**: medium
**Status**: pending
**Area**: tests

### Summary
The broad roles management preview slice still fails on a pre-existing `Galaxy status` assertion unrelated to the title wording change.

### Error
```
Expected response to contain: Galaxy status
```

### Context
- Command attempted: `php artisan test --filter='test_authenticated_user_can_access_roles_permissions_management_preview'`
- Environment details: targeted wording sweep for the roles live-form title
- Failure occurred before the new title assertion was the gating issue.

### Suggested Fix
Use a narrower stable roles slice for copy-only wording sweeps, or inspect the roles preview rendering before relying on the broader preview test.

### Metadata
- Reproducible: yes
- Related Files: tests/Feature/AdminDashboardTest.php, config/admin-pages.php
- See Also: ERR-20260518-007

---
## [ERR-20260518-011] selected-role-title-assumption-miss

**Logged**: 2026-05-18T23:29:00Z
**Priority**: medium
**Status**: pending
**Area**: tests

### Summary
The selected-role review slice does not render the create-state title, so the new assertion targeted the wrong UI state.

### Error
```
Expected response to contain: Create Galaxy role in Galaxy foundation
```

### Context
- Command attempted: `php artisan test --filter='test_roles_permissions_page_surfaces_selected_role_context_from_laravel_data'`
- Environment details: targeted wording sweep for the roles live-form title
- The selected-role route renders a different title than the base create-state preview.

### Suggested Fix
Inspect the selected-role response for its exact live-form title and validate the correct state-specific wording instead of assuming the create-state title carries over.

### Metadata
- Reproducible: yes
- Related Files: tests/Feature/AdminDashboardTest.php, config/admin-pages.php
- See Also: ERR-20260518-010

---

## [ERR-20260519-001] php-artisan-test

**Logged**: 2026-05-19T21:08:46+00:00
**Priority**: medium
**Status**: pending
**Area**: tests

### Summary
A malformed exact-replacement edit introduced a syntax error into ResourceIndexController during wording-only Phase 1 work.

### Error
```
ParseError
syntax error, unexpected token ",", expecting ";"
at app/Http/Controllers/Admin/ResourceIndexController.php:3510
```

### Context
- Operation attempted: focused wording alignment in selected-role review strings
- Failure happened after an exact text replacement inserted stray characters into a match arm
- Recovery: inspect the affected block, repair syntax, then rerun the narrow test slice

### Suggested Fix
After large multi-replacement edits in PHP match expressions, rerun a targeted parse-sensitive command immediately and prefer smaller grouped edits around adjacent lines.

### Metadata
- Reproducible: yes
- Related Files: app/Http/Controllers/Admin/ResourceIndexController.php

---

## [ERR-20260519-002] php-artisan-test-filter

**Logged**: 2026-05-19T21:22:26+00:00
**Priority**: low
**Status**: pending
**Area**: tests

### Summary
A focused php artisan test run used a non-matching --filter string and returned no tests found.

### Error
```
INFO  No tests found.
```

### Context
- Operation attempted: narrow regression run after wording-only Phase 1 edits
- Cause: guessed filter string did not match the actual PHPUnit test method name
- Recovery: grep exact test method names before rerunning filtered suites

### Suggested Fix
Prefer `grep -n "function test_..."` to capture the exact method name before using `php artisan test --filter=...`.

### Metadata
- Reproducible: yes
- Related Files: tests/Feature/AdminDashboardTest.php

---

## [ERR-20260519-003] reports-test-assertion-drift

**Logged**: 2026-05-19T22:07:29+00:00
**Priority**: low
**Status**: pending
**Area**: tests

### Summary
A focused reports test exposed an existing assertion drift in cards-by-shop Galaxy input wording while validating a nearby copy-only reporting change.

### Error
```
Expected response to contain: card and branch inputs are ready for on-screen review
```

### Context
- Operation attempted: focused reports regression after wording-only reporting copy alignment
- App text already uses a more specific Galaxy card-shell wording
- Recovery: align the stale assertion to the current rendered copy and rerun the narrow suite

### Suggested Fix
When touching reporting copy, scan adjacent assertions in the same selectedSummary block because older wording drift may surface only when that slice is finally rerun.

### Metadata
- Reproducible: yes
- Related Files: tests/Feature/AdminDashboardTest.php, app/Http/Controllers/Admin/ResourceIndexController.php

---

## [ERR-20260519-004] apply_patch

**Logged**: 2026-05-19T22:38:29+00:00
**Priority**: medium
**Status**: pending
**Area**: tests

### Summary
apply_patch failed because the expected adjacent assertion block in AdminDashboardTest no longer matched exactly.

### Error
```
Failed to find expected lines in tests/Feature/AdminDashboardTest.php for the combined Shop-scoped behavior and Partner-card checks assertion block.
```

### Context
- Operation attempted: multi-file apply_patch for a narrow services-rules wording slice
- Related Files: tests/Feature/AdminDashboardTest.php, app/Http/Controllers/Admin/ResourceIndexController.php
- Cause: exact-match patch targeted a block whose surrounding lines had drifted from the assumed layout

### Suggested Fix
Read the current assertion block first and patch the exact local lines or use smaller replacements.

### Metadata
- Reproducible: yes
- Related Files: tests/Feature/AdminDashboardTest.php, app/Http/Controllers/Admin/ResourceIndexController.php

---

## [ERR-20260519-005] php-artisan-test-filter

**Logged**: 2026-05-19T23:07:17+00:00
**Priority**: low
**Status**: pending
**Area**: tests

### Summary
A focused php artisan test filter used the wrong gifts test names and returned no matching tests.

### Error
```
INFO  No tests found.
```

### Context
- Command attempted: php artisan test --filter='test_gifts_page_supports_selected_reward_review_context|test_gifts_page_supports_selected_scoped_reward_review_context|test_gifts_page_supports_selected_zero_stock_reward_review_context'
- Related Files: tests/Feature/AdminDashboardTest.php
- Follow-up: reread the current test block and reran with exact test names.

### Suggested Fix
Read exact test method names before composing a narrow filter for long feature-test files.

### Metadata
- Reproducible: yes
- Related Files: tests/Feature/AdminDashboardTest.php

---

## [ERR-20260520-001] services-rules-test-drift

**Logged**: 2026-05-20T00:07:15+00:00
**Priority**: medium
**Status**: pending
**Area**: tests

### Summary
Focused services-rules assertions drifted because the selected-rule summary copy was updated but the blocked review-priority helper string remained unchanged.

### Error
```
Expected response to contain: Blocked until draft rule priority order is verified against legacy exclusion precedence in the Galaxy foundation layer.
```

### Context
- Command attempted: php artisan test --filter='test_services_rules_page_supports_selected_rule_review_context|test_services_rules_page_supports_selected_scoped_rule_review_context|test_services_rules_page_supports_selected_all_shop_rule_review_context'
- Related Files: app/Http/Controllers/Admin/ResourceIndexController.php, tests/Feature/AdminDashboardTest.php
- Cause: wording change covered selected-rule summary/dependency copy but not the separate review-priority helper string.

### Suggested Fix
When updating selected-review wording, also grep and align adjacent helper/match-arm strings that feed the same rendered surface before rerunning tests.

### Metadata
- Reproducible: yes
- Related Files: app/Http/Controllers/Admin/ResourceIndexController.php, tests/Feature/AdminDashboardTest.php

---

## [ERR-20260520-002] roles-permissions-stale-assertion

**Logged**: 2026-05-20T01:52:57+00:00
**Priority**: low
**Status**: pending
**Area**: tests

### Summary
A narrow roles-permissions wording change exposed a stale adjacent assertion in the same management-preview block.

### Error
```
Expected response to contain: Blocked until the Galaxy foundation permission matrix can be verified against legacy staff access.
```

### Context
- Command attempted: php artisan test --filter='test_authenticated_user_can_access_roles_permissions_management_preview'
- Related Files: config/admin-pages.php, tests/Feature/AdminDashboardTest.php
- Cause: the targeted publish-posture copy changed cleanly, but a neighboring assertion was already out of sync with the currently rendered matrix-help copy.

### Suggested Fix
When touching a config-driven form block, reread and synchronize adjacent assertions in the same rendered section before finalizing the slice.

### Metadata
- Reproducible: yes
- Related Files: config/admin-pages.php, tests/Feature/AdminDashboardTest.php

---

## [ERR-20260520-003] roles-placeholder-filter-drift

**Logged**: 2026-05-20T03:52:38+00:00
**Priority**: low
**Status**: pending
**Area**: tests

### Summary
A follow-up roles catalog check used the right placeholder-page test name, but that broader test already had an unrelated stale assertion for placeholder copy.

### Error
```
Expected response to contain: shop-scoped access rules
```

### Context
- Command attempted: php artisan test --filter='test_authenticated_user_can_access_roles_permissions_placeholder_page|test_roles_permissions_page_surfaces_selected_role_context_from_laravel_data'
- Related Files: tests/Feature/AdminDashboardTest.php
- Cause: broad placeholder coverage included an older assertion unrelated to the narrow unscoped-read-slice change.

### Suggested Fix
Prefer the nearest selected-surface test first for narrow wording slices, and only add broader catalog coverage after confirming its assertions are current.

---
## [ERR-20260520-001] shops_branch_form_placeholder_assertion_mismatch

**Logged**: 2026-05-20T04:06:00Z
**Priority**: low
**Status**: resolved
**Area**: tests

### Summary
A focused shops feature test assumed the branch review-note placeholder would be visible in the rendered response, but the current form rendering did not surface that exact placeholder text.

### Error
```
Expected response content to contain: Capture parity-sensitive notes for the current Galaxy foundation branch shell.
```

### Context
- Operation attempted: focused php artisan test filter for the two shops page slices
- Root cause: the new assertion targeted placeholder copy instead of stable visible form/help copy
- Relevant surface: shops live-form branch identity/review-note wording slice

### Suggested Fix
Prefer asserting stable visible helper copy for this surface unless the placeholder is explicitly known to render in the response.

### Metadata
- Reproducible: yes
- Related Files: tests/Feature/AdminDashboardTest.php, config/admin-pages.php

### Resolution
- **Resolved**: 2026-05-20T04:07:00Z
- **Commit/PR**: pending
- **Notes**: Dropped the brittle placeholder assertion and kept the visible helper-copy assertion for the narrow wording slice.

---
## [ERR-20260520-001] php-artisan-test-filter

**Logged**: 2026-05-20T07:36:00Z
**Priority**: medium
**Status**: resolved
**Area**: tests

### Summary
A focused receipts preview test failed because one expected blocker assertion still referenced old Laravel wording after nearby Galaxy foundation wording had already shifted.

### Error
```text
Expected response to contain: Blocked until zero-accrual and branch-aware troubleshooting are backed by Laravel transaction and rule data.
```

### Context
- Command attempted: `php artisan test --filter='test_authenticated_user_can_access_checks_points_operational_index_shape|test_authenticated_user_can_access_receipts_operational_index_shape|test_authenticated_user_can_access_checks_points_management_preview'`
- Relevant files: `config/admin-pages.php`, `tests/Feature/AdminDashboardTest.php`
- The failure showed this receipts preview slice should be updated as a grouped wording surface, not one string at a time.

### Suggested Fix
When touching receipts preview wording, grep and align all nearby visible blocker and notice assertions in the same focused test before rerunning.

### Metadata
- Reproducible: yes
- Related Files: config/admin-pages.php, tests/Feature/AdminDashboardTest.php

---

### Resolution
- **Resolved**: 2026-05-20T07:38:00Z
- **Commit/PR**: pending
- **Notes**: Updated the stale receipts blocker assertion to Galaxy foundation wording and re-ran the focused checks-points preview test successfully.


## [ERR-20260523-001] shops-manager-guidance-neighbor-expectation

**Logged**: 2026-05-23T21:52:00Z
**Priority**: medium
**Status**: resolved
**Area**: tests

### Summary
A focused shops backend-gap wording pass initially missed one neighboring manager-guidance assertion on the manager-only branch review slice.

### Error
```
Expected response HTML to contain: Keep current manager ownership visible during review, because legacy Galaxy branch administration depended on clear branch responsibility.
at tests/Feature/AdminDashboardTest.php:5235
```

### Context
- Command attempted: `php artisan test --filter='test_shops_page_supports_selected_branch_coverage_without_manager_review_context|test_shops_page_supports_selected_manager_only_branch_review_context|test_shops_page_supports_selected_paused_branch_review_context'`
- The real rendered copy already used `Keep current branch manager ownership visible during review...`, matching the nearby active-branch shops slice.
- The failure was a neighboring expectation drift inside the same selected-shop review surface, not a controller regression.

### Suggested Fix
When touching a focused shops review slice, grep the nearby assertions for the same guidance sentence and align all selected-shop variants before rerunning the filter.

### Metadata
- Reproducible: yes
- Related Files: tests/Feature/AdminDashboardTest.php, app/Http/Controllers/Admin/ResourceIndexController.php
- See Also: ERR-20260521-001

---
