<?php

namespace App\Modules\Analytics\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Analytics\Services\ReportingService;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function __construct(private readonly ReportingService $reporting)
    {
    }

    public function index()
    {
        $user = Auth::user();
        $report = $this->reporting->getReport($user);

        return view('modules.analytics.reports.index', $report);
    }
}
