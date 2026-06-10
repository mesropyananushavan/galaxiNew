@include('admin.partials.resource-key-value-list', [
    'title' => $selectedRecordSummaryTitle ?? 'Selected record summary',
    'items' => $selectedRecordSummary,
    'keyField' => 'label',
    'valueField' => 'value',
])
