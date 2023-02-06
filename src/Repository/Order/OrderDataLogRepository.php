<?php

namespace App\Repository\Order;

use App\Domain\Entity\Order\Order;
use App\Domain\Entity\Order\OrderEventLog\OrderDataLog;
use App\Repository\AbstractRepository;
use Doctrine\ORM\EntityManagerInterface;

class OrderDataLogRepository extends AbstractRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->repo = $entityManager->getRepository(OrderDataLog::class);
    }

    public function findLastPaymentDataLogByOrder(Order $order): ?OrderDataLog
    {
        return $this->repo->createQueryBuilder('l')
            ->andWhere('l.order = :order')
            ->setParameter('order', $order)
            ->andWhere('l.payment is not null')
            ->orderBy("l.id", "DESC")
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findLastDeliveryDataLogByOrder(Order $order): ?OrderDataLog
    {
        return $this->repo->createQueryBuilder('l')
            ->andWhere('l.order = :order')
            ->setParameter('order', $order)
            ->andWhere('l.delivery is not null')
            ->orderBy("l.id", "DESC")
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findLastProductsDataLogByOrder(Order $order): ?OrderDataLog
    {
        return $this->repo->createQueryBuilder('l')
            ->andWhere('l.order = :order')
            ->setParameter('order', $order)
            ->andWhere('l.products is not null')
            ->orderBy("l.id", "DESC")
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
