<?php

declare(strict_types=1);

namespace Checkout;

final readonly class SKU
{
    public function __construct(
        public string $value
    ) {}

    public function __toString(): string
    {
        return $this->value;
    }
}

