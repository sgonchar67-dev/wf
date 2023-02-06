<?php

namespace App\Controller\Order;

use App\Controller\AbstractController;
use App\Handler\Order\GetOrderStatusesHandler;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetOrderStatusesAction extends AbstractController
{
    public function __invoke(GetOrderStatusesHandler $handler): array
    {
        return $handler->handle($this->getEmployee());
    }
}