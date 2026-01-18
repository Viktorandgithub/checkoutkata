<?php

declare(strict_types=1);

namespace Checkout;

use Checkout\Pricing\AllowedSkusValidator;
use Checkout\Pricing\Contract\PricingRule;
use Checkout\Pricing\PricingRules;
use Checkout\Pricing\ValidatingPricingEngine;

/**
 * Convenience factory for building a fully configured Checkout instance from pricing rules.
 *
 * This is purely ergonomic: consumers can still wire things manually against interfaces if desired.
 */
final class CheckoutFactory
{
    public static function fromRules(PricingRule ...$rules): CheckoutInterface
    {
        $pricingRules = new PricingRules(...$rules);
        $validator = new AllowedSkusValidator($pricingRules->ruleSkus());
        $engine = new ValidatingPricingEngine($validator, $pricingRules);

        return new Checkout($engine);
    }
}

