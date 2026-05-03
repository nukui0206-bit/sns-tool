<section>
    <h2 class="h5 text-danger">アカウント削除</h2>
    <p class="text-muted small">
        アカウントを削除すると、関連データがすべて完全に削除されます。
        削除前に必要なデータをエクスポートしてください。
    </p>

    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#confirmUserDeletion">
        アカウントを削除
    </button>

    <div class="modal fade" id="confirmUserDeletion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="modal-header">
                        <h5 class="modal-title">本当にアカウントを削除しますか？</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="small text-muted">
                            この操作は取り消せません。確認のためパスワードを入力してください。
                        </p>
                        <div>
                            <label for="password" class="form-label visually-hidden">パスワード</label>
                            <input id="password" name="password" type="password" placeholder="パスワード"
                                class="form-control @error('password', 'userDeletion') is-invalid @enderror">
                            @error('password', 'userDeletion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                        <button type="submit" class="btn btn-danger">削除する</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($errors->userDeletion->isNotEmpty())
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                new bootstrap.Modal(document.getElementById('confirmUserDeletion')).show();
            });
        </script>
    @endif
</section>
