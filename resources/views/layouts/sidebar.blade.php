@php
    // Phase 0 時点ではダッシュボード以外スタブリンク。各 Phase で route を実装したら有効化される。
    $navItems = [
        ['label' => 'ダッシュボード',     'route' => 'dashboard'],
        ['label' => 'クライアント管理',   'route' => null, 'note' => 'Phase 1'],
        ['label' => '投稿管理',           'route' => null, 'note' => 'Phase 3'],
        ['label' => '投稿カレンダー',     'route' => null, 'note' => 'Phase 5'],
        ['label' => '連携アカウント',     'route' => null, 'note' => 'Phase 2'],
    ];

    $adminItems = [
        ['label' => '失敗ジョブ', 'route' => null, 'note' => 'Phase 10'],
    ];
@endphp
<aside class="app-sidebar">
    <div class="brand">
        <a href="{{ route('dashboard') }}" class="text-decoration-none text-white">
            {{ config('app.name', 'SNS Tool') }}
        </a>
    </div>
    <nav class="nav flex-column mt-2">
        @foreach ($navItems as $item)
            @php
                $href = $item['route'] && \Illuminate\Support\Facades\Route::has($item['route'])
                    ? route($item['route'])
                    : '#';
                $matchPattern = $item['match'] ?? $item['route'] ?? null;
                $active = $matchPattern && request()->routeIs($matchPattern);
            @endphp
            <a href="{{ $href }}" class="nav-link {{ $active ? 'active' : '' }} {{ $href === '#' ? 'opacity-50' : '' }}">
                {{ $item['label'] }}
                @if (!empty($item['note']))
                    <span class="badge bg-secondary ms-1" style="font-size: 0.65rem;">{{ $item['note'] }}</span>
                @endif
            </a>
        @endforeach

        <div class="text-muted text-uppercase small mt-4 mb-1 px-3" style="font-size: 0.7rem; letter-spacing: 0.05em;">
            運用
        </div>
        @foreach ($adminItems as $item)
            @php
                $href = $item['route'] && \Illuminate\Support\Facades\Route::has($item['route'])
                    ? route($item['route'])
                    : '#';
            @endphp
            <a href="{{ $href }}" class="nav-link {{ $href === '#' ? 'opacity-50' : '' }}">
                {{ $item['label'] }}
                @if (!empty($item['note']))
                    <span class="badge bg-secondary ms-1" style="font-size: 0.65rem;">{{ $item['note'] }}</span>
                @endif
            </a>
        @endforeach
    </nav>
</aside>
