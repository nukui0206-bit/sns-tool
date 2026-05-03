<x-app-layout>
    <x-slot name="header">投稿管理</x-slot>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <p class="text-muted small mb-0">登録されている投稿の一覧です。</p>
        <a href="{{ route('posts.create') }}" class="btn btn-primary">＋ 新規投稿</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 64px;">ID</th>
                        <th style="width: 220px;">クライアント / 投稿先</th>
                        <th>投稿内容</th>
                        <th class="d-none d-md-table-cell" style="width: 170px;">投稿予定日時</th>
                        <th class="text-center" style="width: 100px;">ステータス</th>
                        <th class="text-end" style="width: 160px;">操作</th>
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
                                @if ($post->socialAccount)
                                    @php
                                        $platformIcon = match ($post->socialAccount->platform) {
                                            'instagram' => 'bi-instagram',
                                            'tiktok' => 'bi-tiktok',
                                            'threads' => 'bi-at',
                                            'youtube' => 'bi-youtube',
                                            default => 'bi-globe',
                                        };
                                    @endphp
                                    <div class="small text-muted mt-1">
                                        <i class="bi {{ $platformIcon }}"></i>
                                        {{ $post->socialAccount->account_name }}
                                    </div>
                                @else
                                    <div class="small text-muted mt-1">
                                        <i class="bi bi-dash-circle"></i> 投稿先未指定
                                    </div>
                                @endif
                            </td>
                            <td class="small">
                                <a href="{{ route('posts.edit', $post) }}" class="text-decoration-none">
                                    {{ \Illuminate\Support\Str::limit($post->content, 60) }}
                                </a>
                            </td>
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
                            <td class="text-end">
                                <a href="{{ route('posts.edit', $post) }}" class="btn btn-sm btn-outline-secondary">編集</a>
                                <form method="POST" action="{{ route('posts.destroy', $post) }}" class="d-inline"
                                      onsubmit="return confirm('この投稿を削除します。よろしいですか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">削除</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                まだ投稿が登録されていません。<a href="{{ route('posts.create') }}">最初の投稿を登録</a>
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
