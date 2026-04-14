<?php

return array_merge([
    ['key' => 'metrics', 'partial' => 'admin.partials.resource-summary-metrics', 'prop' => 'metrics'],
    ['key' => 'table', 'partial' => 'admin.partials.operational-index-table', 'prop' => 'table'],
], config('admin-operational-context-blocks', []), [
    ['key' => 'form', 'partial' => 'admin.partials.resource-form-preview', 'prop' => 'form'],
    ['key' => 'emptyState', 'partial' => 'admin.partials.resource-empty-state', 'prop' => 'emptyState'],
    ['key' => 'notice', 'partial' => 'admin.partials.resource-preview-notice', 'prop' => 'notice'],
], config('admin-operational-workflow-blocks', []), [
    ['key' => 'readinessChecklist', 'partial' => 'admin.partials.resource-readiness-checklist', 'prop' => 'readinessChecklist'],
    ['key' => 'dependencyStatus', 'partial' => 'admin.partials.resource-dependency-status', 'prop' => 'dependencyStatus'],
    ['key' => 'implementationHandoff', 'partial' => 'admin.partials.resource-implementation-handoff', 'prop' => 'implementationHandoff'],
]);
