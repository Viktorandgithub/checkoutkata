# Kata09: Back to the Checkout

Eine PHP-Implementierung des Supermarkt-Checkout-Katas.

## Anforderungen

- PHP 8.2+
- Composer

## Installation

```bash
composer install
```

## Nutzung als Library (in einem anderen Projekt)

Wenn du dieses Paket in einem anderen Projekt verwenden willst, ist es ein ganz normales Composer-Paket (PSR-4, Namespace `Checkout\\`).

### Option A: per Pfad (lokal, ohne Repository)

In der `composer.json` deines Zielprojekts:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "../checkoutkata"
    }
  ],
  "require": {
    "kata/checkout": "*"
  }
}
```

Dann:

```bash
composer update kata/checkout
```

## Tests ausführen

```bash
./vendor/bin/phpunit
```

## Architektur

```
src/
├── Basket.php              # Zählt gescannte SKUs (Mengen pro SKU)
├── SKU.php                 # Value Object für Artikelnummern
├── Checkout.php            # Hauptklasse für den Checkout
├── CheckoutInterface.php   # Interface für DI/Mocks in Consumer-Code
├── CheckoutFactory.php     # Convenience Factory: baut Checkout aus Pricing Rules
├── Exception/
│   ├── InvalidSkuException.php
│   └── UnknownSkuException.php
└── Pricing/
    ├── PricingRules.php            # Aggregiert Regeln und berechnet total
    ├── ValidatingPricingEngine.php # Decorator: validiert Basket, delegiert dann an PricingEngine
    ├── AllowedSkusValidator.php    # Validator: alle SKUs im Basket müssen in den Rules existieren
    ├── Contract/
    │   ├── PricingEngine.php  # Interface für Pricing-Engines (total(Basket))
    │   └── PricingRule.php    # Domain-Name für Preisregeln
    ├── UnitPricing.php     # Einfache Stückpreise
    ├── BulkPricing.php     # Mengenrabatte ("3 für 130")
    └── BuyNOfXGetYFreePricing.php  # Cross-SKU Promo ("buy N of X, get Y of sku free")
```

## Design-Entscheidungen

### Pricing Rules
Der Checkout kennt keine konkreten Preisregeln — nur `PricingRule`/`PricingEngine`. Neue Preismodelle können hinzugefügt werden, ohne bestehenden Code zu ändern (Open/Closed Principle).

### Unknown/Invalid SKU Verhalten
- `SKU` validiert Eingaben (A–Z). Ungültige Werte werfen `InvalidSkuException`.
- Wenn im Basket eine SKU ohne Pricing Rule landet, wirft `total()` eine `UnknownSkuException` (via `AllowedSkusValidator`/`ValidatingPricingEngine`).

### Immutable Objects
Alle Pricing-Klassen sind `readonly` — einmal erstellt, können sie nicht mehr verändert werden. Das verhindert Bugs durch unerwartete Zustandsänderungen.

### Value Object (SKU)
Die SKU ist ein eigener Typ statt nur `string`. Das erhöht die Typsicherheit und macht den Code ausdrucksstärker.

## Beispiel

```php
use Checkout\CheckoutInterface;
use Checkout\CheckoutFactory;
use Checkout\Pricing\BulkPricing;
use Checkout\Pricing\UnitPricing;
use Checkout\SKU;

// In Consumer-Code (DI) kannst du gegen das Interface typisieren:
/** @var CheckoutInterface $checkout */
$checkout = CheckoutFactory::fromRules(
    new BulkPricing(new SKU('A'), unitPrice: 50, bulkQty: 3, bulkPrice: 130),
    new BulkPricing(new SKU('B'), unitPrice: 30, bulkQty: 2, bulkPrice: 45),
    new UnitPricing(new SKU('C'), 20),
    new UnitPricing(new SKU('D'), 15),
);
$checkout->scan(new SKU('A'));
$checkout->scan(new SKU('A'));
$checkout->scan(new SKU('A'));
$checkout->scan(new SKU('B'));

echo $checkout->total(); // 160 (130 + 30)
```

