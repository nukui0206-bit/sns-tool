<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SNS Tool') }}</title>

    @vite(['resources/js/app.js'])
</head>
<body>
    <div class="app-shell">
        @include('layouts.sidebar')

        <div class="app-main">
            <header class="app-topbar">
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary topbar-toggler d-lg-none"
                            data-bs-toggle="offcanvas" data-bs-target="#appSidebar" aria-controls="appSidebar">
                        ☰
                    </button>
                    <div class="fw-semibold">
                        @isset($header){{ $header }}@endisset
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('profile.edit') }}" class="text-decoration-none text-dark small">
                        {{ Auth::user()?->name }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary">ログアウト</button>
                    </form>
                </div>
            </header>

            <main class="app-content flex-grow-1">
                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                {{ $slot }}
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
