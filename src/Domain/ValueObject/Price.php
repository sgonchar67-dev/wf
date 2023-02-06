<?php


namespace App\Domain\ValueObject;

use JetBrains\PhpStorm\Pure;

class Price
{
    private string $currency = 'RUB';

    private float $value = 0;

    /**
     * Price constructor.
     * @param $currency
     * @param $value
     */
    public function __construct($currency, $value)
    {
        $this->currency = $currency;
        $this->value = (float) $value;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    #[Pure] public function getPriceWithDiscount($discount): float
    {
        $price = $this->getValue();
        $priceWithDiscount = $price * (100 - $discount) / 100;
        return round($priceWithDiscount, 2);
    }

    #[Pure] public function getDiscount($discount)
    {
        $price = $this->getValue();
        $priceWithDiscount = $price * $discount / 100;
        return round($priceWithDiscount, 2);
    }
}
