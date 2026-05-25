# Phase 1 Foundation Seams

## Purpose
Track the small Galaxy-specific config and documentation seams that keep Phase 1 grounded in explicit migration structure instead of inline starter-era defaults.

## Source of truth
- readable summary: this file
- implementation baseline: `config/phase-1-foundation-seams.php`
- live runtime surface: `resources/views/admin/dashboard.blade.php`

## Current seams

### Domain baseline
- readable summary: `docs/phase-1-domain-map.md`
- implementation baseline: `config/phase-1-domain-map.php`
- visible runtime surface: `resources/views/admin/dashboard.blade.php`

### Admin reference trail
- readable summary anchor: this file
- implementation baseline: `config/phase-1-reference-docs.php`
- visible runtime surface: `resources/views/admin/dashboard.blade.php`
- current role: keeps the admin-side Phase 1 blueprint, plan, IA, shell, checkpoint, and progress-log trail aligned

### Public landing reference trail
- readable summary anchor: this file
- implementation baseline: `config/landing-docs.php`
- visible runtime surface: `resources/views/welcome.blade.php`
- current role: keeps the public Galaxy migration doc trail aligned

## Phase 1 posture
- prefer small config-backed seams over new inline Blade lists when the goal is stable Galaxy migration structure
- keep source-of-truth notes visible where contributors first encounter the surface
- when one of these seams changes, update the runtime surface, the focused test, and `docs/progress-log.md` together
