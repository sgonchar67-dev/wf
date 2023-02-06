<?php

namespace App\Handler\Order;

use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Order\Order;
use App\Domain\Entity\Order\OrderEventLog\OrderEventConstants;
use App\Domain\Entity\Order\OrderEventLog\OrderEventLog;
use App\Domain\Entity\Order\OrderStatusConstants;
use Doctrine\ORM\EntityManagerInterface;

class SeenOrderHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function handle(Order $order, Employee $employee)
    {
        if ($order->getStatus() === OrderStatusConstants::STATUS_PLACED &&
            $order->getSupplierCompany() === $employee->getCompany()
        ) {
            $order->seen();

            $orderEventLog = OrderEventLog::create(
                $employee,
                OrderEventConstants::EVENT_SEEN,
                $order
            );
            $order->addOrderEventLog($orderEventLog);
            $this->entityManager->flush();
        }
    }
}