<?php

namespace App\Controller\Order;

use App\Controller\AbstractController;
use App\Domain\Entity\Order\Order;
use App\Handler\Order\CustomerCreateOrderHandler;
use App\Handler\Order\SupplierCreateOrderHandler;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CustomerCreateOrderAction extends AbstractController
{

    public function __invoke(Order $data, CustomerCreateOrderHandler $handler): Order
    {
        $handler->handle($data, $this->getEmployee());
        return $data;
    }
}