@extends('admin.layouts.app')

@section('content')
    <section class="card">
        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 16px;">
            <div>
                <span class="eyebrow">Admin / Users / Show</span>
                <h2 style="margin: 16px 0 10px; font-size: 1.75rem;">User details</h2>
                <p style="margin: 0; color: var(--text-muted); max-width: 780px; line-height: 1.6;">
                    Read-only baseline view for a single user from the admin area.
                </p>
            </div>

            <a href="{{ route('admin.users.index') }}" style="display: inline-flex; align-items: center; gap: 8px; border: 1px solid var(--border); background: var(--surface); border-radius: 999px; padding: 10px 14px; color: var(--text);">
                Back to users
            </a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; margin-top: 24px;">
            <div class="metric">
                <p class="metric-label">Name</p>
                <p class="metric-value" style="font-size: 1.15rem;">{{ $user->name ?: '—' }}</p>
            </div>

            <div class="metric">
                <p class="metric-label">Email</p>
                <p class="metric-value" style="font-size: 1.15rem;">{{ $user->email ?: '—' }}</p>
            </div>

            <div class="metric">
                <p class="metric-label">Admin</p>
                <p style="margin: 0;">
                    <span class="badge {{ $user->is_admin ? 'badge-success' : 'badge-muted' }}">
                        {{ $user->is_admin ? 'Yes' : 'No' }}
                    </span>
                </p>
            </div>

            <div class="metric">
                <p class="metric-label">Created at</p>
                <p class="metric-value" style="font-size: 1.15rem;">{{ optional($user->created_at)->format('Y-m-d H:i') ?? '—' }}</p>
            </div>
        </div>
    </section>
@endsection
