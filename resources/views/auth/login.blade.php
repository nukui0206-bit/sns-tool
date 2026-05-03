<x-guest-layout>
    <h1 class="h5 mb-3 text-center">ログイン</h1>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">メールアドレス</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                class="form-control @error('email') is-invalid @enderror">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">パスワード</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="form-control @error('password') is-invalid @enderror">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-check mb-3">
            <input id="remember_me" type="checkbox" name="remember" class="form-check-input">
            <label for="remember_me" class="form-check-label">ログイン状態を保持する</label>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="small text-muted">パスワードを忘れた方</a>
            @else
                <span></span>
            @endif
            <button type="submit" class="btn btn-primary">ログイン</button>
        </div>
    </form>
</x-guest-layout>
