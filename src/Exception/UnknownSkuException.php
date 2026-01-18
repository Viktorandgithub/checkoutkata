<?php

declare(strict_types=1);

namespace Checkout\Exception;

final class UnknownSkuException extends \InvalidArgumentException
{
    public static function forSku(string $sku): self
    {
        return new self("No pricing rule defined for SKU '{$sku}'");
    }
}

