<x-app-layout>
    <x-slot name="header">クライアントの新規登録</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="{{ route('clients.index') }}">クライアント管理</a></li>
            <li class="breadcrumb-item active" aria-current="page">新規登録</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-body">
            @include('clients._form')
        </div>
    </div>
</x-app-layout>
