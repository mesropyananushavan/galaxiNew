@include('admin.partials.resource-key-value-list', [
    'title' => 'Operational glossary',
    'items' => $operationalGlossary,
    'keyField' => 'term',
    'valueField' => 'meaning',
])
