<?php

namespace App\Handler\OrderEvent;

use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Order\OrderEventLog\OrderEvent\Shipment;
use App\Domain\Entity\Order\OrderEventLog\OrderEvent\ShipmentItem;
use App\Handler\OrderEvent\dto\CreateShipmentItem;
use App\Repository\Order\ShipmentRepository;

class AddShipmentItemHandler
{
    public function __construct(
        private ShipmentRepository $repository,
    ) {
    }

    public function handle(\App\Domain\Entity\Order\OrderEventLog\OrderEvent\Shipment $shipment, CreateShipmentItem $dto, Employee $employee)
    {
        $order = $shipment->getOrder();
        $orderProduct = $order->findOrderProductById($dto->orderProduct);
        $shipmentItem = new \App\Domain\Entity\Order\OrderEventLog\OrderEvent\ShipmentItem($orderProduct, $dto->count);
        $shipment->addItem($shipmentItem);
        $this->repository->save($shipment);
    }
}