<?php

declare(strict_types=1);

namespace Checkout\Pricing\Contract;

use Checkout\Basket;

/**
 * Computes the total price of a basket given a configured set of pricing rules.
 */
interface PricingEngine
{
    public function total(Basket $basket): int;
}

