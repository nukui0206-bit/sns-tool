<x-app-layout>
    <x-slot name="header">連携アカウントの編集</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="{{ route('social_accounts.index') }}">連携アカウント</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $account->account_name }}</li>
        </ol>
    </nav>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            @include('social_accounts._form')
        </div>
    </div>
</x-app-layout>
