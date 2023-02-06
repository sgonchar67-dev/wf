<?php

namespace App\Controller\Order;

use App\Controller\AbstractController;
use App\Domain\Entity\Order\Order;
use App\Handler\Order\CheckoutOrderHandler;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CheckoutOrderAction extends AbstractController
{
    public function __invoke(Order $data, CheckoutOrderHandler $handler): Order
    {
        $handler->handle($data, $this->getEmployee());
        return $data;
    }
}