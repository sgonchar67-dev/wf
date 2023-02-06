<?php

namespace App\Service\Order\OrderDataLog\dto;

class OrderDataLogPaymentDto
{
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function toArray(): array
    {
        return (array) $this;
    }


}