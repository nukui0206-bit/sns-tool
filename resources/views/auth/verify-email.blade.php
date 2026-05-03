<x-guest-layout>
    <p class="text-muted small">
        登録ありがとうございます。メールに送信した確認リンクをクリックしてください。<br>
        メールが届かない場合は再送できます。
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success">確認メールを再送しました。</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mt-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary">確認メールを再送</button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="m-0">
            @csrf
            <button type="submit" class="btn btn-link text-muted small p-0">ログアウト</button>
        </form>
    </div>
</x-guest-layout>
