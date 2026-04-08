@extends('admin.layouts.app')

@section('content')
    <section class="card">
        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 16px;">
            <div>
                <span class="eyebrow">Admin / Users / Create</span>
                <h2 style="margin: 16px 0 10px; font-size: 1.75rem;">Create user</h2>
                <p style="margin: 0; color: var(--text-muted); max-width: 780px; line-height: 1.6;">
                    Baseline admin flow for manually creating a user account with optional admin access.
                </p>
            </div>

            <a href="{{ route('admin.users.index') }}" style="display: inline-flex; align-items: center; gap: 8px; border: 1px solid var(--border); background: var(--surface); border-radius: 999px; padding: 10px 14px; color: var(--text);">
                Back to users
            </a>
        </div>

        @if ($errors->any())
            <div style="margin-top: 20px; padding: 14px 16px; border-radius: 14px; background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca;">
                <strong style="display: block; margin-bottom: 8px;">Please fix the form errors.</strong>
                <ul style="margin: 0; padding-left: 18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}" style="margin-top: 24px; display: grid; gap: 18px; max-width: 720px;">
            @csrf

            <div>
                <label for="name" style="display: block; margin-bottom: 8px; font-size: 0.95rem; font-weight: 600;">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus style="width: 100%; padding: 12px 14px; border: 1px solid var(--border); border-radius: 12px; font: inherit; background: var(--surface); color: var(--text);">
            </div>

            <div>
                <label for="email" style="display: block; margin-bottom: 8px; font-size: 0.95rem; font-weight: 600;">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" style="width: 100%; padding: 12px 14px; border: 1px solid var(--border); border-radius: 12px; font: inherit; background: var(--surface); color: var(--text);">
            </div>

            <div>
                <label for="password" style="display: block; margin-bottom: 8px; font-size: 0.95rem; font-weight: 600;">Password</label>
                <input id="password" name="password" type="password" required autocomplete="new-password" style="width: 100%; padding: 12px 14px; border: 1px solid var(--border); border-radius: 12px; font: inherit; background: var(--surface); color: var(--text);">
                <p style="margin: 8px 0 0; color: var(--text-muted); font-size: 0.9rem;">Minimum 8 characters for this baseline.</p>
            </div>

            <label for="is_admin" style="display: inline-flex; align-items: center; gap: 10px; color: var(--text-muted); font-weight: 500;">
                <input id="is_admin" name="is_admin" type="checkbox" value="1" {{ old('is_admin') ? 'checked' : '' }} style="width: auto; margin: 0;">
                <span>Grant admin access</span>
            </label>

            <div style="display: flex; align-items: center; gap: 12px;">
                <button type="submit" style="border: 0; border-radius: 12px; padding: 12px 16px; background: var(--accent); color: white; font: inherit; font-weight: 600; cursor: pointer;">
                    Create user
                </button>

                <a href="{{ route('admin.users.index') }}" style="color: var(--text-muted);">Cancel</a>
            </div>
        </form>
    </section>
@endsection
