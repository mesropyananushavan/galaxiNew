@include('admin.partials.resource-summary-list', [
    'title' => 'Operator checklist',
    'summary' => $operatorChecklist['summary'],
    'items' => $operatorChecklist['items'],
])
