<x-app-layout>
    <x-slot name="header">投稿の新規登録</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="{{ route('posts.index') }}">投稿管理</a></li>
            <li class="breadcrumb-item active" aria-current="page">新規登録</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-body">
            @include('posts._form')
        </div>
    </div>
</x-app-layout>
