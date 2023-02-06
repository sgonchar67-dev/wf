<?php

namespace App\Repository\Order;

use App\Domain\Entity\Order\OrderEventLog\OrderEvent\Payment;
use App\Domain\Entity\Order\OrderEventLog\OrderEvent\Shipment;
use App\Domain\Entity\Order\OrderEventLog\OrderEvent\ShipmentItem;
use App\Repository\AbstractRepository;
use Doctrine\ORM\EntityManagerInterface;

class ShipmentItemRepository extends AbstractRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->repo = $entityManager->getRepository(ShipmentItem::class);
    }
}