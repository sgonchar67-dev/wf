<?php

namespace App\Controller\Order;

use App\Controller\AbstractController;
use App\Domain\Entity\Order\Order;
use App\Handler\Order\PlaceOrderHandler;
use App\Service\Order\OrderService;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class SendOrderAction extends AbstractController
{
    public function __invoke(Order $data, PlaceOrderHandler $handler): Order
    {
        return $handler->handle($data, $this->getEmployee());
    }
}