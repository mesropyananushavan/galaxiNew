# Phase 1 Foundation Seams

## Purpose
Track the small Galaxy-specific config and documentation seams that keep Phase 1 grounded in explicit migration structure instead of inline starter-era defaults.

## Source of truth
- readable summary anchor: `docs/phase-1-foundation-seams.md`
- implementation baseline anchor: `config/phase-1-foundation-seams.php`
- seam-source baseline bridge: `config/phase-1-seam-sources.php`
- live runtime surface: `resources/views/admin/dashboard.blade.php`
- admin runtime note: `App\Http\Controllers\Admin\DashboardController` now formats seam-source and foundation-seam reference strings before they reach the dashboard surface, routes review-entry, latest-work, and assigned-branch action link collections through prepared workspace-link payloads, prepares migration-map navigation links before render, shapes the foundation snapshot, assigned-branch, domain, live-entry, latest-work, migration-map, reference-doc, seam-source, and foundation-seam metric payloads before render, and no longer hands the old raw planned-section counter or raw live-entry/latest-work summary strings directly to Blade

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
- current role: keeps the admin-side Phase 1 blueprint, plan, IA, shell, checkpoint, progress-log, and seam-source baseline trail aligned, with the dashboard controller now formatting more of that runtime reference prose, shaping the linked reference-doc inventory display payload, and preparing the reference-doc summary metrics before render

### Admin domain baseline runtime handoff
- readable summary anchor: this file
- implementation baseline: `config/phase-1-domain-map.php`
- visible runtime surface: `resources/views/admin/dashboard.blade.php`
- current role: keeps the dashboard controller responsible for formatting the entity-map guide and source-of-truth reference strings before they reach the admin runtime surface
- related runtime shaping: the dashboard controller now also prepares the entity coverage and inventory metric payload before render, so the admin domain baseline stays less tied to raw summary strings inside Blade
- related runtime shaping: the dashboard controller now also prepares the entity inventory payload before render, so the admin domain baseline stays less tied to raw config structure inside Blade
- related runtime shaping: the dashboard controller now also prepares the foundation-seam source trail text and secondary sources note before render so the admin seam inventory stays less Blade-assembled

### Public landing reference trail
- readable summary anchor: this file
- implementation baseline: `config/landing-docs.php`
- source-of-truth anchors: `README.md`, `config/landing-docs.php`
- seam-source bridge: `config/phase-1-seam-sources.php`
- visible runtime surface: `resources/views/welcome.blade.php`
- current role: keeps the public Galaxy migration doc trail, docs-card heading, metric labels, explanatory notes, config-path callouts, and seam-source bridge references aligned, with the landing controller now preparing the full docs-card payload, including heading, summary rows, and docs inventory, before render

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
- current role: keeps the README-level config seam inventory aligned across repo guidance plus the admin and public entry surfaces, with both entry controllers now formatting parts of that runtime reference trail and the dashboard controller shaping both the admin seam-source inventory and seam-source summary metrics before render

## Phase 1 posture
- prefer small config-backed seams over new inline Blade lists when the goal is stable Galaxy migration structure
- keep source-of-truth notes visible where contributors first encounter the surface
- when one of these seams changes, update the runtime surface, the focused test, and `docs/progress-log.md` together
