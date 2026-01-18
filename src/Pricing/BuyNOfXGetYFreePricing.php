<?php

declare(strict_types=1);

namespace Checkout\Pricing;

use Checkout\Basket;
use Checkout\Pricing\Contract\PricingRule;
use Checkout\SKU;

/**
 * Cross-SKU discount: for every N units of trigger SKU in the basket, M units of $sku are free.
 *
 * Example: triggerSku = 'A', triggerQty = 2, sku = 'B', freeQtyPerSet = 1, unitPrice = 30
 * - Basket: A=2, B=1 => B total = 0
 * - Basket: A=2, B=2 => B total = 30
 */
final readonly class BuyNOfXGetYFreePricing implements PricingRule
{
    private SKU $sku;
    private SKU $triggerSku;
    private int $triggerQty;
    private int $unitPrice;
    private int $freeQtyPerSet;

    public function __construct(
        SKU $sku,
        SKU $triggerSku,
        int $triggerQty,
        int $unitPrice,
        int $freeQtyPerSet = 1
    ) {
        if ($triggerQty <= 0) {
            throw new \InvalidArgumentException('triggerQty must be >= 1');
        }
        if ($unitPrice < 0) {
            throw new \InvalidArgumentException('unitPrice must be >= 0');
        }
        if ($freeQtyPerSet < 0) {
            throw new \InvalidArgumentException('freeQtyPerSet must be >= 0');
        }

        $this->sku = $sku;
        $this->triggerSku = $triggerSku;
        $this->triggerQty = $triggerQty;
        $this->unitPrice = $unitPrice;
        $this->freeQtyPerSet = $freeQtyPerSet;
    }

    public function sku(): SKU
    {
        return $this->sku;
    }

    public function apply(Basket $basket): int
    {
        $qty = $basket->quantity($this->sku);
        $sets = intdiv($basket->quantity($this->triggerSku), $this->triggerQty);
        $eligibleFree = $sets * $this->freeQtyPerSet;

        $chargeable = max(0, $qty - $eligibleFree);

        return $chargeable * $this->unitPrice;
    }
}

