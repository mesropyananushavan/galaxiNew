@extends('admin.layouts.app')

@section('content')
    <section class="card">
        <span class="eyebrow">{{ $eyebrow }}</span>
        <h2 style="margin: 16px 0 12px; font-size: 1.75rem;">{{ $pageTitle }} placeholder</h2>
        <p style="margin: 0; color: var(--text-muted); max-width: 780px; line-height: 1.6;">
            {{ $summary }}
        </p>

        @if (! empty($actions))
            <div style="margin-top: 18px;">
                @include('admin.partials.resource-actions', ['actions' => $actions])
            </div>
        @endif

        <div class="placeholder-grid">
            <article class="metric">
                <p class="metric-label">Section key</p>
                <p class="metric-value" style="font-size: 1.2rem;">{{ $resourceKey }}</p>
            </article>
            <article class="metric">
                <p class="metric-label">Phase</p>
                <p class="metric-value">1</p>
            </article>
            <article class="metric">
                <p class="metric-label">Next step</p>
                <p class="metric-value" style="font-size: 1.05rem; line-height: 1.4;">{{ $nextStep }}</p>
            </article>
        </div>
    </section>

    @foreach ($resourceBlocks as $block)
        @if (! empty(${$block['key']}))
            @include($block['partial'], [$block['prop'] => ${$block['key']}])
        @endif
    @endforeach

    @include('admin.partials.resource-page-rationale', ['items' => $pageRationale])
@endsection
