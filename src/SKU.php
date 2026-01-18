<?php

declare(strict_types=1);

namespace Checkout;

use Checkout\Exception\InvalidSkuException;

final readonly class SKU
{
    public function __construct(
        public string $value
    ) {
        if (!preg_match('/^[A-Z]$/', $this->value)) {
            throw InvalidSkuException::forValue($this->value);
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

