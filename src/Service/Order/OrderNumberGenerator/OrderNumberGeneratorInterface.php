<?php

namespace App\Service\Order\OrderNumberGenerator;

use App\Domain\Entity\Order\Order;
use App\Service\Order\OrderNumberGenerator\dto\OrderNumber;

interface OrderNumberGeneratorInterface
{
    public function generate(Order $order): OrderNumber;
}