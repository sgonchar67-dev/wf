<?php

namespace App\Controller\Order;

use App\Controller\AbstractController;
use App\Domain\Entity\Order\Order;
use App\Handler\Order\SeenOrderHandler;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class SeenOrderAction extends AbstractController
{
    public function __invoke(Order $data, SeenOrderHandler $handler): Order
    {
        $handler->handle($data, $this->getEmployee());
        return $data;
    }
}