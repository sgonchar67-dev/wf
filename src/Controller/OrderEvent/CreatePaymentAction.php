<?php

namespace App\Controller\OrderEvent;

use App\Controller\AbstractController;
use App\Domain\Entity\Order\OrderEventLog\OrderEvent\Payment;
use App\Handler\OrderEvent\CreatePaymentHandler;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CreatePaymentAction extends AbstractController
{
    public function __invoke(Payment $data, CreatePaymentHandler $handler): Payment
    {
        $handler->handle($data, $this->getEmployee());
        return $data;
    }

}