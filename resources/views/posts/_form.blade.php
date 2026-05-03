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
            <label for="content" class="form-label">投稿内容 <span class="text-danger">*</span></label>
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
