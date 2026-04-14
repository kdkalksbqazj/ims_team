<?php

namespace App\Listeners;

use App\Events\StockUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\Log;

class SendStockAlert
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(StockUpdated $event): void
    {
        $product = $event->product;
        $branch = $event->branch;

        $pivot = $product->branches()->where('branch_id', $branch->id)->first();

        if ($pivot && $pivot->pivot->stock_level <= $pivot->pivot->reorder_threshold) {
            Log::warning("Low stock alert: Product '{$product->name}' in branch '{$branch->name}' is at level {$pivot->pivot->stock_level}. Threshold is {$pivot->pivot->reorder_threshold}.");
            
            // In a real application, you would send an email or SMS here.
            // Notification::send($admins, new LowStockNotification($product, $branch, $pivot->pivot->stock_level));
        }
    }
}
