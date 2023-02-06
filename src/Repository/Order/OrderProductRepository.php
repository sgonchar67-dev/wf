<?php

namespace App\Repository\Order;

use App\Domain\Entity\Order\OrderProduct\OrderProduct;
use App\Repository\AbstractRepository;
use Doctrine\ORM\EntityManagerInterface;

class OrderProductRepository extends AbstractRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->repo = $entityManager->getRepository(OrderProduct::class);
    }
}