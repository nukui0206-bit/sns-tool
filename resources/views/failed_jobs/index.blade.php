<x-app-layout>
    <x-slot name="header">失敗ジョブ</x-slot>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <p class="text-muted small mb-0">
            キューで失敗したジョブの一覧です。再実行（retry）または削除できます。
        </p>
        @if ($jobs->total() > 0)
            <form method="POST" action="{{ route('failed_jobs.destroy_all') }}" class="m-0"
                  onsubmit="return confirm('すべての失敗ジョブを削除します。本当によろしいですか？');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-trash"></i> すべて削除
                </button>
            </form>
        @endif
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 64px;">ID</th>
                        <th>ジョブ</th>
                        <th class="d-none d-md-table-cell" style="width: 130px;">キュー</th>
                        <th style="width: 170px;">失敗日時</th>
                        <th class="text-end" style="width: 220px;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jobs as $job)
                        <tr>
                            <td class="text-muted">{{ $job->id }}</td>
                            <td>
                                <a href="{{ route('failed_jobs.show', $job->id) }}" class="text-decoration-none fw-semibold">
                                    {{ $job->display_name }}
                                </a>
                                <div class="text-muted small font-monospace">{{ \Illuminate\Support\Str::limit(strtok($job->exception, "\n"), 100) }}</div>
                            </td>
                            <td class="small text-muted d-none d-md-table-cell">
                                {{ $job->connection }} / {{ $job->queue }}
                            </td>
                            <td class="small text-muted">{{ \Illuminate\Support\Carbon::parse($job->failed_at)->format('Y-m-d H:i:s') }}</td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('failed_jobs.retry', $job->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-arrow-clockwise"></i> 再実行
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('failed_jobs.destroy', $job->id) }}" class="d-inline"
                                      onsubmit="return confirm('この失敗ジョブを削除します。よろしいですか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">削除</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-check2-circle"></i> 失敗ジョブはありません。
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
        <div class="text-muted small">
            全 {{ $jobs->total() }} 件中 {{ $jobs->firstItem() ?? 0 }}〜{{ $jobs->lastItem() ?? 0 }} 件
        </div>
        <div>
            {{ $jobs->links() }}
        </div>
    </div>
</x-app-layout>
