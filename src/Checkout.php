<?php

declare(strict_types=1);

namespace Checkout;

use Checkout\Pricing\Strategy;

final class Checkout
{
    /** @var array<string, int> */
    private array $cart = [];

    /**
     * @param array<string, Strategy> $rules
     */
    public function __construct(
        private readonly array $rules = []
    ) {}

    public function scan(SKU $sku): void
    {
        $key = $sku->value;
        $this->cart[$key] = ($this->cart[$key] ?? 0) + 1;
    }

    public function total(): int
    {
        $total = 0;
        
        foreach ($this->cart as $sku => $quantity) {
            if (isset($this->rules[$sku])) {
                $total += $this->rules[$sku]->calculate($quantity);
            }
        }
        
        return $total;
    }
}

