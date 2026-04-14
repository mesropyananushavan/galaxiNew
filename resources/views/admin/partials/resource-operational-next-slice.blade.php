@include('admin.partials.resource-summary-list', [
    'title' => 'Operational next slice',
    'summary' => $operationalNextSlice['summary'],
    'items' => $operationalNextSlice['steps'],
])
