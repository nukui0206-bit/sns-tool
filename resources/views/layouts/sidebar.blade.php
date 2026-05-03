@php
    // Phase 1 着手中。route が解決可能なものは自動で有効リンクに変わる。
    $navItems = [
        ['label' => 'ダッシュボード', 'route' => 'dashboard', 'icon' => 'bi-speedometer2'],
        ['label' => 'クライアント管理', 'route' => 'clients.index', 'match' => 'clients.*', 'icon' => 'bi-people'],
        ['label' => '投稿管理', 'route' => null, 'note' => 'Phase 3', 'icon' => 'bi-pencil-square'],
        ['label' => '投稿カレンダー', 'route' => null, 'note' => 'Phase 5', 'icon' => 'bi-calendar'],
        ['label' => '連携アカウント', 'route' => null, 'note' => 'Phase 2', 'icon' => 'bi-link-45deg'],
    ];

    $adminItems = [
        ['label' => '失敗ジョブ', 'route' => null, 'note' => 'Phase 10', 'icon' => 'bi-exclamation-triangle'],
    ];
@endphp
<aside class="app-sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="appSidebar" aria-labelledby="appSidebarLabel">
    <div class="brand d-flex align-items-center justify-content-between">
        <a href="{{ route('dashboard') }}" class="text-decoration-none text-white" id="appSidebarLabel">
            {{ config('app.name', 'SNS Tool') }}
        </a>
        <button type="button" class="btn-close btn-close-white d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#appSidebar" aria-label="Close"></button>
    </div>
    <nav class="nav flex-column">
        @foreach ($navItems as $item)
            @php
                $href = $item['route'] && \Illuminate\Support\Facades\Route::has($item['route'])
                    ? route($item['route'])
                    : '#';
                $matchPattern = $item['match'] ?? $item['route'] ?? null;
                $active = $matchPattern && request()->routeIs($matchPattern);
            @endphp
            <a href="{{ $href }}" class="nav-link {{ $active ? 'active' : '' }} {{ $href === '#' ? 'is-stub' : '' }}">
                <i class="bi {{ $item['icon'] ?? 'bi-circle' }}"></i>
                <span>{{ $item['label'] }}</span>
                @if (!empty($item['note']))
                    <span class="badge" style="font-size: 0.65rem;">{{ $item['note'] }}</span>
                @endif
            </a>
        @endforeach

        <div class="section-title">運用</div>
        @foreach ($adminItems as $item)
            @php
                $href = $item['route'] && \Illuminate\Support\Facades\Route::has($item['route'])
                    ? route($item['route'])
                    : '#';
            @endphp
            <a href="{{ $href }}" class="nav-link {{ $href === '#' ? 'is-stub' : '' }}">
                <i class="bi {{ $item['icon'] ?? 'bi-circle' }}"></i>
                <span>{{ $item['label'] }}</span>
                @if (!empty($item['note']))
                    <span class="badge" style="font-size: 0.65rem;">{{ $item['note'] }}</span>
                @endif
            </a>
        @endforeach
    </nav>
</aside>
