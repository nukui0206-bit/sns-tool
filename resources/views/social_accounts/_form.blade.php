@php
    /** @var \App\Models\SocialAccount $account */
    /** @var \Illuminate\Support\Collection $clients */
    $isEdit = $account->exists;
    $tokenExpiresValue = old(
        'token_expires_at',
        $account->token_expires_at?->format('Y-m-d\TH:i')
    );
@endphp

<form method="POST" action="{{ $isEdit ? route('social_accounts.update', $account) : route('social_accounts.store') }}">
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
                    <option value="{{ $client->id }}" @selected((int) old('client_id', $account->client_id) === $client->id)>
                        {{ $client->name }}@if ($client->business_name)（{{ $client->business_name }}）@endif
                    </option>
                @endforeach
            </select>
            @error('client_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label for="platform" class="form-label">プラットフォーム <span class="text-danger">*</span></label>
            <select id="platform" name="platform" class="form-select @error('platform') is-invalid @enderror">
                @foreach (\App\Models\SocialAccount::PLATFORMS as $value => $label)
                    <option value="{{ $value }}" @selected(old('platform', $account->platform) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('platform')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label for="account_name" class="form-label">アカウント名 <span class="text-danger">*</span></label>
            <input id="account_name" type="text" name="account_name" value="{{ old('account_name', $account->account_name) }}" maxlength="255" required
                class="form-control @error('account_name') is-invalid @enderror"
                placeholder="例: @brand_official">
            @error('account_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label for="external_account_id" class="form-label">外部アカウントID</label>
            <input id="external_account_id" type="text" name="external_account_id" value="{{ old('external_account_id', $account->external_account_id) }}" maxlength="255"
                class="form-control @error('external_account_id') is-invalid @enderror"
                placeholder="API 側のアカウントID（OAuth連携で自動取得予定）">
            @error('external_account_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label for="status" class="form-label">ステータス <span class="text-danger">*</span></label>
            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                @foreach (\App\Models\SocialAccount::STATUSES as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $account->status) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label for="token_expires_at" class="form-label">トークン有効期限</label>
            <input id="token_expires_at" type="datetime-local" name="token_expires_at" value="{{ $tokenExpiresValue }}"
                class="form-control @error('token_expires_at') is-invalid @enderror">
            @error('token_expires_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-12">
            <hr class="my-2">
            <p class="text-muted small mb-2">
                <i class="bi bi-shield-lock"></i>
                アクセストークン / リフレッシュトークンは AES-256（APP_KEY）で暗号化して保存されます。
            </p>
        </div>

        <div class="col-md-6">
            <label for="access_token" class="form-label">アクセストークン</label>
            <textarea id="access_token" name="access_token" rows="3"
                class="form-control font-monospace @error('access_token') is-invalid @enderror"
                placeholder="（OAuth連携時に自動セット予定）">{{ old('access_token', $account->access_token) }}</textarea>
            @error('access_token')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label for="refresh_token" class="form-label">リフレッシュトークン</label>
            <textarea id="refresh_token" name="refresh_token" rows="3"
                class="form-control font-monospace @error('refresh_token') is-invalid @enderror"
                placeholder="（OAuth連携時に自動セット予定）">{{ old('refresh_token', $account->refresh_token) }}</textarea>
            @error('refresh_token')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-12">
            <label for="memo" class="form-label">メモ</label>
            <textarea id="memo" name="memo" rows="3"
                class="form-control @error('memo') is-invalid @enderror">{{ old('memo', $account->memo) }}</textarea>
            @error('memo')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('social_accounts.index') }}" class="btn btn-outline-secondary">キャンセル</a>
        <button type="submit" class="btn btn-primary">
            {{ $isEdit ? '更新する' : '登録する' }}
        </button>
    </div>
</form>
