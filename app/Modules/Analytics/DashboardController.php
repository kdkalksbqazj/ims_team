<?php

namespace App\Modules\Analytics;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $totalProducts = Product::count();
            $totalBranches = Branch::count();
            $recentTransactions = Transaction::with(['product', 'branch', 'user'])->latest()->limit(5)->get();
            $lowStockItems = DB::table('product_branch')
                ->join('products', 'products.id', '=', 'product_branch.product_id')
                ->join('branches', 'branches.id', '=', 'product_branch.branch_id')
                ->whereColumn('stock_level', '<=', 'reorder_threshold')
                ->select('products.name as product_name', 'branches.name as branch_name', 'stock_level', 'reorder_threshold')
                ->get();
        } else {
            $branchId = $user->branch_id;
            $totalProducts = DB::table('product_branch')->where('branch_id', $branchId)->count();
            $totalBranches = 1; // Only their branch
            $recentTransactions = Transaction::where('branch_id', $branchId)->with(['product', 'user'])->latest()->limit(5)->get();
            $lowStockItems = DB::table('product_branch')
                ->join('products', 'products.id', '=', 'product_branch.product_id')
                ->where('branch_id', $branchId)
                ->whereColumn('stock_level', '<=', 'reorder_threshold')
                ->select('products.name as product_name', 'stock_level', 'reorder_threshold')
                ->get();
        }

        return view('modules.analytics.dashboard', compact('totalProducts', 'totalBranches', 'recentTransactions', 'lowStockItems'));
    }
}
