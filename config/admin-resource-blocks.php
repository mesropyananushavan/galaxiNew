<?php

return array_merge(
    config('admin-base-shell-blocks', []),
    config('admin-operational-context-blocks', []),
    config('admin-preview-shell-blocks', []),
    config('admin-operational-workflow-blocks', []),
    config('admin-operational-closing-blocks', [])
);
