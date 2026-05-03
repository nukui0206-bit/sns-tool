<x-app-layout>
    <x-slot name="header">クライアント管理</x-slot>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <p class="text-muted small mb-0">登録されているクライアントの一覧です。</p>
        <a href="{{ route('clients.create') }}" class="btn btn-primary">＋ 新規登録</a>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('clients.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md">
                    <input type="text" name="q" value="{{ $q }}" class="form-control"
                           placeholder="クライアント名 / 店舗名で検索">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-outline-secondary">検索</button>
                    @if ($q !== '')
                        <a href="{{ route('clients.index') }}" class="btn btn-link text-muted">クリア</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 64px;">ID</th>
                        <th>クライアント名</th>
                        <th class="d-none d-md-table-cell">メール</th>
                        <th class="d-none d-lg-table-cell">電話</th>
                        <th class="text-center" style="width: 100px;">ステータス</th>
                        <th class="text-end" style="width: 160px;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clients as $client)
                        <tr>
                            <td class="text-muted">{{ $client->id }}</td>
                            <td>
                                <a href="{{ route('clients.edit', $client) }}" class="text-decoration-none fw-semibold">
                                    {{ $client->name }}
                                </a>
                                @if ($client->business_name)
                                    <span class="text-muted small ms-1">／ {{ $client->business_name }}</span>
                                @endif
                                <div class="d-md-none small text-muted mt-1">
                                    {{ $client->email ?? '—' }}
                                </div>
                            </td>
                            <td class="small text-muted d-none d-md-table-cell">{{ $client->email ?? '—' }}</td>
                            <td class="small text-muted d-none d-lg-table-cell">{{ $client->phone ?? '—' }}</td>
                            <td class="text-center">
                                @if ($client->status === 'active')
                                    <span class="badge bg-success">稼働中</span>
                                @else
                                    <span class="badge bg-secondary">停止中</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-outline-secondary">編集</a>
                                <form method="POST" action="{{ route('clients.destroy', $client) }}" class="d-inline"
                                      onsubmit="return confirm('「{{ $client->name }}」を削除します。よろしいですか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">削除</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                @if ($q !== '')
                                    条件に一致するクライアントが見つかりませんでした。
                                @else
                                    まだクライアントが登録されていません。<a href="{{ route('clients.create') }}">最初のクライアントを登録</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
        <div class="text-muted small">
            全 {{ $clients->total() }} 件中 {{ $clients->firstItem() ?? 0 }}〜{{ $clients->lastItem() ?? 0 }} 件
        </div>
        <div>
            {{ $clients->links() }}
        </div>
    </div>
</x-app-layout>
