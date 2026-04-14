<?php

return array_merge([
    ['key' => 'metrics', 'partial' => 'admin.partials.resource-summary-metrics', 'prop' => 'metrics'],
    ['key' => 'table', 'partial' => 'admin.partials.operational-index-table', 'prop' => 'table'],
], config('admin-operational-context-blocks', []), [
    ['key' => 'form', 'partial' => 'admin.partials.resource-form-preview', 'prop' => 'form'],
    ['key' => 'emptyState', 'partial' => 'admin.partials.resource-empty-state', 'prop' => 'emptyState'],
    ['key' => 'notice', 'partial' => 'admin.partials.resource-preview-notice', 'prop' => 'notice'],
], config('admin-operational-workflow-blocks', []), config('admin-operational-closing-blocks', []));
