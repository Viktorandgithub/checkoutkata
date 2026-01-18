<?php

declare(strict_types=1);

namespace Checkout\Tests;

use Checkout\Basket;
use Checkout\SKU;
use PHPUnit\Framework\TestCase;

final class BasketTest extends TestCase
{
    public function testNewBasketHasNoItems(): void
    {
        $basket = new Basket();

        $this->assertSame([], $basket->quantities());
        $this->assertSame(0, $basket->quantity(new SKU('A')));
    }

    public function testAddingSkuIncrementsQuantity(): void
    {
        $basket = new Basket();

        $basket->add(new SKU('A'));

        $this->assertSame(['A' => 1], $basket->quantities());
        $this->assertSame(1, $basket->quantity(new SKU('A')));
    }

    public function testAddingSameSkuTwiceIncrementsToTwo(): void
    {
        $basket = new Basket();

        $basket->add(new SKU('A'));
        $basket->add(new SKU('A'));

        $this->assertSame(['A' => 2], $basket->quantities());
        $this->assertSame(2, $basket->quantity(new SKU('A')));
    }
}

