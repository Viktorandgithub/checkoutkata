<?php

declare(strict_types=1);

namespace Checkout\Tests;

use Checkout\CheckoutFactory;
use Checkout\Exception\UnknownSkuException;
use Checkout\Pricing\BulkPricing;
use Checkout\Pricing\UnitPricing;
use Checkout\SKU;
use PHPUnit\Framework\TestCase;

final class CheckoutFactoryTest extends TestCase
{
    public function testBuildsConfiguredCheckoutAndComputesTotal(): void
    {
        $checkout = CheckoutFactory::fromRules(
            new BulkPricing(new SKU('A'), unitPrice: 50, bulkQty: 3, bulkPrice: 130),
            new UnitPricing(new SKU('B'), 30),
        );

        $checkout->scan(new SKU('A'));
        $checkout->scan(new SKU('A'));
        $checkout->scan(new SKU('A'));
        $checkout->scan(new SKU('B'));

        $this->assertSame(160, $checkout->total());
    }

    public function testThrowsWhenScannedSkuHasNoPricingRule(): void
    {
        $checkout = CheckoutFactory::fromRules(
            new UnitPricing(new SKU('A'), 50),
        );

        $checkout->scan(new SKU('A'));
        $checkout->scan(new SKU('Z'));

        $this->expectException(UnknownSkuException::class);
        $checkout->total();
    }
}

