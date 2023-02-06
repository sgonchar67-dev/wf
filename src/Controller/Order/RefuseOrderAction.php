<?php

namespace App\Controller\Order;

use App\Controller\AbstractController;
use App\DTO\Order\OrderActionDto;
use App\Domain\Entity\Order\Order;
use App\Handler\Order\RefuseOrderHandler;
use App\Service\Order\OrderService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class RefuseOrderAction extends AbstractController
{
    public function __invoke(\App\Domain\Entity\Order\Order $data, Request $request, RefuseOrderHandler $handler): \App\Domain\Entity\Order\Order
    {
        $dto = OrderActionDto::create()
            ->handleRequest($request);
        $handler->handle($data, $this->getEmployee(), $dto);
        return $data;
    }
}