@include('admin.partials.resource-key-value-list', [
    'title' => 'Implementation dependencies',
    'items' => $dependencyStatus,
    'keyField' => 'label',
    'valueField' => 'value',
])
