<x-app-layout>
    <x-slot name="header">ダッシュボード</x-slot>

    <div class="alert alert-info">
        <strong>SNS Tool — Phase 0 セットアップ完了</strong><br>
        Phase 1 以降でクライアント管理 / 投稿管理 / 投稿カレンダー / 連携アカウント / AI文案生成 が順次有効化されます。
    </div>

    <div class="row g-3">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small">クライアント数</div>
                    <div class="h3 mb-0">— <small class="text-muted fs-6">社</small></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small">予約投稿（直近）</div>
                    <div class="h3 mb-0">— <small class="text-muted fs-6">件</small></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small">本日公開済</div>
                    <div class="h3 mb-0">— <small class="text-muted fs-6">件</small></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small">失敗ジョブ</div>
                    <div class="h3 mb-0">— <small class="text-muted fs-6">件</small></div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
