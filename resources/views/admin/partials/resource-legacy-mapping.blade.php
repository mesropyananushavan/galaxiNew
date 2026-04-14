@include('admin.partials.resource-key-value-list', [
    'title' => 'Legacy parity mapping',
    'items' => $legacyMapping,
    'keyField' => 'label',
    'valueField' => 'value',
])
