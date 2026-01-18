<?php

declare(strict_types=1);

namespace Checkout;

use Checkout\Pricing\Contract\PricingEngine;

final class Checkout implements CheckoutInterface
{
    private Basket $basket;
    private readonly PricingEngine $pricingEngine;

    public function __construct(
        PricingEngine $pricingEngine
    ) {
        $this->basket = new Basket();
        $this->pricingEngine = $pricingEngine;
    }

    public function scan(SKU $sku): void
    {
        $this->basket->add($sku);
    }

    public function total(): int
    {
        return $this->pricingEngine->total($this->basket);
    }
}

