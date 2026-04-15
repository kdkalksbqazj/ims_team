<?php

namespace App\Modules\Analytics\Services;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardMetricsService
{
    /**
     * Build dashboard data according to the signed-in user role scope.
     *
     * @return array<string, mixed>
     */
    public function getForUser(User $user): array
    {
        if ($user->isAdmin()) {
            return [
                'totalProducts' => Product::count(),
                'totalBranches' => Branch::count(),
                'recentTransactions' => Transaction::query()
                    ->with(['product:id,name', 'branch:id,name', 'user:id,name'])
                    ->latest('id')
                    ->limit(5)
                    ->get(),
                'lowStockItems' => $this->lowStockForAllBranches(),
            ];
        }

        $branchId = (int) $user->branch_id;

        return [
            'totalProducts' => DB::table('product_branch')->where('branch_id', $branchId)->count(),
            'totalBranches' => 1,
            'recentTransactions' => Transaction::query()
                ->where('branch_id', $branchId)
                ->with(['product:id,name', 'branch:id,name', 'user:id,name'])
                ->latest('id')
                ->limit(5)
                ->get(),
            'lowStockItems' => $this->lowStockForSingleBranch($branchId),
        ];
    }

    /**
     * @return Collection<int, object>
     */
    private function lowStockForAllBranches(): Collection
    {
        return DB::table('product_branch')
            ->join('products', 'products.id', '=', 'product_branch.product_id')
            ->join('branches', 'branches.id', '=', 'product_branch.branch_id')
            ->whereColumn('stock_level', '<=', 'reorder_threshold')
            ->select(
                'products.name as product_name',
                'branches.name as branch_name',
                'stock_level',
                'reorder_threshold'
            )
            ->orderBy('stock_level')
            ->get();
    }

    /**
     * @return Collection<int, object>
     */
    private function lowStockForSingleBranch(int $branchId): Collection
    {
        return DB::table('product_branch')
            ->join('products', 'products.id', '=', 'product_branch.product_id')
            ->where('branch_id', $branchId)
            ->whereColumn('stock_level', '<=', 'reorder_threshold')
            ->select(
                'products.name as product_name',
                'stock_level',
                'reorder_threshold'
            )
            ->orderBy('stock_level')
            ->get();
    }
}
