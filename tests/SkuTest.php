<?php

declare(strict_types=1);

namespace Checkout\Tests;

use Checkout\Exception\InvalidSkuException;
use Checkout\SKU;
use PHPUnit\Framework\TestCase;

final class SkuTest extends TestCase
{
    public function testAcceptsSingleUppercaseLetter(): void
    {
        $sku = new SKU('A');
        $this->assertSame('A', $sku->value);
        $this->assertSame('A', (string) $sku);
    }

    public function testRejectsEmptyString(): void
    {
        $this->expectException(InvalidSkuException::class);
        new SKU('');
    }

    public function testRejectsLowercase(): void
    {
        $this->expectException(InvalidSkuException::class);
        new SKU('a');
    }

    public function testRejectsMoreThanOneCharacter(): void
    {
        $this->expectException(InvalidSkuException::class);
        new SKU('AA');
    }

    public function testRejectsNonLetter(): void
    {
        $this->expectException(InvalidSkuException::class);
        new SKU('1');
    }
}

