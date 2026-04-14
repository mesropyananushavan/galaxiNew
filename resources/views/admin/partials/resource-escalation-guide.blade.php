@include('admin.partials.resource-summary-list', [
    'title' => 'Escalation guide',
    'summary' => $escalationGuide['summary'],
    'items' => $escalationGuide['items'],
])
