@php
    /** @var \App\Models\Post $post */
    /** @var \Illuminate\Support\Collection $clients */
    /** @var \Illuminate\Support\Collection $socialAccounts */  // groupBy(client_id) 済み
    $isEdit = $post->exists;
    $allowedStatuses = [
        \App\Models\Post::STATUS_DRAFT     => \App\Models\Post::STATUSES[\App\Models\Post::STATUS_DRAFT],
        \App\Models\Post::STATUS_SCHEDULED => \App\Models\Post::STATUSES[\App\Models\Post::STATUS_SCHEDULED],
    ];
    $scheduledAtValue = old(
        'scheduled_at',
        $post->scheduled_at?->format('Y-m-d\TH:i')
    );
    $selectedSocialAccountId = (int) old('social_account_id', $post->social_account_id);
@endphp

<form method="POST" action="{{ $isEdit ? route('posts.update', $post) : route('posts.store') }}">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="row g-3">
        <div class="col-md-6">
            <label for="client_id" class="form-label">クライアント <span class="text-danger">*</span></label>
            <select id="client_id" name="client_id" required
                class="form-select @error('client_id') is-invalid @enderror">
                <option value="">選択してください</option>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}" @selected((int) old('client_id', $post->client_id) === $client->id)>
                        {{ $client->name }}@if ($client->business_name)（{{ $client->business_name }}）@endif
                    </option>
                @endforeach
            </select>
            @error('client_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label for="social_account_id" class="form-label">投稿先アカウント</label>
            <select id="social_account_id" name="social_account_id"
                class="form-select @error('social_account_id') is-invalid @enderror">
                <option value="">指定しない（下書き等）</option>
                @foreach ($socialAccounts as $clientId => $accounts)
                    @php $clientLabel = $accounts->first()->client?->name ?? "Client #{$clientId}"; @endphp
                    <optgroup label="{{ $clientLabel }}">
                        @foreach ($accounts as $sa)
                            <option value="{{ $sa->id }}" data-client-id="{{ $sa->client_id }}"
                                @selected($selectedSocialAccountId === $sa->id)>
                                {{ \App\Models\SocialAccount::PLATFORMS[$sa->platform] ?? $sa->platform }}
                                — {{ $sa->account_name }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
            <div class="form-text">
                クライアントに紐付くアカウントのみ選択可能。指定がない場合は下書き／後から決める扱い。
            </div>
            @error('social_account_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label for="status" class="form-label">ステータス <span class="text-danger">*</span></label>
            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                @foreach ($allowedStatuses as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $post->status) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <div class="form-text">「予約済」を選ぶ場合は投稿予定日時が必須になります。</div>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label for="scheduled_at" class="form-label">投稿予定日時</label>
            <input id="scheduled_at" type="datetime-local" name="scheduled_at" value="{{ $scheduledAtValue }}"
                class="form-control @error('scheduled_at') is-invalid @enderror">
            @error('scheduled_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="content" class="form-label mb-0">投稿内容 <span class="text-danger">*</span></label>
                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#aiGenerateModal">
                    <i class="bi bi-magic"></i> AI下書き生成
                </button>
            </div>
            <textarea id="content" name="content" rows="6" required
                class="form-control @error('content') is-invalid @enderror">{{ old('content', $post->content) }}</textarea>
            @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('posts.index') }}" class="btn btn-outline-secondary">キャンセル</a>
        <button type="submit" class="btn btn-primary">
            {{ $isEdit ? '更新する' : '登録する' }}
        </button>
    </div>
</form>

{{-- Phase 6: AI 下書き生成モーダル --}}
<div class="modal fade" id="aiGenerateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-magic"></i> AI下書き生成</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="ai_prompt" class="form-label">どんな投稿を作りたいですか？</label>
                    <textarea id="ai_prompt" rows="3" class="form-control"
                        placeholder="例：新商品のキャンペーン告知、週末のイベント案内、フォロワー向けのお礼メッセージ"></textarea>
                    <div class="form-text">
                        現在は <strong>Stub 実装</strong>（サンプル文を返すだけ）。
                        Phase 7 で OpenAI / Claude に差し替え予定。
                    </div>
                </div>

                <div id="ai_result_wrapper" class="d-none">
                    <label for="ai_result" class="form-label">生成結果</label>
                    <textarea id="ai_result" rows="8" class="form-control" readonly></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">キャンセル</button>
                <button type="button" class="btn btn-primary" id="ai_generate_btn">
                    <i class="bi bi-stars"></i> 生成する
                </button>
                <button type="button" class="btn btn-success d-none" id="ai_apply_btn">
                    <i class="bi bi-check2"></i> 投稿内容に貼り付け
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const generateUrl = @json(route('posts.ai_generate'));

    document.addEventListener('DOMContentLoaded', () => {
        const generateBtn = document.getElementById('ai_generate_btn');
        if (!generateBtn) return;

        const promptInput = document.getElementById('ai_prompt');
        const resultWrapper = document.getElementById('ai_result_wrapper');
        const resultTextarea = document.getElementById('ai_result');
        const applyBtn = document.getElementById('ai_apply_btn');
        const contentTextarea = document.getElementById('content');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        generateBtn.addEventListener('click', async () => {
            const prompt = promptInput.value.trim();
            if (!prompt) {
                alert('プロンプトを入力してください。');
                return;
            }

            const originalText = generateBtn.innerHTML;
            generateBtn.disabled = true;
            generateBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>生成中...';

            try {
                const res = await fetch(generateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ prompt }),
                    credentials: 'same-origin',
                });

                if (!res.ok) {
                    const data = await res.json().catch(() => ({}));
                    throw new Error(data.message ?? `HTTP ${res.status}`);
                }

                const data = await res.json();
                resultTextarea.value = data.content ?? '';
                resultWrapper.classList.remove('d-none');
                applyBtn.classList.remove('d-none');
            } catch (e) {
                alert('生成に失敗しました：' + e.message);
            } finally {
                generateBtn.disabled = false;
                generateBtn.innerHTML = originalText;
            }
        });

        applyBtn.addEventListener('click', () => {
            if (contentTextarea && resultTextarea.value) {
                contentTextarea.value = resultTextarea.value;
            }
            const modalEl = document.getElementById('aiGenerateModal');
            const modal = window.bootstrap?.Modal.getInstance(modalEl);
            modal?.hide();

            // リセット
            promptInput.value = '';
            resultTextarea.value = '';
            resultWrapper.classList.add('d-none');
            applyBtn.classList.add('d-none');
        });
    });
})();
</script>
@endpush
