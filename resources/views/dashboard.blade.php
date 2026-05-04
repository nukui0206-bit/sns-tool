<x-app-layout>
    <x-slot name="header">ダッシュボード</x-slot>

    <p class="text-muted small mb-3">
        運用状況のサマリ。各カードをクリックすると関連画面へ遷移します。
    </p>

    <div class="row g-3">
        <div class="col-md-6 col-xl-3">
            <div class="card h-100 position-relative">
                <div class="card-body">
                    <div class="d-flex align-items-center text-muted small mb-2">
                        <i class="bi bi-people me-1"></i>クライアント数
                    </div>
                    <div class="h3 mb-0">{{ number_format($clientsCount) }} <small class="text-muted fs-6">社</small></div>
                </div>
                <a href="{{ route('clients.index') }}" class="stretched-link" aria-label="クライアント一覧へ"></a>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card h-100 position-relative">
                <div class="card-body">
                    <div class="d-flex align-items-center text-muted small mb-2">
                        <i class="bi bi-calendar2-event me-1"></i>予約投稿
                    </div>
                    <div class="h3 mb-0">{{ number_format($scheduledCount) }} <small class="text-muted fs-6">件</small></div>
                </div>
                <a href="{{ route('calendar.index') }}" class="stretched-link" aria-label="投稿カレンダーへ"></a>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card h-100 position-relative">
                <div class="card-body">
                    <div class="d-flex align-items-center text-muted small mb-2">
                        <i class="bi bi-check2-circle me-1"></i>本日公開済
                    </div>
                    <div class="h3 mb-0">{{ number_format($todayPostedCount) }} <small class="text-muted fs-6">件</small></div>
                </div>
                <a href="{{ route('posts.index') }}" class="stretched-link" aria-label="投稿一覧へ"></a>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card h-100 position-relative {{ $failedJobsCount > 0 ? 'border-danger' : '' }}">
                <div class="card-body">
                    <div class="d-flex align-items-center text-muted small mb-2">
                        <i class="bi bi-exclamation-triangle me-1"></i>失敗ジョブ
                    </div>
                    <div class="h3 mb-0 {{ $failedJobsCount > 0 ? 'text-danger' : '' }}">
                        {{ number_format($failedJobsCount) }} <small class="text-muted fs-6">件</small>
                    </div>
                </div>
                <a href="{{ route('failed_jobs.index') }}" class="stretched-link" aria-label="失敗ジョブ一覧へ"></a>
            </div>
        </div>
    </div>
</x-app-layout>
