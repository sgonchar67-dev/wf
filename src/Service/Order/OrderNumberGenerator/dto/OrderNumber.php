<?php

namespace App\Service\Order\OrderNumberGenerator\dto;

class OrderNumber
{
    public int $shotNumber;
    public string $number;

    /**
     * @param int $shotNumber
     * @param string $number
     */
    public function __construct(int $shotNumber, string $number)
    {
        $this->shotNumber = $shotNumber;
        $this->number = $number;
    }


}