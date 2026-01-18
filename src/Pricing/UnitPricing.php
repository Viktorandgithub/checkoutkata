<?php

declare(strict_types=1);

namespace Checkout\Pricing;

use Checkout\Basket;
use Checkout\Pricing\Contract\PricingRule;
use Checkout\SKU;

final readonly class UnitPricing implements PricingRule
{
    private SKU $sku;
    private int $price;

    public function __construct(
        SKU $sku,
        int $price
    ) {
        if ($price < 0) {
            throw new \InvalidArgumentException('price must be >= 0');
        }

        $this->sku = $sku;
        $this->price = $price;
    }

    public function sku(): SKU
    {
        return $this->sku;
    }

    public function apply(Basket $basket): int
    {
        $quantity = $basket->quantity($this->sku);
        return $this->price * $quantity;
    }
}

