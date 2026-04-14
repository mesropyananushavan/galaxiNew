@extends('admin.layouts.app')

@section('content')
    @include('admin.partials.resource-page-header', [
        'eyebrow' => $eyebrow,
        'pageTitle' => $pageTitle,
        'summary' => $summary,
        'actions' => $actions ?? [],
        'resourceKey' => $resourceKey,
        'phase' => $phase,
        'nextStep' => $nextStep,
    ])

    @foreach ($resourceBlocks as $block)
        @if (! empty(${$block['key']}))
            @include($block['partial'], [$block['prop'] => ${$block['key']}])
        @endif
    @endforeach

    @include('admin.partials.resource-page-rationale', ['items' => $pageRationale])
@endsection
