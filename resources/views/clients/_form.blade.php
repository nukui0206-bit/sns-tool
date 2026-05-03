@php
    /** @var \App\Models\Client $client */
    $isEdit = $client->exists;
@endphp

<form method="POST" action="{{ $isEdit ? route('clients.update', $client) : route('clients.store') }}">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="row g-3">
        <div class="col-md-8">
            <label for="name" class="form-label">クライアント名 <span class="text-danger">*</span></label>
            <input id="name" type="text" name="name" value="{{ old('name', $client->name) }}" maxlength="255" required autofocus
                class="form-control @error('name') is-invalid @enderror">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-4">
            <label for="status" class="form-label">ステータス <span class="text-danger">*</span></label>
            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                @foreach (\App\Models\Client::STATUSES as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $client->status) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-12">
            <label for="business_name" class="form-label">店舗名</label>
            <input id="business_name" type="text" name="business_name" value="{{ old('business_name', $client->business_name) }}" maxlength="255"
                class="form-control @error('business_name') is-invalid @enderror">
            @error('business_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label for="email" class="form-label">メールアドレス</label>
            <input id="email" type="email" name="email" value="{{ old('email', $client->email) }}" maxlength="255"
                placeholder="例: client@example.com"
                class="form-control @error('email') is-invalid @enderror">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label for="phone" class="form-label">電話番号</label>
            <input id="phone" type="text" name="phone" value="{{ old('phone', $client->phone) }}" maxlength="50"
                placeholder="例: 03-1234-5678"
                class="form-control @error('phone') is-invalid @enderror">
            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-12">
            <label for="address" class="form-label">住所</label>
            <input id="address" type="text" name="address" value="{{ old('address', $client->address) }}" maxlength="255"
                class="form-control @error('address') is-invalid @enderror">
            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-12">
            <label for="memo" class="form-label">メモ</label>
            <textarea id="memo" name="memo" rows="4"
                class="form-control @error('memo') is-invalid @enderror">{{ old('memo', $client->memo) }}</textarea>
            @error('memo')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">キャンセル</a>
        <button type="submit" class="btn btn-primary">
            {{ $isEdit ? '更新する' : '登録する' }}
        </button>
    </div>
</form>
