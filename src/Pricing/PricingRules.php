<?php

declare(strict_types=1);

namespace Checkout\Pricing;

use Checkout\Basket;
use Checkout\Pricing\Contract\PricingEngine;
use Checkout\Pricing\Contract\PricingRule;
use Checkout\SKU;

/**
 * Pricing engine used by Checkout: aggregates pricing rules and sums their contributions.
 *
 */
final readonly class PricingRules
    implements PricingEngine
{
    /** @var array<string, PricingRule> */
    private array $rules;

    public function __construct(PricingRule ...$rules)
    {
        $bySku = [];
        foreach ($rules as $rule) {
            $key = $rule->sku()->value;
            if (isset($bySku[$key])) {
                throw new \InvalidArgumentException("Duplicate pricing rule for SKU '{$key}'");
            }
            $bySku[$key] = $rule;
        }

        $this->rules = $bySku;
    }

    /** @return list<SKU> */
    public function ruleSkus(): array
    {
        $skus = [];
        foreach (array_keys($this->rules) as $sku) {
            $skus[] = new SKU($sku);
        }

        return $skus;
    }

    public function total(Basket $basket): int
    {
        $total = 0;
        foreach ($this->rules as $rule) {
            $total += $rule->apply($basket);
        }

        return $total;
    }
}

