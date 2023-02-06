<?php

namespace App\Repository\Order;

use App\Domain\Entity\Order\OrderEventLog\OrderEventLog;
use App\Repository\AbstractRepository;
use Doctrine\ORM\EntityManagerInterface;

class OrderEventLogRepository extends AbstractRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->repo = $entityManager->getRepository(OrderEventLog::class);
    }
}
