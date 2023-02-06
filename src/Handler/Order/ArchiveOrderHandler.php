<?php

namespace App\Handler\Order;

use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Order\Order;
use App\Repository\Order\OrderRepository;

class ArchiveOrderHandler
{
    public function __construct(
        private OrderRepository $orderRepository,
    ) {
    }

    public function handle(\App\Domain\Entity\Order\Order $order, Employee $employee)
    {
        $order->archive($employee);
        $this->orderRepository->save($order);
    }
}