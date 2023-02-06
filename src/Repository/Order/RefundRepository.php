<?php

namespace App\Repository\Order;

use App\Domain\Entity\Order\OrderEventLog\OrderEvent\Refund;
use App\Domain\Entity\Order\OrderEventLog\OrderEventLog;
use App\Repository\AbstractRepository;
use Doctrine\ORM\EntityManagerInterface;

class RefundRepository extends AbstractRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->repo = $entityManager->getRepository(Refund::class);
    }

    public function findOneByOrderEventLog(OrderEventLog $orderEventLog): ?Refund
    {
        return $this->repo->findOneBy([
            'orderEventLog' => $orderEventLog
        ]);
    }
}