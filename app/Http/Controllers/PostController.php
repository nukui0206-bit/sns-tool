<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Post;
use App\Models\SocialAccount;
use Illuminate\Http\JsonResponse;
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

    public function create(Request $request): View
    {
        $defaults = ['status' => Post::STATUS_DRAFT];

        // FullCalendar の日付クリックから ?scheduled_at=... で来た場合、初期値にセット。
        if ($request->filled('scheduled_at')) {
            $defaults['scheduled_at'] = $request->input('scheduled_at');
            $defaults['status'] = Post::STATUS_SCHEDULED;
        }

        return view('posts.create', [
            'post' => new Post($defaults),
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
     * FullCalendar 用 JSON。指定範囲（start..end）の scheduled_at を持つ投稿を返す。
     */
    public function calendarEvents(Request $request): JsonResponse
    {
        $start = $request->query('start');
        $end = $request->query('end');

        $posts = Post::query()
            ->with(['client', 'socialAccount'])
            ->whereNotNull('scheduled_at')
            ->when($start, fn ($q) => $q->where('scheduled_at', '>=', $start))
            ->when($end, fn ($q) => $q->where('scheduled_at', '<', $end))
            ->get();

        $events = $posts->map(function (Post $p) {
            $clientName = $p->client?->name ?? '—';
            $excerpt = mb_strlen($p->content) > 30
                ? mb_substr($p->content, 0, 30) . '…'
                : $p->content;

            return [
                'id' => $p->id,
                'title' => "{$clientName}: {$excerpt}",
                'start' => $p->scheduled_at?->toIso8601String(),
                'color' => $this->statusColor($p->status),
                // draft / scheduled のみドラッグ移動を許可
                'editable' => in_array($p->status, [Post::STATUS_DRAFT, Post::STATUS_SCHEDULED], true),
            ];
        });

        return response()->json($events);
    }

    /**
     * FullCalendar のドラッグ移動による scheduled_at 更新。
     * draft / scheduled のみ更新を受け付ける（公開済 / 失敗は再スケジュール禁止）。
     */
    public function updateSchedule(Request $request, Post $post): JsonResponse
    {
        if (! in_array($post->status, [Post::STATUS_DRAFT, Post::STATUS_SCHEDULED], true)) {
            return response()->json([
                'ok' => false,
                'error' => '公開済 / 失敗の投稿は再スケジュールできません。',
            ], 422);
        }

        $validated = $request->validate([
            'scheduled_at' => ['required', 'date'],
        ]);

        $post->update([
            'scheduled_at' => $validated['scheduled_at'],
        ]);

        return response()->json(['ok' => true]);
    }

    private function statusColor(string $status): string
    {
        return match ($status) {
            Post::STATUS_DRAFT     => '#6b7280',
            Post::STATUS_SCHEDULED => '#2563eb',
            Post::STATUS_POSTED    => '#10b981',
            Post::STATUS_FAILED    => '#dc2626',
            default                => '#6c757d',
        };
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
