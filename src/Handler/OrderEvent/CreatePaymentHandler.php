<?php

namespace App\Handler\OrderEvent;

use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Order\OrderEventLog\OrderEvent\Payment;
use App\Domain\Entity\Order\OrderEventLog\OrderEventConstants;
use App\Domain\Entity\Order\OrderEventLog\OrderEventLog;
use App\Repository\Order\PaymentRepository;

class CreatePaymentHandler
{
    public function __construct(
        private PaymentRepository $repository,
    ) {
    }

    public function handle(\App\Domain\Entity\Order\OrderEventLog\OrderEvent\Payment $payment, Employee $employee)
    {
        $order = $payment->getOrder();
        $eventLog = new OrderEventLog(
            $employee,
            OrderEventConstants::EVENT_PAYMENT,
            $order,
            $payment->getComment(),
            $payment->getDocuments()
        );

        $payment->setOrderEventLog($eventLog);

        $this->repository->save($payment);
        
    }
}