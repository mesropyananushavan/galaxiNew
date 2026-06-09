# Phase 1 Foundation Seams

## Purpose
Track the small Galaxy-specific config and documentation seams that keep Phase 1 grounded in explicit migration structure instead of inline starter-era defaults.

## Source of truth
- readable summary anchor: `docs/phase-1-foundation-seams.md`
- implementation baseline anchor: `config/phase-1-foundation-seams.php`
- seam-source baseline bridge: `config/phase-1-seam-sources.php`
- live runtime surface: `resources/views/admin/dashboard.blade.php`

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
- current role: keeps the admin-side Phase 1 blueprint, plan, IA, shell, checkpoint, progress-log, and seam-source baseline trail aligned

### Public landing reference trail
- readable summary anchor: this file
- implementation baseline: `config/landing-docs.php`
- source-of-truth anchors: `README.md`, `config/landing-docs.php`
- seam-source bridge: `config/phase-1-seam-sources.php`
- visible runtime surface: `resources/views/welcome.blade.php`
- current role: keeps the public Galaxy migration doc trail plus seam-source baseline aligned

### Public landing shell baseline
- readable summary anchor: this file
- implementation baseline: `config/landing-foundation.php`
- source-of-truth anchors: `config/landing-foundation.php`, `resources/views/welcome.blade.php`
- visible runtime surface: `resources/views/welcome.blade.php`
- current role: keeps the public Galaxy landing hero, snapshot, live-surface, and working-rule copy explicit through one config-backed Phase 1 seam

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
- current role: keeps the README-level config seam inventory aligned across repo guidance plus the admin and public entry surfaces

## Phase 1 posture
- prefer small config-backed seams over new inline Blade lists when the goal is stable Galaxy migration structure
- keep source-of-truth notes visible where contributors first encounter the surface
- when one of these seams changes, update the runtime surface, the focused test, and `docs/progress-log.md` together
