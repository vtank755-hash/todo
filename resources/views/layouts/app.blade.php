<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="topbar">
        <div class="container topbar-inner">
            <a class="brand" href="{{ route('todos.index') }}">
                <span class="brand-mark">✓</span>
                {{ config('app.name') }}
            </a>

            <nav class="nav">
                <a href="{{ route('todos.index') }}"
                   class="nav-link {{ request()->routeIs('todos.*') ? 'is-active' : '' }}">
                    Todos
                </a>
                <a href="{{ route('categories.index') }}"
                   class="nav-link {{ request()->routeIs('categories.*') ? 'is-active' : '' }}">
                    Categories
                </a>
            </nav>

            @auth
                <div class="topbar-user">
                    <span class="avatar" aria-hidden="true">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <span class="topbar-username">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-ghost btn-sm">Log out</button>
                    </form>
                </div>
            @endauth
        </div>
    </header>

    <main class="container main">
        @include('partials.flash')

        @yield('content')
    </main>

    <footer class="container footer">
        {{ config('app.name') }} &middot; Laravel {{ app()->version() }} &middot; MySQL
    </footer>

    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
