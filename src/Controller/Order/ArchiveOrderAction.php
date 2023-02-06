<?php

namespace App\Controller\Order;

use App\Controller\AbstractController;
use App\Domain\Entity\Order\Order;
use App\Handler\Order\ArchiveOrderHandler;
use App\Service\Order\OrderService;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ArchiveOrderAction extends AbstractController
{
    public function __invoke(\App\Domain\Entity\Order\Order $data, ArchiveOrderHandler $handler): \App\Domain\Entity\Order\Order
    {
        $handler->handle($data, $this->getEmployee());
        return $data;
    }
}