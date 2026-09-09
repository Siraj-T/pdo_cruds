<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laravel Users CRUD & Eloquent Runner')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

    <header class="navbar">
        <div class="navbar-container">
            <a href="{{ route('users.index') }}" class="brand">
                <div class="brand-icon">L</div>
                <div class="brand-text">
                    <h1>Laravel Users CRUD</h1>
                    <span>The Modern MVC & Eloquent Way</span>
                </div>
            </a>

            <nav class="nav-links">
                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}">
                    👥 Users Management
                </a>
                <a href="{{ route('guide') }}" class="nav-link {{ request()->routeIs('guide') ? 'active' : '' }}">
                    📖 PDO vs Eloquent Guide
                </a>
                <span class="nav-badge">SQLite Database</span>
            </nav>
        </div>
    </header>

    <main class="main-content">
        @if (session('success'))
            <div class="alert alert-success">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">
                <strong>⚠ Notice:</strong> {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="footer">
        <p>Built with <strong>Laravel 12 / 13</strong>, <strong>Eloquent ORM</strong>, and <strong>SQLite</strong>. Converted from Vanilla PHP PDO.</p>
    </footer>

    @yield('scripts')
</body>
</html>
