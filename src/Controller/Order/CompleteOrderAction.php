<?php

namespace App\Controller\Order;

use App\Controller\AbstractController;
use App\DTO\Order\OrderActionDto;
use App\Domain\Entity\Order\Order;
use App\Handler\Order\CompleteOrderHandler;
use App\Service\Order\OrderService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CompleteOrderAction extends AbstractController
{
    public function __invoke(\App\Domain\Entity\Order\Order $data, Request $request, CompleteOrderHandler $handler): Order
    {
        $dto = OrderActionDto::create()
            ->handleRequest($request);
        $handler->handle($data, $this->getEmployee(), $dto);
        return $data;
    }
}