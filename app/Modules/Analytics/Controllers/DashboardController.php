<?php

namespace App\Modules\Analytics\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Analytics\Services\DashboardMetricsService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardMetricsService $metrics)
    {
    }

    public function index()
    {
        $user = Auth::user();
        $payload = $this->metrics->getForUser($user);

        return view('modules.analytics.dashboard.index', $payload);
    }
}
