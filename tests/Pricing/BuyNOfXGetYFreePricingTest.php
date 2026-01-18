<?php

declare(strict_types=1);

namespace Checkout\Tests\Pricing;

use Checkout\Basket;
use Checkout\Pricing\BuyNOfXGetYFreePricing;
use Checkout\SKU;
use PHPUnit\Framework\TestCase;

final class BuyNOfXGetYFreePricingTest extends TestCase
{
    public function testMakesSomeItemsFreeBasedOnOtherSkuQuantity(): void
    {
        $basket = new Basket();
        // trigger A=2, target B=2 => 1 B free => 1 B paid => 30
        $basket->add(new SKU('A'));
        $basket->add(new SKU('A'));
        $basket->add(new SKU('B'));
        $basket->add(new SKU('B'));

        $pricing = new BuyNOfXGetYFreePricing(new SKU('B'), triggerSku: new SKU('A'), triggerQty: 2, unitPrice: 30);

        $this->assertSame(30, $pricing->apply($basket));
    }

    public function testReturnsZeroWhenTargetSkuNotInBasket(): void
    {
        $basket = new Basket();
        $basket->add(new SKU('A'));
        $basket->add(new SKU('A'));

        $pricing = new BuyNOfXGetYFreePricing(new SKU('B'), triggerSku: new SKU('A'), triggerQty: 2, unitPrice: 30);

        $this->assertSame(0, $pricing->apply($basket));
    }

    public function testThrowsWhenTriggerQtyIsZeroOrLess(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new BuyNOfXGetYFreePricing(new SKU('B'), triggerSku: new SKU('A'), triggerQty: 0, unitPrice: 30);
    }

    public function testThrowsWhenFreeQtyPerSetIsNegative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new BuyNOfXGetYFreePricing(new SKU('B'), triggerSku: new SKU('A'), triggerQty: 2, unitPrice: 30, freeQtyPerSet: -1);
    }
}

