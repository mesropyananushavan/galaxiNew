@include('admin.partials.resource-summary-list', [
    'title' => 'First Laravel wiring step',
    'summary' => $implementationHandoff['summary'],
    'items' => $implementationHandoff['steps'],
])
