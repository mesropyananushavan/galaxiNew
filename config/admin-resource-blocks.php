<?php

// Bridge the layered admin shell stacks into one default resource page block sequence.
// See docs/admin-shell-layering.md for the Phase 1 rationale and layer responsibilities.
return array_merge(
    config('admin-base-shell-blocks', []),
    config('admin-operational-context-blocks', []),
    config('admin-preview-shell-blocks', []),
    config('admin-operational-workflow-blocks', []),
    config('admin-operational-closing-blocks', [])
);
