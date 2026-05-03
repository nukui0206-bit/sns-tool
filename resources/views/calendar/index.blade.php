<x-app-layout>
    <x-slot name="header">投稿カレンダー</x-slot>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <p class="text-muted small mb-0">
            予約投稿を月間カレンダーで確認・編集できます。
            <span class="ms-2"><span class="badge" style="background-color:#6b7280">下書き</span></span>
            <span><span class="badge" style="background-color:#2563eb">予約済</span></span>
            <span><span class="badge" style="background-color:#10b981">公開済</span></span>
            <span><span class="badge" style="background-color:#dc2626">失敗</span></span>
        </p>
        <a href="{{ route('posts.create') }}" class="btn btn-primary">＋ 新規投稿</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div id="calendar"
                data-events-url="{{ route('calendar.events') }}"
                data-edit-url-pattern="{{ url('/posts/__ID__/edit') }}"
                data-create-url="{{ route('posts.create') }}"
                data-schedule-url-pattern="{{ url('/posts/__ID__/schedule') }}">
            </div>
        </div>
    </div>

    @push('scripts')
        @vite(['resources/js/calendar.js'])
    @endpush
</x-app-layout>
