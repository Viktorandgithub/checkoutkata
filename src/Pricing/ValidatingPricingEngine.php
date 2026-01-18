<?php

declare(strict_types=1);

namespace Checkout\Pricing;

use Checkout\Basket;
use Checkout\Pricing\Contract\PricingEngine;
use Checkout\Pricing\Contract\BasketValidator;

/**
 * Decorator that validates the basket's SKUs before delegating total calculation.
 */
final readonly class ValidatingPricingEngine implements PricingEngine
{
    public function __construct(
        private BasketValidator $validator,
        private PricingEngine $inner
    ) {}

    public function total(Basket $basket): int
    {
        $this->validator->validate($basket);
        return $this->inner->total($basket);
    }
}

