@extends('admin.layouts.app')

@section('content')
    <section class="card">
        <span class="eyebrow">Admin / Users</span>
        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-top: 16px;">
            <div>
                <h2 style="margin: 0 0 10px; font-size: 1.75rem;">Users</h2>
                <p style="margin: 0; color: var(--text-muted); max-width: 780px; line-height: 1.6;">
                    First real admin module baseline: a simple users list for quick visibility into who has access.
                </p>
            </div>
            <div style="color: var(--text-muted); font-size: 0.95rem; white-space: nowrap;">
                Total: {{ $users->count() }}
            </div>
        </div>

        @if ($users->isEmpty())
            <div class="empty-state" style="margin-top: 20px;">
                No users found yet.
            </div>
        @else
            <div style="margin-top: 20px; overflow-x: auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Admin</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge {{ $user->is_admin ? 'badge-success' : 'badge-muted' }}">
                                        {{ $user->is_admin ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td>{{ optional($user->created_at)->format('Y-m-d H:i') ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
