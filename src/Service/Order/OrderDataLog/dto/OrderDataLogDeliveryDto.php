<?php

namespace App\Service\Order\OrderDataLog\dto;

class OrderDataLogDeliveryDto
{
    public string $name;
    public ?float $price = null;

    /**
     * @param string $name
     * @param float|null $price
     */
    public function __construct(string $name, ?float $price)
    {
        $this->name = $name;
        $this->price = $price;
    }

    public function toArray(): array
    {
        return (array) $this;
    }


}