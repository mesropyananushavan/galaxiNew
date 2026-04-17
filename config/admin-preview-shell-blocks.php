<?php

// Preview shell blocks keep preview-only CRUD structure visible before real handlers exist.
return [
    ['key' => 'liveForm', 'partial' => 'admin.partials.resource-live-form', 'prop' => 'liveForm'],
    ['key' => 'form', 'partial' => 'admin.partials.resource-form-preview', 'prop' => 'form'],
    ['key' => 'emptyState', 'partial' => 'admin.partials.resource-empty-state', 'prop' => 'emptyState'],
    ['key' => 'notice', 'partial' => 'admin.partials.resource-preview-notice', 'prop' => 'notice'],
];
