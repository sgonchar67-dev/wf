<?php

namespace App\Service\Order\OrderNumberGenerator;

use App\Domain\Entity\Order\Order;
use App\Repository\Order\OrderRepository;
use App\Service\Order\OrderNumberGenerator\dto\OrderNumber;

class OrderNumberGenerator implements OrderNumberGeneratorInterface
{
    public function __construct(private OrderRepository $orderRepository )
    {
    }

    public function generate(Order $order): OrderNumber
    {
        if ($order->getContractor()) {
            $lastOrder = $this->orderRepository->findLastNumberedByContractor($order->getContractor());
            $shotNumber = $lastOrder?->getShotNumber() + 1;
            $number = "{$order->getContractor()->getId()}-{$shotNumber}";
        } elseif ($order->getCustomerCompany()) {
            $lastOrder = $this->orderRepository->findLastNumberedByCustomer($order->getCustomerCompany());
            $shotNumber = $lastOrder?->getShotNumber() + 1;
            $number = "{$order->getCustomerCompany()->getId()}-B-{$shotNumber}";
        } else {
            $lastOrder = $this->orderRepository->findLastNumberedDraftBySupplier($order->getSupplierCompany());
            $shotNumber = $lastOrder?->getShotNumber() + 1;
            $number = "Draft-{$shotNumber}";
        }

        return new OrderNumber($shotNumber, $number);
    }
}