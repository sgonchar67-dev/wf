<?php

namespace App\Handler\Order;

use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Order\Order;
use App\Domain\Entity\Order\OrderEventLog\OrderEventConstants;
use App\Domain\Entity\Order\OrderEventLog\OrderEventLog;
use App\Repository\Cart\CartRepository;
use App\Repository\Order\OrderEventLogRepository;
use App\Repository\Order\OrderRepository;
use App\Service\Order\OrderDataLog\OrderDataLogService;
use App\Service\Order\OrderNumberGenerator\OrderNumberGeneratorInterface;

class PlaceOrderHandler
{
    public function __construct(
        private OrderNumberGeneratorInterface $orderNumberGenerator,
        private OrderRepository $orderRepository,
        private CartRepository  $cartRepository,
        private OrderDataLogService $orderDataLogService,
    ) {
    }

    public function handle(Order $order, Employee $employee): Order
    {
        $order->place();
        $dto = $this->orderNumberGenerator->generate($order);
        $order->setShotNumber($dto->shotNumber)
            ->setNumber($dto->number);

        if ($order->getCart()?->isClosed()) {
            $this->cartRepository->remove($order->getCart());
        }

        $orderEventLog = OrderEventLog::create(
            $employee,
            OrderEventConstants::EVENT_SEND,
            $order,
        );

        $order->addOrderEventLog($orderEventLog);

        if (!$order->getLastDataLog()) {
            $this->orderDataLogService->firstLogByEvent($orderEventLog);
        }

        $this->orderRepository->save($order);

        return $order;
    }
}