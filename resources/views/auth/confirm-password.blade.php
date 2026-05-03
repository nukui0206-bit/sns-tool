<x-guest-layout>
    <p class="text-muted small">
        セキュリティ確認のため、パスワードを再入力してください。
    </p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="mb-3">
            <label for="password" class="form-label">パスワード</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="form-control @error('password') is-invalid @enderror">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">確認</button>
        </div>
    </form>
</x-guest-layout>
