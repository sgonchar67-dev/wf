<?php

namespace App\Controller\OrderProduct;

use App\Controller\AbstractController;
use App\Domain\Entity\Order\OrderProduct\OrderProduct;
use App\Service\Order\OrderDataLog\OrderDataLogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CreateOrderProductAction extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(OrderProduct $data, OrderDataLogService $orderDataLogService): OrderProduct
    {
        $data->getOrder()->addOrderProduct($data);
        $this->entityManager->persist($data);
        $this->entityManager->flush();
        $orderDataLogService->logProductsChanges($data->getOrder(), $this->getEmployee());
        return $data;
    }
}