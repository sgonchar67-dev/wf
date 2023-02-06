<?php

namespace App\Controller\Order;

use App\Controller\AbstractController;
use App\Domain\Entity\Order\Order;
use App\Handler\Order\SupplierCreateOrderHandler;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class SupplierCreateOrderAction extends AbstractController
{
    public function __invoke(Order $data, SupplierCreateOrderHandler $handler): Order
    {
        $handler->handle($data, $this->getEmployee());
        return $data;
    }
}