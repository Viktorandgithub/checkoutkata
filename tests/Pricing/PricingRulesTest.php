<?php

declare(strict_types=1);

namespace Checkout\Tests\Pricing;

use Checkout\Basket;
use Checkout\Pricing\PricingRules;
use Checkout\Pricing\Contract\PricingRule;
use Checkout\SKU;
use PHPUnit\Framework\TestCase;

final class PricingRulesTest extends TestCase
{
    public function testTotalSumsAllStrategies(): void
    {
        $basket = new Basket();
        $basket->add(new SKU('A'));
        $basket->add(new SKU('B'));

        $rules = new PricingRules(
            new class implements PricingRule {
                public function sku(): SKU
                {
                    return new SKU('A');
                }

                public function apply(Basket $basket): int
                {
                    return $basket->quantity(new SKU('A')) * 10;
                }
            },
            new class implements PricingRule {
                public function sku(): SKU
                {
                    return new SKU('B');
                }

                public function apply(Basket $basket): int
                {
                    return $basket->quantity(new SKU('B')) * 5;
                }
            },
        );

        $this->assertSame(15, $rules->total($basket));
    }

    public function testTotalIgnoresSkusThatHaveNoRule(): void
    {
        $basket = new Basket();
        $basket->add(new SKU('A'));
        $basket->add(new SKU('Z')); // no rule for Z, engine just sums rules it knows about

        $rules = new PricingRules(
            new class implements PricingRule {
                public function sku(): SKU
                {
                    return new SKU('A');
                }

                public function apply(Basket $basket): int
                {
                    return $basket->quantity(new SKU('A')) * 10;
                }
            },
        );

        $this->assertSame(10, $rules->total($basket));
    }
}

