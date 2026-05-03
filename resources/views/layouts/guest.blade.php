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
    <div class="guest-shell">
        <div class="guest-card">
            <div class="text-center mb-4">
                <a href="/" class="text-decoration-none">
                    <span class="h4 fw-semibold text-dark">{{ config('app.name', 'SNS Tool') }}</span>
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-4">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
