@include('admin.partials.resource-summary-list', [
    'title' => 'Open issues to carry',
    'summary' => $openIssues['summary'],
    'items' => $openIssues['items'],
])
