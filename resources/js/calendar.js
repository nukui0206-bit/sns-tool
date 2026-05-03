// Phase 5: 投稿カレンダー（FullCalendar）
// /calendar 画面のみで読み込まれる。@vite(['resources/js/calendar.js']) で呼び出し。

import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import jaLocale from '@fullcalendar/core/locales/ja';

document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('calendar');
    if (!el) return;

    const eventsUrl = el.dataset.eventsUrl;
    const editUrlPattern = el.dataset.editUrlPattern;
    const createUrl = el.dataset.createUrl;
    const scheduleUrlPattern = el.dataset.scheduleUrlPattern;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const calendar = new Calendar(el, {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        locale: jaLocale,
        timeZone: 'local',
        initialView: 'dayGridMonth',
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek',
        },
        buttonText: {
            today: '今日',
            month: '月',
            week: '週',
        },
        editable: true,        // ドラッグ移動を許可（個別 event の editable で上書き）
        eventStartEditable: true,
        eventDurationEditable: false,
        events: { url: eventsUrl, method: 'GET' },

        eventClick: (info) => {
            info.jsEvent.preventDefault();
            window.location.href = editUrlPattern.replace('__ID__', info.event.id);
        },

        dateClick: (info) => {
            // 「日付クリックでその日の投稿作成画面へ」
            // datetime-local 互換のフォーマット 'YYYY-MM-DDTHH:MM' でクエリ送信
            const d = info.date;
            const pad = (n) => String(n).padStart(2, '0');
            const local = `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T09:00`;
            window.location.href = `${createUrl}?scheduled_at=${encodeURIComponent(local)}`;
        },

        eventDrop: async (info) => {
            const url = scheduleUrlPattern.replace('__ID__', info.event.id);
            const newStart = info.event.start;
            // ローカルタイムを 'YYYY-MM-DD HH:MM:SS' で送る（Laravel が parse できる形）
            const pad = (n) => String(n).padStart(2, '0');
            const newStartStr =
                `${newStart.getFullYear()}-${pad(newStart.getMonth() + 1)}-${pad(newStart.getDate())} ` +
                `${pad(newStart.getHours())}:${pad(newStart.getMinutes())}:${pad(newStart.getSeconds())}`;

            try {
                const res = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ scheduled_at: newStartStr }),
                    credentials: 'same-origin',
                });

                if (!res.ok) {
                    const data = await res.json().catch(() => ({}));
                    info.revert();
                    alert(data.error ?? '時刻の更新に失敗しました。');
                }
            } catch (e) {
                info.revert();
                alert('通信エラーが発生しました。');
            }
        },
    });

    calendar.render();
});
