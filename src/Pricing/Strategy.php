<?php

declare(strict_types=1);

namespace Checkout\Pricing;

interface Strategy
{
    public function calculate(int $quantity): int;
}

