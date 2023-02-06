<?php

namespace App\Service\Order;

use App\Domain\Entity\Contractor\Contractor;
use App\Repository\Order\OrderRepository;

class OrderUpdater
{
    public function __construct(
        private OrderRepository $orderRepository,
    ) {
    }

    public function updateByContractor(Contractor $contractor): void
    {
        $orders = $this->orderRepository->findOrdersWithoutCustomerCompanyByContractor($contractor);
        foreach ($orders as $order) {
            $order->setCustomerCompany($contractor->getContractorCompany());
        }
        $orders = $this->orderRepository->findOrdersWithoutContractorByCustomerCompany($contractor->getContractorCompany());
        foreach ($orders as $order) {
            $order->setContractor($contractor);
        }
    }
}