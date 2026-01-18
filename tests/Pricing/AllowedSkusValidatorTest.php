<?php

declare(strict_types=1);

namespace Checkout\Tests\Pricing;

use Checkout\Basket;
use Checkout\Exception\UnknownSkuException;
use Checkout\Pricing\AllowedSkusValidator;
use Checkout\SKU;
use PHPUnit\Framework\TestCase;

final class AllowedSkusValidatorTest extends TestCase
{
    public function testAllowsOnlyConfiguredSkus(): void
    {
        $basket = new Basket();
        $basket->add(new SKU('A'));
        $basket->add(new SKU('B'));

        $validator = new AllowedSkusValidator([new SKU('A'), new SKU('B')]);
        $validator->validate($basket);

        $this->assertTrue(true);
    }

    public function testThrowsForUnknownSku(): void
    {
        $basket = new Basket();
        $basket->add(new SKU('Z'));

        $validator = new AllowedSkusValidator([new SKU('A')]);

        $this->expectException(UnknownSkuException::class);
        $validator->validate($basket);
    }
}

