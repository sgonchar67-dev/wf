<?php

namespace App\Handler\OrderEvent;

use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Order\OrderEventLog\OrderEvent\Refund;
use App\Domain\Entity\Order\OrderEventLog\OrderEventConstants;
use App\Domain\Entity\Order\OrderEventLog\OrderEventLog;
use App\Repository\Order\RefundRepository;

class CreateRefundHandler
{
    public function __construct(
        private RefundRepository $repository,
    ) {
    }

    public function handle(Refund $invoice, Employee $employee)
    {
        $order = $invoice->getOrder();
        $eventLog = new OrderEventLog(
            $employee,
            OrderEventConstants::EVENT_REFUND,
            $order,
            $invoice->getComment(),
            $invoice->getDocuments()
        );

        $invoice->setOrderEventLog($eventLog);

        $this->repository->save($invoice);
    }
}