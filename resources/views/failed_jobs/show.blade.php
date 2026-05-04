<x-app-layout>
    <x-slot name="header">失敗ジョブ #{{ $job->id }}</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="{{ route('failed_jobs.index') }}">失敗ジョブ</a></li>
            <li class="breadcrumb-item active" aria-current="page">#{{ $job->id }}</li>
        </ol>
    </nav>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3 small">
                <div class="col-md-6">
                    <div class="text-muted">ジョブ</div>
                    <div class="fw-semibold font-monospace">{{ $displayName }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">UUID</div>
                    <div class="font-monospace text-break">{{ $job->uuid }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">失敗日時</div>
                    <div>{{ \Illuminate\Support\Carbon::parse($job->failed_at)->format('Y-m-d H:i:s') }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">connection</div>
                    <div class="font-monospace">{{ $job->connection }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">queue</div>
                    <div class="font-monospace">{{ $job->queue }}</div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-3">
                <form method="POST" action="{{ route('failed_jobs.retry', $job->id) }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-clockwise"></i> 再実行する
                    </button>
                </form>
                <form method="POST" action="{{ route('failed_jobs.destroy', $job->id) }}" class="m-0"
                      onsubmit="return confirm('この失敗ジョブを削除します。よろしいですか？');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">削除する</button>
                </form>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header bg-danger-subtle">
            <strong>例外（Exception）</strong>
        </div>
        <div class="card-body p-0">
            <pre class="mb-0 p-3 small text-danger-emphasis bg-light"
                 style="white-space: pre-wrap; word-wrap: break-word;">{{ $job->exception }}</pre>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <strong>Payload（ジョブ復元データ）</strong>
        </div>
        <div class="card-body p-0">
            <pre class="mb-0 p-3 small bg-light"
                 style="white-space: pre-wrap; word-wrap: break-word;">{{ json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
        </div>
    </div>
</x-app-layout>
