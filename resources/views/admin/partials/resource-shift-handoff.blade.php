@include('admin.partials.resource-summary-list', [
    'title' => 'Shift handoff notes',
    'summary' => $shiftHandoff['summary'],
    'items' => $shiftHandoff['items'],
])
