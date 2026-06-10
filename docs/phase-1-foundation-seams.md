# Phase 1 Foundation Seams

## Purpose
Track the small Galaxy-specific config and documentation seams that keep Phase 1 grounded in explicit migration structure instead of inline starter-era defaults.

## Source of truth
- readable summary anchor: `docs/phase-1-foundation-seams.md`
- implementation baseline anchor: `config/phase-1-foundation-seams.php`
- seam-source baseline bridge: `config/phase-1-seam-sources.php`
- live runtime surface: `resources/views/admin/dashboard.blade.php`
- admin runtime note: `App\Http\Controllers\Admin\DashboardController` now formats seam-source, model-skeleton, migration baseline, access-baseline, shop-scope baseline, and foundation-seam reference strings before they reach the dashboard surface, routes review-entry, latest-work, and assigned-branch action link collections through prepared workspace-link payloads, prepares migration-map navigation and item summary payloads before render, shapes the foundation snapshot, assigned-branch, domain, model-skeleton, migration baseline, access-baseline, shop-scope baseline, live-entry, latest-work, migration-map, reference-doc, seam-source, and foundation-seam metric payloads before render, and no longer hands the old raw planned-section counter or raw live-entry/latest-work summary strings directly to Blade

## Current seams

### Domain baseline
- readable summary: `docs/phase-1-domain-map.md`
- implementation baseline: `config/phase-1-domain-map.php`
- source-of-truth anchors: `docs/phase-1-domain-map.md`, `config/phase-1-domain-map.php`
- visible runtime surface: `resources/views/admin/dashboard.blade.php`

### Admin reference trail
- readable summary anchor: this file
- implementation baseline: `config/phase-1-reference-docs.php`
- source-of-truth anchors: `README.md`, `docs/blueprint.md`, `docs/phase-1-plan.md`, `config/phase-1-reference-docs.php`
- seam-source bridge: `config/phase-1-seam-sources.php`
- visible runtime surface: `resources/views/admin/dashboard.blade.php`
- current role: keeps the admin-side Phase 1 blueprint, plan, IA, shell, checkpoint, progress-log, newer access/data-layer baselines, and seam-source baseline trail aligned, with the dashboard controller now formatting more of that runtime reference prose, shaping the linked reference-doc inventory display payload, and preparing the reference-doc summary metrics before render

### Admin domain baseline runtime handoff
- readable summary anchor: this file
- implementation baseline: `config/phase-1-domain-map.php`
- visible runtime surface: `resources/views/admin/dashboard.blade.php`
- current role: keeps the dashboard controller responsible for formatting the entity-map guide and source-of-truth reference strings before they reach the admin runtime surface
- related runtime shaping: the dashboard controller now also prepares the entity coverage and inventory metric payload before render, so the admin domain baseline stays less tied to raw summary strings inside Blade
- related runtime shaping: the dashboard controller now also prepares the entity inventory display payload before render, so the admin domain baseline stays less tied to raw config structure and line assembly inside Blade
- related runtime shaping: the dashboard controller now also prepares the foundation-seam primary summary line plus the source trail text and secondary sources note before render so the admin seam inventory stays less Blade-assembled

### Migration baseline
- readable summary anchor: `docs/phase-1-migration-baseline.md`
- implementation baseline: `config/phase-1-migration-baseline.php`
- source-of-truth anchors: `docs/phase-1-migration-baseline.md`, `config/phase-1-migration-baseline.php`, `database/migrations`
- visible runtime surface: `resources/views/admin/dashboard.blade.php`
- current role: keeps the first Galaxy schema anchors explicit while Phase 1 migration work is still landing, with the dashboard controller now shaping both the migration metrics and the tracked schema checkpoint inventory before render

### Model skeleton baseline
- readable summary anchor: `docs/phase-1-model-skeletons.md`
- implementation baseline: `config/phase-1-model-skeletons.php`
- source-of-truth anchors: `docs/phase-1-model-skeletons.md`, `config/phase-1-model-skeletons.php`, `app/Models`, `database/migrations`
- visible runtime surface: `resources/views/admin/dashboard.blade.php`
- current role: keeps the first Galaxy Eloquent models and migration anchors explicit while Phase 1 data-layer work is still landing, with the dashboard controller now shaping both the model-skeleton metrics and the tracked skeleton inventory before render

### Admin access baseline
- readable summary anchor: `docs/phase-1-access-baseline.md`
- implementation baseline: `config/phase-1-access-baseline.php`
- source-of-truth anchors: `docs/phase-1-access-baseline.md`, `config/phase-1-access-baseline.php`, `app/Providers/Concerns/RegistersAdminAccessGates.php`, `app/Providers/Concerns/RegistersAdminPolicies.php`, `app/Policies/PermissionPolicy.php`, `routes/admin.php`
- visible runtime surface: `resources/views/admin/dashboard.blade.php`
- current role: keeps the first Galaxy authorization gates and policy mappings explicit while Phase 1 admin access and shop scope are still landing, with the dashboard controller now shaping both the access metrics and the tracked gate/policy inventory, now including the permission-vocabulary policy seam, before render
- runtime note: the shared `roles-permissions` admin review route now requires both the role and permission `viewAny` policy checks so the permission-vocabulary seam participates in a real endpoint guard instead of staying dashboard-only
- runtime note: the dashboard access-baseline card now tracks the first live `shops`, `cardholders`, `cards`, `card-types`, and `roles-permissions` route trios plus the dedicated `card-types` status-toggle action as first-class guardrail entries alongside the broader gate and policy inventories, and now also includes the current shell-guarded `checks-points`, `services-rules`, `gifts`, and `reports` review routes so the dashboard reflects both policy-backed and shared-shell Galaxy access lanes with controller-prepared HTTP method and URI contract text plus controller-shaped intro notes and metrics, including a visible policy-backed versus shared-shell split, before render

### Shop-scoped access baseline
- readable summary anchor: `docs/phase-1-shop-access-baseline.md`
- implementation baseline: `config/phase-1-shop-access-baseline.php`
- source-of-truth anchors: `docs/phase-1-shop-access-baseline.md`, `config/phase-1-shop-access-baseline.php`, `app/Models/User.php`, `app/Policies/ShopPolicy.php`, `app/Providers/Concerns/RegistersAdminAccessGates.php`
- visible runtime surface: `resources/views/admin/dashboard.blade.php`
- current role: keeps the first Galaxy branch-scoped access rules explicit while Phase 1 shop visibility is still landing, with the dashboard controller now shaping both the shop-scope metrics and the tracked branch-boundary rules before render

### Public landing reference trail
- readable summary anchor: this file
- implementation baseline: `config/landing-docs.php`
- source-of-truth anchors: `README.md`, `config/landing-docs.php`
- seam-source bridge: `config/phase-1-seam-sources.php`
- visible runtime surface: `resources/views/welcome.blade.php`
- current role: keeps the public Galaxy migration doc trail, docs-card heading, metric labels, explanatory notes, config-path callouts, newer access/data-layer source-of-truth anchors, and seam-source bridge references aligned, with the landing controller now preparing the full docs-card payload, including heading, summary rows, and docs inventory, before render

### Public landing shell baseline
- readable summary anchor: this file
- implementation baseline: `config/landing-foundation.php`
- source-of-truth anchors: `config/landing-foundation.php`, `resources/views/welcome.blade.php`
- visible runtime surface: `resources/views/welcome.blade.php`
- current role: keeps the public Galaxy landing hero copy, focus note, emphasis tokens, CTA actions, snapshot framing, live-surface headings, working-rule copy, and named route/controller handoff explicit through one config-backed Phase 1 seam, with the controller now passing a controller-shaped hero frame, prepared hero description HTML, prepared hero actions, controller-shaped landing snapshot rows, controller-shaped landing foundation cards, and a controller-shaped docs card payload into the runtime surface instead of leaving raw landing config reads in Blade

### Top-level repo guidance
- readable summary anchor: this file
- source-of-truth anchors: `README.md`, `docs/blueprint.md`, `docs/phase-1-plan.md`, `config/phase-1-reference-docs.php`
- visible runtime surface: `README.md`
- current role: keeps top-level Phase 1 references and seam-source guidance aligned for contributors before they open the live surfaces

### README seam-source trail
- readable summary anchor: this file
- implementation baseline: `config/phase-1-seam-sources.php`
- source-of-truth anchors: `README.md`, `config/phase-1-seam-sources.php`
- seam focus: keep the README-level seam-source inventory visible across repo guidance plus the admin and public Phase 1 entry surfaces
- seam posture: README-backed seam-source baseline stays explicit across the live Galaxy reference trail
- visible runtime surfaces: `README.md`, `resources/views/admin/dashboard.blade.php`, `resources/views/welcome.blade.php`
- current role: keeps the README-level config seam inventory aligned across repo guidance plus the admin and public entry surfaces, with both entry controllers now formatting parts of that runtime reference trail and the dashboard controller shaping both the admin seam-source inventory display payload and seam-source summary metrics before render

## Phase 1 posture
- prefer small config-backed seams over new inline Blade lists when the goal is stable Galaxy migration structure
- keep source-of-truth notes visible where contributors first encounter the surface
- when one of these seams changes, update the runtime surface, the focused test, and `docs/progress-log.md` together
