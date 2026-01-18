<?php

declare(strict_types=1);

namespace Checkout;

final class Basket
{
    /** @var array<string, int> */
    private array $items = [];

    public function add(SKU $sku): void
    {
        $key = $sku->value;
        $this->items[$key] = ($this->items[$key] ?? 0) + 1;
    }

    public function quantity(SKU $sku): int
    {
        return $this->items[$sku->value] ?? 0;
    }

    /** @return array<string, int> */
    public function quantities(): array
    {
        return $this->items;
    }
}

