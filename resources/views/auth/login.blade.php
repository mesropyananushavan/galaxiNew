<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login · Galaxi Admin</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f5f7fb;
            --surface: #ffffff;
            --border: #dbe3f0;
            --text: #172033;
            --text-muted: #5f6b85;
            --accent: #4f46e5;
            --danger: #b91c1c;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        .card {
            width: min(100%, 420px);
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 28px;
            box-shadow: 0 10px 30px rgba(16, 24, 43, 0.06);
        }

        h1 {
            margin: 0 0 8px;
            font-size: 1.5rem;
        }

        p {
            margin: 0 0 24px;
            color: var(--text-muted);
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.95rem;
            font-weight: 600;
        }

        input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 12px;
            font: inherit;
            margin-bottom: 16px;
        }

        .checkbox {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            color: var(--text-muted);
        }

        .checkbox input {
            width: auto;
            margin: 0;
        }

        button {
            width: 100%;
            border: 0;
            border-radius: 12px;
            padding: 12px 14px;
            background: var(--accent);
            color: white;
            font: inherit;
            font-weight: 600;
            cursor: pointer;
        }

        .error {
            margin: 0 0 16px;
            padding: 12px 14px;
            border-radius: 12px;
            background: #fef2f2;
            color: var(--danger);
            border: 1px solid #fecaca;
        }

        .hint {
            margin-top: 16px;
            font-size: 0.875rem;
            color: var(--text-muted);
        }
    </style>
</head>
<body>
    <main class="card">
        <h1>Admin login</h1>
        <p>Minimal session-based sign in for the admin baseline.</p>

        @if ($errors->any())
            <div class="error">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username">

            <label for="password">Password</label>
            <input id="password" name="password" type="password" required autocomplete="current-password">

            <label class="checkbox" for="remember">
                <input id="remember" name="remember" type="checkbox" value="1" {{ old('remember') ? 'checked' : '' }}>
                <span>Remember this session</span>
            </label>

            <button type="submit">Sign in</button>
        </form>

        <div class="hint">
            Access to <code>/admin</code> now requires an authenticated session.
        </div>
    </main>
</body>
</html>
