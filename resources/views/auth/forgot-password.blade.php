<x-guest-layout>
    <h1 class="h5 mb-3">パスワード再設定</h1>

    <p class="text-muted small">
        登録メールアドレスを入力してください。再設定用リンクをお送りします。
    </p>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">メールアドレス</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="form-control @error('email') is-invalid @enderror">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">再設定リンクを送信</button>
        </div>
    </form>
</x-guest-layout>
