<?php

namespace App\Handler\Order;

use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Order\Order;
use App\Repository\Order\OrderRepository;

class CheckoutOrderHandler
{
    public function __construct(
        private OrderRepository $orderRepository,
    ) {
    }

    public function handle(Order $order, Employee $employee)
    {
        foreach ($order->getOrderProducts() as $orderProduct) {
            $this->orderRepository->remove($orderProduct);
        }
        $order->checkout($order->getCart(), $employee);
        $this->orderRepository->save($order);
    }
}