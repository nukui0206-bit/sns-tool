<x-app-layout>
    <x-slot name="header">連携アカウント</x-slot>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <p class="text-muted small mb-0">クライアントごとの SNS アカウント連携情報を管理します。</p>
        <a href="{{ route('social_accounts.create') }}" class="btn btn-primary">＋ 新規登録</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 64px;">ID</th>
                        <th style="width: 200px;">クライアント</th>
                        <th style="width: 130px;">プラットフォーム</th>
                        <th>アカウント名</th>
                        <th class="d-none d-md-table-cell" style="width: 170px;">トークン期限</th>
                        <th class="text-center" style="width: 100px;">ステータス</th>
                        <th class="text-end" style="width: 160px;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($accounts as $account)
                        <tr>
                            <td class="text-muted">{{ $account->id }}</td>
                            <td>
                                <span class="fw-semibold">{{ $account->client->name ?? '—' }}</span>
                                @if ($account->client?->business_name)
                                    <div class="text-muted small">{{ $account->client->business_name }}</div>
                                @endif
                            </td>
                            <td>
                                @php
                                    $platformIcon = match ($account->platform) {
                                        'instagram' => 'bi-instagram',
                                        'tiktok' => 'bi-tiktok',
                                        'threads' => 'bi-at',
                                        'youtube' => 'bi-youtube',
                                        default => 'bi-globe',
                                    };
                                @endphp
                                <i class="bi {{ $platformIcon }} me-1"></i>{{ $account->platformLabel() }}
                            </td>
                            <td>
                                <a href="{{ route('social_accounts.edit', $account) }}" class="text-decoration-none">
                                    {{ $account->account_name }}
                                </a>
                                @if ($account->external_account_id)
                                    <div class="text-muted small">{{ $account->external_account_id }}</div>
                                @endif
                            </td>
                            <td class="small text-muted d-none d-md-table-cell">
                                {{ $account->token_expires_at?->format('Y-m-d H:i') ?? '—' }}
                            </td>
                            <td class="text-center">
                                @php
                                    $badgeClass = match ($account->status) {
                                        'connected'    => 'bg-success',
                                        'disconnected' => 'bg-secondary',
                                        'error'        => 'bg-danger',
                                        default        => 'bg-light text-dark',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $account->statusLabel() }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('social_accounts.edit', $account) }}" class="btn btn-sm btn-outline-secondary">編集</a>
                                <form method="POST" action="{{ route('social_accounts.destroy', $account) }}" class="d-inline"
                                      onsubmit="return confirm('「{{ $account->account_name }}」を削除します。よろしいですか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">削除</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                まだ連携アカウントが登録されていません。<a href="{{ route('social_accounts.create') }}">最初のアカウントを登録</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
        <div class="text-muted small">
            全 {{ $accounts->total() }} 件中 {{ $accounts->firstItem() ?? 0 }}〜{{ $accounts->lastItem() ?? 0 }} 件
        </div>
        <div>
            {{ $accounts->links() }}
        </div>
    </div>
</x-app-layout>
