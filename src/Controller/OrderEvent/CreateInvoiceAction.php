<?php

namespace App\Controller\OrderEvent;

use App\Controller\AbstractController;
use App\Domain\Entity\Order\OrderEventLog\OrderEvent\Invoice;
use App\Domain\Entity\Order\OrderEventLog\OrderEventLog;
use App\Handler\OrderEvent\CreateInvoiceHandler;
use App\Handler\OrderEvent\CreateRefundHandler;
use App\Handler\OrderEvent\CreatePaymentHandler;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CreateInvoiceAction extends AbstractController
{
    public function __invoke(\App\Domain\Entity\Order\OrderEventLog\OrderEvent\Invoice $data, CreateInvoiceHandler $handler): Invoice
    {
        $handler->handle($data, $this->getEmployee());
        return $data;
    }
}