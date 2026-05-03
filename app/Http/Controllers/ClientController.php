<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $clients = Client::query()
            ->search($request->string('q')->toString())
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('clients.index', [
            'clients' => $clients,
            'q' => $request->string('q')->toString(),
        ]);
    }

    public function create(): View
    {
        return view('clients.create', [
            'client' => new Client(['status' => Client::STATUS_ACTIVE]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);

        Client::create($validated);

        return redirect()
            ->route('clients.index')
            ->with('status', 'クライアントを登録しました。');
    }

    public function show(Client $client): RedirectResponse
    {
        return redirect()->route('clients.edit', $client);
    }

    public function edit(Client $client): View
    {
        return view('clients.edit', [
            'client' => $client,
        ]);
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $validated = $this->validatedData($request);

        $client->update($validated);

        return redirect()
            ->route('clients.edit', $client)
            ->with('status', 'クライアント情報を更新しました。');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();

        return redirect()
            ->route('clients.index')
            ->with('status', 'クライアントを削除しました。');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(array_keys(Client::STATUSES))],
            'memo' => ['nullable', 'string'],
        ]);
    }
}
