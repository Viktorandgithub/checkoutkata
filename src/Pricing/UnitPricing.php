<?php

declare(strict_types=1);

namespace Checkout\Pricing;

final readonly class UnitPricing implements Strategy
{
    public function __construct(
        private int $price
    ) {}

    public function calculate(int $quantity): int
    {
        return $this->price * $quantity;
    }
}

