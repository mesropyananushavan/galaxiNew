# Decisions

## 2026-04-13

### D-001: Migration posture
We are not redesigning the product first. We are rebuilding `galaxiOld` in Laravel while preserving critical functionality and UX parity.

### D-002: Architecture style
Use a simple Laravel monolith. Avoid microservices, avoid heavy SPA architecture, avoid unnecessary abstraction early.

### D-003: UI strategy
Use Blade as the default rendering layer. Keep UI dense and operational. Do not drift into a generic modern SaaS style.

### D-004: Interactivity strategy
Use Alpine.js for lightweight interaction. Use Livewire only if a specific workflow clearly benefits from it.

### D-005: Backend strategy
Model domain entities and business flows before rebuilding screens. UI should follow domain clarity, not substitute for it.

### D-006: Git transparency
Persist structured process artifacts in Git: migration plan, progress log, decisions, and focused analysis summaries when needed.

### D-007: Agent-attributed commits
Small commits should make authorship/intention obvious. Commit messages should include the responsible role in a compact prefix when work is driven by a specific agent perspective, for example:
- `[main] ...`
- `[pm] ...`
- `[architect] ...`
- `[backend] ...`
- `[frontend] ...`

If work is synthesized across multiple roles, use `[main]` and describe the synthesis in the progress log.

### D-008: 15-minute visible activity rule
Work should leave a visible trace in Git at least every 15 minutes while active work is underway. This can be either:
- a small commit and push, or
- a compact progress update committed to the repo when code changes are not ready yet.

This rule applies to coordinated multi-agent work as well. The goal is to make active progress visible without waiting for large batches.
