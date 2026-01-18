<?php

declare(strict_types=1);

namespace Checkout\Pricing;

use Checkout\Basket;
use Checkout\Pricing\Contract\PricingRule;
use Checkout\SKU;

final readonly class BulkPricing implements PricingRule
{
    private SKU $sku;
    private int $unitPrice;
    private int $bulkQty;
    private int $bulkPrice;

    public function __construct(
        SKU $sku,
        int $unitPrice,
        int $bulkQty,
        int $bulkPrice
    ) {
        if ($unitPrice < 0) {
            throw new \InvalidArgumentException('unitPrice must be >= 0');
        }
        if ($bulkQty <= 0) {
            throw new \InvalidArgumentException('bulkQty must be >= 1');
        }
        if ($bulkPrice < 0) {
            throw new \InvalidArgumentException('bulkPrice must be >= 0');
        }

        $this->sku = $sku;
        $this->unitPrice = $unitPrice;
        $this->bulkQty = $bulkQty;
        $this->bulkPrice = $bulkPrice;
    }

    public function sku(): SKU
    {
        return $this->sku;
    }

    public function apply(Basket $basket): int
    {
        $quantity = $basket->quantity($this->sku);
        $bulkSets = intdiv($quantity, $this->bulkQty);
        $remainder = $quantity % $this->bulkQty;
        
        return $bulkSets * $this->bulkPrice + $remainder * $this->unitPrice;
    }
}

