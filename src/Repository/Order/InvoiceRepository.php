<?php

namespace App\Repository\Order;

use App\Domain\Entity\Order\OrderEventLog\OrderEvent\Invoice;
use App\Domain\Entity\Order\OrderEventLog\OrderEventLog;
use App\Repository\AbstractRepository;
use Doctrine\ORM\EntityManagerInterface;

class InvoiceRepository extends AbstractRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->repo = $entityManager->getRepository(Invoice::class);
    }

    public function findOneByOrderEventLog(OrderEventLog $orderEventLog): ?Invoice
    {
        return $this->repo->findOneBy([
            'orderEventLog' => $orderEventLog
        ]);
    }
}