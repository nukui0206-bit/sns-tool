<x-app-layout>
    <x-slot name="header">投稿管理</x-slot>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <p class="text-muted small mb-0">登録されている投稿の一覧です。</p>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 64px;">ID</th>
                        <th style="width: 200px;">クライアント</th>
                        <th>投稿内容</th>
                        <th class="d-none d-md-table-cell" style="width: 170px;">投稿予定日時</th>
                        <th class="text-center" style="width: 100px;">ステータス</th>
                        <th class="text-end" style="width: 120px;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($posts as $post)
                        <tr>
                            <td class="text-muted">{{ $post->id }}</td>
                            <td>
                                <span class="fw-semibold">{{ $post->client->name ?? '—' }}</span>
                                @if ($post->client?->business_name)
                                    <div class="text-muted small">{{ $post->client->business_name }}</div>
                                @endif
                            </td>
                            <td class="small">{{ \Illuminate\Support\Str::limit($post->content, 60) }}</td>
                            <td class="small text-muted d-none d-md-table-cell">
                                {{ $post->scheduled_at?->format('Y-m-d H:i') ?? '—' }}
                            </td>
                            <td class="text-center">
                                @php
                                    $badgeClass = match ($post->status) {
                                        'draft'     => 'bg-secondary',
                                        'scheduled' => 'bg-primary',
                                        'posted'    => 'bg-success',
                                        'failed'    => 'bg-danger',
                                        default     => 'bg-light text-dark',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $post->statusLabel() }}</span>
                            </td>
                            <td class="text-end text-muted small">—</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                まだ投稿が登録されていません。
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
        <div class="text-muted small">
            全 {{ $posts->total() }} 件中 {{ $posts->firstItem() ?? 0 }}〜{{ $posts->lastItem() ?? 0 }} 件
        </div>
        <div>
            {{ $posts->links() }}
        </div>
    </div>
</x-app-layout>
