<?php

// Base shell blocks anchor each resource page with the primary operational snapshot.
return [
    ['key' => 'metrics', 'partial' => 'admin.partials.resource-summary-metrics', 'prop' => 'metrics'],
    ['key' => 'table', 'partial' => 'admin.partials.operational-index-table', 'prop' => 'table'],
];
