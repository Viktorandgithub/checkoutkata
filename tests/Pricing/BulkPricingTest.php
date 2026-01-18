<?php

declare(strict_types=1);

namespace Checkout\Tests\Pricing;

use Checkout\Basket;
use Checkout\Pricing\BulkPricing;
use Checkout\SKU;
use PHPUnit\Framework\TestCase;

final class BulkPricingTest extends TestCase
{
    public function testAppliesBulkPriceAndRemainder(): void
    {
        $basket = new Basket();
        // 4 A's => 130 + 50 = 180
        $basket->add(new SKU('A'));
        $basket->add(new SKU('A'));
        $basket->add(new SKU('A'));
        $basket->add(new SKU('A'));

        $pricing = new BulkPricing(new SKU('A'), unitPrice: 50, bulkQty: 3, bulkPrice: 130);

        $this->assertSame(180, $pricing->apply($basket));
    }

    public function testReturnsZeroWhenSkuNotInBasket(): void
    {
        $basket = new Basket();
        $basket->add(new SKU('B'));

        $pricing = new BulkPricing(new SKU('A'), unitPrice: 50, bulkQty: 3, bulkPrice: 130);

        $this->assertSame(0, $pricing->apply($basket));
    }
}

