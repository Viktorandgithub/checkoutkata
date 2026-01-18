<?php

declare(strict_types=1);

namespace Checkout\Pricing\Contract;

use Checkout\Basket;

interface BasketValidator
{
    /**
     * @throws \InvalidArgumentException
     */
    public function validate(Basket $basket): void;
}

