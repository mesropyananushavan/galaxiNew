@include('admin.partials.resource-key-value-list', [
    'title' => 'Selected record summary',
    'items' => $selectedRecordSummary,
    'keyField' => 'label',
    'valueField' => 'value',
])
