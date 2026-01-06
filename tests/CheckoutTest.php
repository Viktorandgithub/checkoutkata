<?php

declare(strict_types=1);

namespace Checkout\Tests;

use Checkout\Checkout;
use Checkout\Pricing\BulkPricing;
use Checkout\Pricing\UnitPricing;
use Checkout\SKU;
use PHPUnit\Framework\TestCase;

final class CheckoutTest extends TestCase
{
    public function testEmptyBasketReturnsZero(): void
    {
        $co = new Checkout();
        
        $this->assertSame(0, $co->total());
    }

    public function testSingleItemA(): void
    {
        $rules = [
            'A' => new UnitPricing(50),
        ];
        $co = new Checkout($rules);
        
        $co->scan(new SKU('A'));
        
        $this->assertSame(50, $co->total());
    }

    public function testMultipleItems(): void
    {
        $rules = [
            'A' => new UnitPricing(50),
            'B' => new UnitPricing(30),
        ];
        $co = new Checkout($rules);
        
        $co->scan(new SKU('A'));
        $co->scan(new SKU('B'));
        
        $this->assertSame(80, $co->total());
    }

    public function testBulkDiscount(): void
    {
        $rules = [
            'A' => new BulkPricing(unitPrice: 50, bulkQty: 3, bulkPrice: 130),
        ];
        $co = new Checkout($rules);
        
        $co->scan(new SKU('A'));
        $co->scan(new SKU('A'));
        $co->scan(new SKU('A'));
        
        $this->assertSame(130, $co->total());
    }

    public function testBulkDiscountWithRemainder(): void
    {
        $rules = [
            'A' => new BulkPricing(unitPrice: 50, bulkQty: 3, bulkPrice: 130),
        ];
        $co = new Checkout($rules);
        
        // 4 A's = 130 + 50 = 180
        $co->scan(new SKU('A'));
        $co->scan(new SKU('A'));
        $co->scan(new SKU('A'));
        $co->scan(new SKU('A'));
        
        $this->assertSame(180, $co->total());
    }

    public function testMixedItemsWithDiscounts(): void
    {
        $rules = [
            'A' => new BulkPricing(unitPrice: 50, bulkQty: 3, bulkPrice: 130),
            'B' => new BulkPricing(unitPrice: 30, bulkQty: 2, bulkPrice: 45),
            'C' => new UnitPricing(20),
            'D' => new UnitPricing(15),
        ];
        $co = new Checkout($rules);
        
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
        $rules = [
            'A' => new BulkPricing(unitPrice: 50, bulkQty: 3, bulkPrice: 130),
            'B' => new BulkPricing(unitPrice: 30, bulkQty: 2, bulkPrice: 45),
            'C' => new UnitPricing(20),
            'D' => new UnitPricing(15),
        ];
        $co = new Checkout($rules);
        
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
        $rules = [
            'A' => new BulkPricing(unitPrice: 50, bulkQty: 3, bulkPrice: 130),
            'B' => new BulkPricing(unitPrice: 30, bulkQty: 2, bulkPrice: 45),
        ];
        $co = new Checkout($rules);
        
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

