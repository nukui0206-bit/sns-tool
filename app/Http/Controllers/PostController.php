<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Post;
use App\Models\SocialAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        $posts = Post::query()
            ->with(['client', 'socialAccount'])
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('posts.index', [
            'posts' => $posts,
        ]);
    }

    public function create(): View
    {
        return view('posts.create', [
            'post' => new Post(['status' => Post::STATUS_DRAFT]),
            'clients' => Client::orderBy('name')->get(),
            'socialAccounts' => $this->loadSocialAccountsByClient(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);

        Post::create($validated);

        return redirect()
            ->route('posts.index')
            ->with('status', '投稿を登録しました。');
    }

    public function edit(Post $post): View
    {
        return view('posts.edit', [
            'post' => $post,
            'clients' => Client::orderBy('name')->get(),
            'socialAccounts' => $this->loadSocialAccountsByClient(),
        ]);
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $validated = $this->validatedData($request);

        $post->update($validated);

        return redirect()
            ->route('posts.edit', $post)
            ->with('status', '投稿を更新しました。');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        return redirect()
            ->route('posts.index')
            ->with('status', '投稿を削除しました。');
    }

    /**
     * クライアント別にグループ化した SocialAccount コレクションを返す。
     * フォームの <optgroup> で「クライアントごとに出し分け」する用途。
     *
     * @return \Illuminate\Support\Collection<int|string, \Illuminate\Support\Collection<int, SocialAccount>>
     */
    private function loadSocialAccountsByClient(): \Illuminate\Support\Collection
    {
        return SocialAccount::with('client')
            ->orderBy('client_id')
            ->orderBy('platform')
            ->get()
            ->groupBy('client_id');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request): array
    {
        $allowedStatuses = [Post::STATUS_DRAFT, Post::STATUS_SCHEDULED];
        $isScheduled = $request->input('status') === Post::STATUS_SCHEDULED;
        $clientId = $request->input('client_id');

        return $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            // social_account_id は任意。指定された場合は client_id と一致する必要あり。
            'social_account_id' => [
                'nullable',
                'integer',
                Rule::exists('social_accounts', 'id')->where(
                    fn ($q) => $q->where('client_id', $clientId)
                ),
            ],
            'content' => ['required', 'string'],
            'scheduled_at' => [$isScheduled ? 'required' : 'nullable', 'date'],
            'status' => ['required', Rule::in($allowedStatuses)],
        ], [
            'social_account_id.exists' => '選択された連携アカウントが、選択中のクライアントに紐付いていません。',
        ]);
    }
}
