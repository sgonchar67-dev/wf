<?php

namespace App\Handler\Order;

use App\DTO\Order\OrderActionDto;
use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Order\Order;
use App\Domain\Entity\Order\OrderEventLog\OrderEventConstants;
use App\Domain\Entity\Order\OrderEventLog\OrderEventLog;
use App\Repository\Order\OrderEventLogRepository;

class CancelOrderHandler
{
    public function __construct(
        private OrderEventLogRepository $orderEventLogRepository,
    ) {
    }

    public function handle(\App\Domain\Entity\Order\Order $order, Employee $employee, OrderActionDto $dto)
    {
        $order->cancel();

        $eventLog = OrderEventLog::create(
            $employee,
            OrderEventConstants::EVENT_CANCEL,
            $order,
            $dto->comment,
            $dto->documents
        );
        $this->orderEventLogRepository->save($eventLog);
    }
}