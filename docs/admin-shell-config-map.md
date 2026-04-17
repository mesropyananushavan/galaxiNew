# Admin Shell Config Map

Related docs:
- `docs/admin-shell-layering.md`
- `docs/phase-1-plan.md`
- `docs/blueprint.md`

This note complements `docs/admin-shell-layering.md`.
It explains which config file owns each part of the Phase 1 Galaxy admin shell.

## Entry points
- `config/admin-pages.php`
  - page-specific content and sample metadata
  - table rows, metrics, actions, notices, form previews, parity notes
  - workflow metadata such as operator checklists, escalation guides, shift handoff notes, and open issues
- `config/admin-navigation.php`
  - sidebar information architecture
- `config/admin-resource-page-defaults.php`
  - shared defaults passed into resource pages
- `config/admin-resource-blocks.php`
  - bridge that composes the layered shell stacks into one render sequence

## Layered shell stacks
- `config/admin-base-shell-blocks.php`
  - base operational snapshot blocks
- `config/admin-operational-context-blocks.php`
  - parity and migration context blocks
- `config/admin-preview-shell-blocks.php`
  - preview-state CRUD shell blocks
- `config/admin-operational-workflow-blocks.php`
  - operator workflow and carry-over blocks
- `config/admin-operational-closing-blocks.php`
  - readiness, dependency, and implementation handoff blocks

## Supporting config
- `config/admin-page-rationale.php`
  - shared rationale shown at the bottom of resource pages

## Rule of thumb
- If the change is page-specific, prefer `config/admin-pages.php`.
- If the change affects many resource pages in the same structural layer, prefer one of the layered stack config files.
- If the change affects shell order across all resource pages, update `config/admin-resource-blocks.php`.

## Current high-depth workflow pages
These pages now use the richer workflow layer to feel closer to the old Galaxy back office, even before real Laravel writes exist.

- `card-types`
- `services-rules`
- `gifts`
- `roles-permissions`

If you are extending one of these screens, keep workflow metadata in `config/admin-pages.php` unless the change should affect every resource page.

## Suggested reading order
1. Read `docs/blueprint.md` for overall migration posture.
2. Read `docs/phase-1-plan.md` for the current Phase 1 target.
3. Read `docs/admin-shell-layering.md` for shell structure.
4. Use this file to pick the exact config entry point to edit.
