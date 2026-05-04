<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard', [
            'clientsCount' => Client::count(),
            'scheduledCount' => Post::where('status', Post::STATUS_SCHEDULED)->count(),
            'todayPostedCount' => Post::where('status', Post::STATUS_POSTED)
                ->whereDate('updated_at', today())
                ->count(),
            'failedJobsCount' => DB::table('failed_jobs')->count(),
        ]);
    }
}
