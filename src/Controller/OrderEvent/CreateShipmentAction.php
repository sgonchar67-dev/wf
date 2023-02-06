<?php

namespace App\Controller\OrderEvent;

use App\Controller\AbstractController;
use App\Domain\Entity\Order\OrderEventLog\OrderEvent\Invoice;
use App\Domain\Entity\Order\OrderEventLog\OrderEvent\Shipment;
use App\Domain\Entity\Order\OrderEventLog\OrderEventLog;
use App\Handler\OrderEvent\CreatePaymentHandler;
use App\Handler\OrderEvent\CreateShipmentHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CreateShipmentAction extends AbstractController
{
    public function __invoke(Shipment $data, CreateShipmentHandler $handler): Shipment
    {
        $handler->handle($data, $this->getEmployee());
        return $data;
    }
}