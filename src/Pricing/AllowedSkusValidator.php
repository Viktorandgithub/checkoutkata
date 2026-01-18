<?php

declare(strict_types=1);

namespace Checkout\Pricing;

use Checkout\Basket;
use Checkout\Exception\UnknownSkuException;
use Checkout\Pricing\Contract\BasketValidator;
use Checkout\SKU;

/**
 * Validates that all SKUs currently present in the basket are known/allowed.
 */
final readonly class AllowedSkusValidator implements BasketValidator
{
    /** @var array<string, true> */
    private array $allowed;

    /**
     * @param list<SKU> $allowedSkus
     */
    public function __construct(array $allowedSkus)
    {
        $allowed = [];
        foreach ($allowedSkus as $sku) {
            $allowed[$sku->value] = true;
        }
        $this->allowed = $allowed;
    }

    public function validate(Basket $basket): void
    {
        foreach ($basket->quantities() as $sku => $_quantity) {
            if (!isset($this->allowed[$sku])) {
                throw UnknownSkuException::forSku($sku);
            }
        }
    }
}

