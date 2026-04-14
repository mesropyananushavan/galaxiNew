@include('admin.partials.resource-key-value-list', [
    'title' => 'Operational data source status',
    'items' => $operationalDataStatus,
    'keyField' => 'label',
    'valueField' => 'value',
])
