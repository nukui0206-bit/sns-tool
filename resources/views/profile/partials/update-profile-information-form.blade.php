<section>
    <h2 class="h5">プロフィール情報</h2>
    <p class="text-muted small">アカウントの名前とメールアドレスを更新します。</p>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="mb-3">
            <label for="name" class="form-label">名前</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                class="form-control @error('name') is-invalid @enderror">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">メールアドレス</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                class="form-control @error('email') is-invalid @enderror">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="form-text">
                    メール確認が未完了です。
                    <button form="send-verification" class="btn btn-link p-0 align-baseline">確認メールを再送する</button>
                </div>
                @if (session('status') === 'verification-link-sent')
                    <div class="alert alert-success mt-2 mb-0">確認メールを再送しました。</div>
                @endif
            @endif
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="submit" class="btn btn-primary">保存</button>
            @if (session('status') === 'profile-updated')
                <span class="text-muted small">保存しました。</span>
            @endif
        </div>
    </form>
</section>
