<?php

namespace App\Repository\Order;

use App\Domain\Entity\Order\OrderEventLog\OrderEvent\Payment;
use App\Domain\Entity\Order\OrderEventLog\OrderEvent\Shipment;
use App\Domain\Entity\Order\OrderEventLog\OrderEventLog;
use App\Repository\AbstractRepository;
use Doctrine\ORM\EntityManagerInterface;

class ShipmentRepository extends AbstractRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->repo = $entityManager->getRepository(Shipment::class);
    }


    public function findOneByOrderEventLog(OrderEventLog $orderEventLog): ?Shipment
    {
        return $this->repo->findOneBy([
            'orderEventLog' => $orderEventLog
        ]);
    }
}