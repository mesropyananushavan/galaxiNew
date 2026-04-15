<?php

return array_merge([
    ['key' => 'metrics', 'partial' => 'admin.partials.resource-summary-metrics', 'prop' => 'metrics'],
    ['key' => 'table', 'partial' => 'admin.partials.operational-index-table', 'prop' => 'table'],
], config('admin-operational-context-blocks', []), config('admin-preview-shell-blocks', []), config('admin-operational-workflow-blocks', []), config('admin-operational-closing-blocks', []));
