<?php

namespace App\Modules\Analytics\Alerts\Listeners;

use App\Events\StockUpdated;
use Illuminate\Support\Facades\Log;

class SendStockAlert
{
    /**
     * Handle stock updates and emit low-stock alert logs.
     */
    public function handle(StockUpdated $event): void
    {
        $product = $event->product;
        $branch = $event->branch;

        $pivot = $product->branches()->where('branch_id', $branch->id)->first();

        if ($pivot && $pivot->pivot->stock_level <= $pivot->pivot->reorder_threshold) {
            Log::warning(
                "Low stock alert: Product '{$product->name}' in branch '{$branch->name}' is at level {$pivot->pivot->stock_level}. Threshold is {$pivot->pivot->reorder_threshold}."
            );
        }
    }
}
