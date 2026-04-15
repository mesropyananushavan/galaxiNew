# Admin Shell Layering

Related docs:
- `docs/phase-1-plan.md`
- `docs/blueprint.md`
- `docs/admin-shell-config-map.md`

## Why this exists
Phase 1 keeps the Galaxy admin shell moving toward old back-office parity while PHP execution is unavailable.
To avoid growing one long fragile block list, the resource page shell is now split into composable layers.

## Current layer order
1. **Base shell** via `config/admin-base-shell-blocks.php`
   - summary metrics
   - operational table
2. **Operational context** via `config/admin-operational-context-blocks.php`
   - glossary
   - parity notes
   - next slice
   - data status
   - migration blockers
   - legacy mapping
   - activity timeline
3. **Preview shell** via `config/admin-preview-shell-blocks.php`
   - form preview
   - empty state
   - preview notice
4. **Operational workflow** via `config/admin-operational-workflow-blocks.php`
   - operator checklist
   - escalation guide
   - shift handoff
   - open issues
5. **Operational closing** via `config/admin-operational-closing-blocks.php`
   - readiness checklist
   - dependency status
   - implementation handoff

## Bridge config
`config/admin-resource-blocks.php` remains the single bridge that composes the layered stacks into the resource page block sequence used by `config/admin-resource-page-defaults.php` and `App\Http\Controllers\Admin\ResourceIndexController`.

## Why the split matters
- keeps the shell config-first
- makes dense operational pages easier to reorder safely
- keeps Phase 1 parity cues visible without pushing logic into Blade
- creates smaller seams for future replacement with real Laravel reads and writes

## Expected next move
When PHP becomes available, replace one preview/config-backed section with the first real Laravel read or form flow without undoing the layered shell structure.

## Where to start when editing
- Start with `docs/admin-shell-config-map.md` if you are not sure which config file owns the change.
- Start with `config/admin-pages.php` for page-specific content edits.
- Start with the layered stack configs for cross-page shell structure edits.
