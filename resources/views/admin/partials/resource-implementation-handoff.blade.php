@include('admin.partials.resource-summary-list', [
    'title' => 'First Galaxy foundation wiring step',
    'summary' => $implementationHandoff['summary'],
    'items' => $implementationHandoff['steps'],
])
