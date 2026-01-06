<?php

declare(strict_types=1);

namespace Checkout\Pricing;

final readonly class BulkPricing implements Strategy
{
    public function __construct(
        private int $unitPrice,
        private int $bulkQty,
        private int $bulkPrice
    ) {}

    public function calculate(int $quantity): int
    {
        $bulkSets = intdiv($quantity, $this->bulkQty);
        $remainder = $quantity % $this->bulkQty;
        
        return $bulkSets * $this->bulkPrice + $remainder * $this->unitPrice;
    }
}

