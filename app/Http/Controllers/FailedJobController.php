<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FailedJobController extends Controller
{
    public function index(): View
    {
        $jobs = DB::table('failed_jobs')
            ->orderByDesc('failed_at')
            ->paginate(20);

        // ジョブ名（payload の displayName）を抽出して各レコードに付与
        $jobs->getCollection()->transform(function ($job) {
            $job->display_name = $this->extractDisplayName($job->payload);
            return $job;
        });

        return view('failed_jobs.index', [
            'jobs' => $jobs,
        ]);
    }

    public function show(string $id): View
    {
        $job = DB::table('failed_jobs')->where('id', $id)->first();
        abort_if(! $job, 404);

        $payload = json_decode($job->payload, true);

        return view('failed_jobs.show', [
            'job' => $job,
            'payload' => $payload,
            'displayName' => $this->extractDisplayName($job->payload),
        ]);
    }

    public function destroy(string $id): RedirectResponse
    {
        DB::table('failed_jobs')->where('id', $id)->delete();

        return redirect()
            ->route('failed_jobs.index')
            ->with('status', "失敗ジョブ #{$id} を削除しました。");
    }

    public function destroyAll(): RedirectResponse
    {
        $count = DB::table('failed_jobs')->count();
        Artisan::call('queue:flush');

        return redirect()
            ->route('failed_jobs.index')
            ->with('status', "失敗ジョブを {$count} 件すべて削除しました。");
    }

    public function retry(string $id): RedirectResponse
    {
        Artisan::call('queue:retry', ['id' => [$id]]);

        return redirect()
            ->route('failed_jobs.index')
            ->with('status', "失敗ジョブ #{$id} を再実行キューに戻しました。");
    }

    /**
     * payload JSON から displayName（ジョブのクラス名）を抽出。
     */
    private function extractDisplayName(string $payload): string
    {
        $data = json_decode($payload, true);

        return $data['displayName'] ?? '(unknown)';
    }
}
