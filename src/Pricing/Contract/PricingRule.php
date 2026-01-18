<?php

declare(strict_types=1);

namespace Checkout\Pricing\Contract;

use Checkout\Basket;
use Checkout\SKU;

/**
 * Returns the rule's contribution to the overall total.
 */
interface PricingRule
{
    public function sku(): SKU;

    public function apply(Basket $basket): int;
}

