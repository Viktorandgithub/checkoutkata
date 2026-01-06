# Kata09: Back to the Checkout

Eine PHP-Implementierung des Supermarkt-Checkout-Katas.

## Anforderungen

- PHP 8.2+
- Composer

## Installation

```bash
composer install
```

## Tests ausführen

```bash
./vendor/bin/phpunit
```

## Architektur

```
src/
├── SKU.php                 # Value Object für Artikelnummern
├── Checkout.php            # Hauptklasse für den Checkout
└── Pricing/
    ├── Strategy.php        # Interface für Preisstrategien
    ├── UnitPricing.php     # Einfache Stückpreise
    └── BulkPricing.php     # Mengenrabatte ("3 für 130")
```

## Design-Entscheidungen

### Strategy Pattern
Der Checkout kennt keine konkreten Preisregeln — nur das `Strategy`-Interface. Neue Preismodelle können hinzugefügt werden, ohne bestehenden Code zu ändern (Open/Closed Principle).

### Immutable Objects
Alle Pricing-Klassen sind `readonly` — einmal erstellt, können sie nicht mehr verändert werden. Das verhindert Bugs durch unerwartete Zustandsänderungen.

### Value Object (SKU)
Die SKU ist ein eigener Typ statt nur `string`. Das erhöht die Typsicherheit und macht den Code ausdrucksstärker.

## Beispiel

```php
use Checkout\Checkout;
use Checkout\Pricing\BulkPricing;
use Checkout\Pricing\UnitPricing;
use Checkout\SKU;

$rules = [
    'A' => new BulkPricing(unitPrice: 50, bulkQty: 3, bulkPrice: 130),
    'B' => new BulkPricing(unitPrice: 30, bulkQty: 2, bulkPrice: 45),
    'C' => new UnitPricing(20),
    'D' => new UnitPricing(15),
];

$checkout = new Checkout($rules);
$checkout->scan(new SKU('A'));
$checkout->scan(new SKU('A'));
$checkout->scan(new SKU('A'));
$checkout->scan(new SKU('B'));

echo $checkout->total(); // 160 (130 + 30)
```

## Was ich mit mehr Zeit noch umsetzen würde

- Fehlerbehandlung für unbekannte SKUs
- Validierung der Eingabewerte
- Weitere Strategien: "Kaufe 2, bekomme 1 gratis"
- Tabellengesteuerte Tests für alle Kata-Testfälle
- PHPStan auf Level max

