<?php

declare(strict_types=1);

namespace Checkout\Tests\Pricing;

use Checkout\Basket;
use Checkout\Pricing\UnitPricing;
use Checkout\SKU;
use PHPUnit\Framework\TestCase;

final class UnitPricingTest extends TestCase
{
    public function testCalculatesUnitPriceTimesQuantity(): void
    {
        $basket = new Basket();
        $basket->add(new SKU('A'));
        $basket->add(new SKU('A'));
        $basket->add(new SKU('B')); // ignored

        $pricing = new UnitPricing(new SKU('A'), 50);

        $this->assertSame(100, $pricing->apply($basket));
    }

    public function testReturnsZeroWhenSkuNotInBasket(): void
    {
        $basket = new Basket();
        $basket->add(new SKU('B'));

        $pricing = new UnitPricing(new SKU('A'), 50);

        $this->assertSame(0, $pricing->apply($basket));
    }
}

