<?php

namespace App\Controller\OrderProduct;

use App\Controller\AbstractController;
use App\Domain\Entity\Order\OrderProduct\OrderProduct;
use App\Service\Order\OrderDataLog\OrderDataLogService;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class OrderProductAction extends AbstractController
{
    public function __invoke(OrderProduct $data, OrderDataLogService $orderDataLogService): OrderProduct
    {
        $orderDataLogService->logProductsChanges($data->getOrder(), $this->getEmployee());
        return $data;
    }
}