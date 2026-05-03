<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\SocialAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SocialAccountController extends Controller
{
    public function index(Request $request): View
    {
        $accounts = SocialAccount::query()
            ->with('client')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('social_accounts.index', [
            'accounts' => $accounts,
        ]);
    }

    public function create(): View
    {
        return view('social_accounts.create', [
            'account' => new SocialAccount([
                'platform' => SocialAccount::PLATFORM_INSTAGRAM,
                'status' => SocialAccount::STATUS_DISCONNECTED,
            ]),
            'clients' => Client::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);

        SocialAccount::create($validated);

        return redirect()
            ->route('social_accounts.index')
            ->with('status', '連携アカウントを登録しました。');
    }

    public function show(SocialAccount $socialAccount): RedirectResponse
    {
        return redirect()->route('social_accounts.edit', $socialAccount);
    }

    public function edit(SocialAccount $socialAccount): View
    {
        return view('social_accounts.edit', [
            'account' => $socialAccount,
            'clients' => Client::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, SocialAccount $socialAccount): RedirectResponse
    {
        $validated = $this->validatedData($request);

        $socialAccount->update($validated);

        return redirect()
            ->route('social_accounts.edit', $socialAccount)
            ->with('status', '連携アカウントを更新しました。');
    }

    public function destroy(SocialAccount $socialAccount): RedirectResponse
    {
        $socialAccount->delete();

        return redirect()
            ->route('social_accounts.index')
            ->with('status', '連携アカウントを削除しました。');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request): array
    {
        return $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'platform' => ['required', Rule::in(array_keys(SocialAccount::PLATFORMS))],
            'account_name' => ['required', 'string', 'max:255'],
            'external_account_id' => ['nullable', 'string', 'max:255'],
            'access_token' => ['nullable', 'string'],
            'refresh_token' => ['nullable', 'string'],
            'token_expires_at' => ['nullable', 'date'],
            'status' => ['required', Rule::in(array_keys(SocialAccount::STATUSES))],
            'memo' => ['nullable', 'string'],
        ]);
    }
}
