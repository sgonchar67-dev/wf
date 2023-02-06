<?php

namespace App\Controller\OrderEvent;

use App\Controller\AbstractController;
use App\Domain\Entity\Order\OrderEventLog\OrderEvent\Invoice;
use App\Domain\Entity\Order\OrderEventLog\OrderEvent\Shipment;
use App\Domain\Entity\Order\OrderEventLog\OrderEvent\ShipmentItem;
use App\Domain\Entity\Order\OrderEventLog\OrderEventLog;
use App\Handler\OrderEvent\CreatePaymentHandler;
use App\Handler\OrderEvent\AddShipmentItemHandler;
use App\Handler\OrderEvent\dto\CreateShipmentItem;
use App\Helper\RequestHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class AddShipmentItemAction extends AbstractController
{
    /**
     * @throws \App\Exception\NotFoundException
     * @throws \App\Exception\AccessDeniedException
     */
    public function __invoke(Shipment $data, Request $request, AddShipmentItemHandler $handler)
    {
        $dto = CreateShipmentItem::createFromRequest($request);
        $handler->handle($data, $dto, $this->getEmployee());
        return $data;
    }
}