@extends('admin.layouts.app')

@section('content')
    @if (session('status'))
        <section class="card" id="backend-flow-status" tabindex="-1" role="status" aria-live="polite" style="border-color: rgba(34, 197, 94, 0.35); background: rgba(34, 197, 94, 0.08);">
            <strong style="display: block; margin-bottom: 6px;">Backend flow checkpoint</strong>
            <span>{{ session('status') }}</span>
        </section>
    @endif

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
