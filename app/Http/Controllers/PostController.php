<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        $posts = Post::query()
            ->with('client')
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
     * @return array<string, mixed>
     */
    private function validatedData(Request $request): array
    {
        $allowedStatuses = [Post::STATUS_DRAFT, Post::STATUS_SCHEDULED];
        $isScheduled = $request->input('status') === Post::STATUS_SCHEDULED;

        return $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'content' => ['required', 'string'],
            'scheduled_at' => [$isScheduled ? 'required' : 'nullable', 'date'],
            'status' => ['required', Rule::in($allowedStatuses)],
        ]);
    }
}
