@php
    /** @var \App\Models\Post $post */
    /** @var \Illuminate\Support\Collection $clients */
    $isEdit = $post->exists;
    $allowedStatuses = [
        \App\Models\Post::STATUS_DRAFT     => \App\Models\Post::STATUSES[\App\Models\Post::STATUS_DRAFT],
        \App\Models\Post::STATUS_SCHEDULED => \App\Models\Post::STATUSES[\App\Models\Post::STATUS_SCHEDULED],
    ];
    $scheduledAtValue = old(
        'scheduled_at',
        $post->scheduled_at?->format('Y-m-d\TH:i')
    );
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
