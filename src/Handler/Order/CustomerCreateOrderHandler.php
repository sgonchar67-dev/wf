<?php

namespace App\Handler\Order;

use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Order\Order;

use App\Repository\Order\OrderRepository;
use App\Service\Order\OrderNumberGenerator\OrderNumberGeneratorInterface;

class CustomerCreateOrderHandler
{
    public function __construct(
        private OrderNumberGeneratorInterface $orderNumberGenerator,
        private OrderRepository $orderRepository,
    ) {
    }

    public function handle(Order $order, Employee $employee)
    {
        $order->checkout($order->getCart(), $employee);
        $dto = $this->orderNumberGenerator->generate($order);
        $order->setShotNumber($dto->shotNumber)
            ->setNumber($dto->number);
        $this->orderRepository->save($order);
    }
}