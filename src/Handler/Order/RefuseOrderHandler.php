<?php

namespace App\Handler\Order;

use App\DTO\Order\OrderActionDto;
use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Order\Order;
use App\Domain\Entity\Order\OrderEventLog\OrderEventConstants;
use App\Domain\Entity\Order\OrderEventLog\OrderEventLog;
use App\Repository\Order\OrderEventLogRepository;

class RefuseOrderHandler
{
    public function __construct(
        private OrderEventLogRepository $orderEventLogRepository,
    ) {
    }

    public function handle(Order $order, Employee $employee, OrderActionDto $dto)
    {
        $order->refuse();

        $eventLog = OrderEventLog::create(
            $employee,
            OrderEventConstants::EVENT_REFUSE,
            $order,
            $dto->comment,
            $dto->documents
        );
        $this->orderEventLogRepository->save($eventLog);
    }
}