<?php

declare(strict_types=1);

namespace Checkout;

interface CheckoutInterface
{
    public function scan(SKU $sku): void;

    /**
     * Returns the total price for the currently scanned items.
     *
     * @throws \InvalidArgumentException
     */
    public function total(): int;
}

