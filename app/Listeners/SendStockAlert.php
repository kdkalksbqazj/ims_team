<?php

namespace App\Listeners;

use App\Events\StockUpdated;
use App\Modules\Analytics\Alerts\Listeners\SendStockAlert as ModuleSendStockAlert;

class SendStockAlert
{
    public function __construct(private readonly ModuleSendStockAlert $moduleListener)
    {
    }

    /**
     * Compatibility adapter: delegates to module listener.
     */
    public function handle(StockUpdated $event): void
    {
        $this->moduleListener->handle($event);
    }
}
