<?php

namespace App\Modules\Analytics\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportingService
{
    /**
     * Build reporting payload based on role scope.
     *
     * @return array<string, mixed>
     */
    public function getReport(User $user): array
    {
        $base = Transaction::query();

        if (!$user->isAdmin()) {
            $base->where('branch_id', $user->branch_id);
        }

        $summary = [
            'total_transactions' => (clone $base)->count(),
            'stock_in_quantity' => (clone $base)->where('type', 'in')->sum('quantity'),
            'stock_out_quantity' => (clone $base)->where('type', 'out')->sum('quantity'),
            'transferred_quantity' => (clone $base)->where('type', 'transfer')->sum('quantity'),
            'adjusted_quantity' => (clone $base)->where('type', 'adjustment')->sum('quantity'),
        ];

        $daily = $this->dailyTrend($user);
        $topProducts = $this->topProducts($user);

        return [
            'summary' => $summary,
            'daily' => $daily,
            'topProducts' => $topProducts,
        ];
    }

    /**
     * @return Collection<int, object>
     */
    private function dailyTrend(User $user): Collection
    {
        $query = Transaction::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupByRaw('DATE(created_at)')
            ->orderByRaw('DATE(created_at) DESC')
            ->limit(7);

        if (!$user->isAdmin()) {
            $query->where('branch_id', $user->branch_id);
        }

        return $query->get();
    }

    /**
     * @return Collection<int, object>
     */
    private function topProducts(User $user): Collection
    {
        $query = DB::table('transactions')
            ->join('products', 'products.id', '=', 'transactions.product_id')
            ->selectRaw('products.name as product_name, COUNT(*) as uses, SUM(transactions.quantity) as total_qty')
            ->groupBy('products.name')
            ->orderByDesc('uses')
            ->limit(5);

        if (!$user->isAdmin()) {
            $query->where('transactions.branch_id', $user->branch_id);
        }

        return $query->get();
    }
}
