<?php

namespace App\Http\Controllers;

use App\Modules\Analytics\Controllers\DashboardController as ModuleDashboardController;

class DashboardController extends Controller
{
    public function __construct(private readonly ModuleDashboardController $moduleDashboardController)
    {
    }

    public function index()
    {
        return $this->moduleDashboardController->index();
    }
}
