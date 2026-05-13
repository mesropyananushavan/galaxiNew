# Errors

Command failures and integration errors.

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
