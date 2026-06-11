@include('admin.partials.resource-key-value-list', [
    'title' => $dependencyStatusTitle ?? 'Implementation dependencies',
    'items' => $dependencyStatus,
    'keyField' => 'label',
    'valueField' => 'value',
])
