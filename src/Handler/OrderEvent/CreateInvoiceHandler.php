<?php

namespace App\Handler\OrderEvent;

use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Order\OrderEventLog\OrderEvent\Invoice;
use App\Domain\Entity\Order\OrderEventLog\OrderEventConstants;
use App\Domain\Entity\Order\OrderEventLog\OrderEventLog;
use App\Repository\Order\RefundRepository;

class CreateInvoiceHandler
{
    public function __construct(
        private RefundRepository $repository,
    ) {
    }

    public function handle(Invoice $invoice, Employee $employee)
    {
        $order = $invoice->getOrder();
        $eventLog = new OrderEventLog(
            $employee,
            OrderEventConstants::EVENT_BILLING,
            $order,
            $invoice->getComment(),
            $invoice->getDocuments()
        );

        $invoice->setOrderEventLog($eventLog);

        $this->repository->save($invoice);
    }
}