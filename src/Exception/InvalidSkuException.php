<?php

declare(strict_types=1);

namespace Checkout\Exception;

final class InvalidSkuException extends \InvalidArgumentException
{
    public static function forValue(string $value): self
    {
        return new self("Invalid SKU '{$value}'. Expected a single uppercase letter A-Z.");
    }
}

