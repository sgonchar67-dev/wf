<?php

namespace App\Handler\OrderEvent;

use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Order\OrderEventLog\OrderEvent\Shipment;
use App\Domain\Entity\Order\OrderEventLog\OrderEventConstants;
use App\Domain\Entity\Order\OrderEventLog\OrderEventLog;
use App\Repository\Order\ShipmentRepository;

class CreateShipmentHandler
{
    public function __construct(
        private ShipmentRepository        $repository,
    ) {
    }

    public function handle(\App\Domain\Entity\Order\OrderEventLog\OrderEvent\Shipment $shipment, Employee $employee)
    {
        $order = $shipment->getOrder();
        foreach ($shipment->getItems() as $item) {
            $item->setShipment($shipment);
        }

        $eventLog = new OrderEventLog(
            $employee,
            OrderEventConstants::EVENT_SHIPMENT,
            $order,
            $shipment->getComment(),
            $shipment->getDocuments(),
        );

        $shipment->setOrderEventLog($eventLog);

        $this->repository->save($shipment);
    }
}