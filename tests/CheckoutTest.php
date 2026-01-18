<?php

declare(strict_types=1);

namespace Checkout\Tests;

use Checkout\CheckoutFactory;
use Checkout\Exception\UnknownSkuException;
use Checkout\Pricing\BuyNOfXGetYFreePricing;
use Checkout\Pricing\BulkPricing;
use Checkout\Pricing\UnitPricing;
use Checkout\SKU;
use PHPUnit\Framework\TestCase;

final class CheckoutTest extends TestCase
{
    /** @return list<\Checkout\Pricing\Contract\PricingRule> */
    private function kataRules(): array
    {
        return [
            new BulkPricing(new SKU('A'), unitPrice: 50, bulkQty: 3, bulkPrice: 130),
            new BulkPricing(new SKU('B'), unitPrice: 30, bulkQty: 2, bulkPrice: 45),
            new UnitPricing(new SKU('C'), 20),
            new UnitPricing(new SKU('D'), 15),
        ];
    }

    private function price(string $goods): int
    {
        $co = CheckoutFactory::fromRules(...$this->kataRules());
        foreach (str_split($goods) as $item) {
            $co->scan(new SKU($item));
        }
        return $co->total();
    }

    public function testKataTotals(): void
    {
        $this->assertSame(0, $this->price(""));
        $this->assertSame(50, $this->price("A"));
        $this->assertSame(80, $this->price("AB"));
        $this->assertSame(115, $this->price("CDBA"));

        $this->assertSame(100, $this->price("AA"));
        $this->assertSame(130, $this->price("AAA"));
        $this->assertSame(180, $this->price("AAAA"));
        $this->assertSame(230, $this->price("AAAAA"));
        $this->assertSame(260, $this->price("AAAAAA"));

        $this->assertSame(160, $this->price("AAAB"));
        $this->assertSame(175, $this->price("AAABB"));
        $this->assertSame(190, $this->price("AAABBD"));
        $this->assertSame(190, $this->price("DABABA"));
    }

    public function testThrowsIfScannedSkuHasNoPricingRule(): void
    {
        $co = CheckoutFactory::fromRules(
            new UnitPricing(new SKU('A'), 50),
        );
        $co->scan(new SKU('A'));
        $co->scan(new SKU('Z'));

        $this->expectException(UnknownSkuException::class);
        $co->total();
    }

    public function testCrossSkuDiscountBuyTwoAGetOneBFree(): void
    {
        $co = CheckoutFactory::fromRules(
            new UnitPricing(new SKU('A'), 50),
            // For every 2 'A' scanned, one 'B' is free.
            new BuyNOfXGetYFreePricing(new SKU('B'), triggerSku: new SKU('A'), triggerQty: 2, unitPrice: 30),
        );
        $co->scan(new SKU('A'));
        $co->scan(new SKU('A'));
        $co->scan(new SKU('B'));

        // Basket A=2, B=1 => B is free => 100
        $this->assertSame(100, $co->total());

        $co->scan(new SKU('B'));
        // Basket A=2, B=2 => one B free, one paid => 100 + 30 = 130
        $this->assertSame(130, $co->total());

        // Order independence.
        $co2 = CheckoutFactory::fromRules(
            new UnitPricing(new SKU('A'), 50),
            new BuyNOfXGetYFreePricing(new SKU('B'), triggerSku: new SKU('A'), triggerQty: 2, unitPrice: 30),
        );
        $co2->scan(new SKU('B'));
        $co2->scan(new SKU('B'));
        $co2->scan(new SKU('A'));
        $co2->scan(new SKU('A'));
        $this->assertSame(130, $co2->total());
    }

    public function testEmptyBasketReturnsZero(): void
    {
        $co = CheckoutFactory::fromRules();
        
        $this->assertSame(0, $co->total());
    }

    public function testSingleItemA(): void
    {
        $co = CheckoutFactory::fromRules(
            new UnitPricing(new SKU('A'), 50),
        );
        
        $co->scan(new SKU('A'));
        
        $this->assertSame(50, $co->total());
    }

    public function testMultipleItems(): void
    {
        $co = CheckoutFactory::fromRules(
            new UnitPricing(new SKU('A'), 50),
            new UnitPricing(new SKU('B'), 30),
        );
        
        $co->scan(new SKU('A'));
        $co->scan(new SKU('B'));
        
        $this->assertSame(80, $co->total());
    }

    public function testBulkDiscount(): void
    {
        $co = CheckoutFactory::fromRules(
            new BulkPricing(new SKU('A'), unitPrice: 50, bulkQty: 3, bulkPrice: 130),
        );
        
        $co->scan(new SKU('A'));
        $co->scan(new SKU('A'));
        $co->scan(new SKU('A'));
        
        $this->assertSame(130, $co->total());
    }

    public function testBulkDiscountWithRemainder(): void
    {
        $co = CheckoutFactory::fromRules(
            new BulkPricing(new SKU('A'), unitPrice: 50, bulkQty: 3, bulkPrice: 130),
        );
        
        // 4 A's = 130 + 50 = 180
        $co->scan(new SKU('A'));
        $co->scan(new SKU('A'));
        $co->scan(new SKU('A'));
        $co->scan(new SKU('A'));
        
        $this->assertSame(180, $co->total());
    }

    public function testMixedItemsWithDiscounts(): void
    {
        $co = CheckoutFactory::fromRules(
            new BulkPricing(new SKU('A'), unitPrice: 50, bulkQty: 3, bulkPrice: 130),
            new BulkPricing(new SKU('B'), unitPrice: 30, bulkQty: 2, bulkPrice: 45),
            new UnitPricing(new SKU('C'), 20),
            new UnitPricing(new SKU('D'), 15),
        );
        
        // AAABBD = 130 + 45 + 15 = 190
        $co->scan(new SKU('A'));
        $co->scan(new SKU('A'));
        $co->scan(new SKU('A'));
        $co->scan(new SKU('B'));
        $co->scan(new SKU('B'));
        $co->scan(new SKU('D'));
        
        $this->assertSame(190, $co->total());
    }

    public function testOrderIndependence(): void
    {
        $co = CheckoutFactory::fromRules(
            new BulkPricing(new SKU('A'), unitPrice: 50, bulkQty: 3, bulkPrice: 130),
            new BulkPricing(new SKU('B'), unitPrice: 30, bulkQty: 2, bulkPrice: 45),
            new UnitPricing(new SKU('C'), 20),
            new UnitPricing(new SKU('D'), 15),
        );
        
        // DABABA should equal AAABBD = 190
        $co->scan(new SKU('D'));
        $co->scan(new SKU('A'));
        $co->scan(new SKU('B'));
        $co->scan(new SKU('A'));
        $co->scan(new SKU('B'));
        $co->scan(new SKU('A'));
        
        $this->assertSame(190, $co->total());
    }

    public function testIncrementalScanning(): void
    {
        $co = CheckoutFactory::fromRules(
            new BulkPricing(new SKU('A'), unitPrice: 50, bulkQty: 3, bulkPrice: 130),
            new BulkPricing(new SKU('B'), unitPrice: 30, bulkQty: 2, bulkPrice: 45),
        );
        
        $this->assertSame(0, $co->total());
        
        $co->scan(new SKU('A'));
        $this->assertSame(50, $co->total());
        
        $co->scan(new SKU('B'));
        $this->assertSame(80, $co->total());
        
        $co->scan(new SKU('A'));
        $this->assertSame(130, $co->total());
        
        $co->scan(new SKU('A'));
        $this->assertSame(160, $co->total());
        
        $co->scan(new SKU('B'));
        $this->assertSame(175, $co->total());
    }
}

