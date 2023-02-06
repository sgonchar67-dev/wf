<?php

namespace App\Controller\Order;

use App\Controller\AbstractController;
use App\DTO\Order\OrderActionDto;
use App\Domain\Entity\Order\Order;
use App\Handler\Order\NotifyOrderHandler;
use App\Service\Order\OrderService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class NotifyOrderAction extends AbstractController
{
    public function __invoke(Order $data, Request $request, NotifyOrderHandler $handler): \App\Domain\Entity\Order\Order
    {
        $dto = OrderActionDto::create()
            ->handleRequest($request);
        $handler->handle($data, $this->getEmployee(), $dto);
        return $data;
    }
}