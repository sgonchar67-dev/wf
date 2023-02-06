<?php

namespace App\Handler\Order;

use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Order\Order;
use App\Repository\Order\OrderRepository;

class NoteOrderHandler
{
    public function __construct(
        private OrderRepository $orderRepository,
    ) {
    }

    public function handle(Order $order, Employee $employee)
    {
        $this->orderRepository->save($order);
    }
}