<x-app-layout>
    <x-slot name="header">投稿の編集</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="{{ route('posts.index') }}">投稿管理</a></li>
            <li class="breadcrumb-item active" aria-current="page">投稿 #{{ $post->id }}</li>
        </ol>
    </nav>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            @include('posts._form')
        </div>
    </div>
</x-app-layout>
