<?php

namespace App\Service\Order\OrderDataLog;

use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Order\Order;
use App\Domain\Entity\Order\OrderEventLog\OrderDataLog;
use App\Domain\Entity\Order\OrderEventLog\OrderEventConstants;
use App\Domain\Entity\Order\OrderEventLog\OrderEventLog;
use App\Helper\ArrayHelper;
use App\Repository\Order\OrderDataLogRepository;
use App\Service\Order\OrderDataLog\dto\OrderDataLogDeliveryDto;
use App\Service\Order\OrderDataLog\dto\OrderDataLogPaymentDto;
use App\Service\Order\OrderDataLog\dto\OrderDataLogProductDto;
use Doctrine\ORM\EntityManagerInterface;

class OrderDataLogService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private OrderDataLogRepository $orderDataLogRepository,
    ) {
    }

    public function firstLogByEvent(OrderEventLog $orderEventLog): OrderDataLog
    {
        $order = $orderEventLog->getOrder();
        $deliveryDto = new OrderDataLogDeliveryDto(
            $order->getDelivery()?->getName(),
            $order->getDeliveryPrice(),
        );
        $paymentDto = new OrderDataLogPaymentDto($order->getPayment()->getName());
        $productsData = [];
        foreach ($order->getOrderProducts() as $orderProduct) {
//            $key = "{$orderProduct->getProduct()->getId()}:{$orderProduct->getProductPackage()->getId()}";
            $key = $orderProduct->getId();
            $productsData[$key] = OrderDataLogProductDto::createFromOrderProduct($orderProduct);
        }
        $orderDataLog = OrderDataLog::createByEventLog($orderEventLog)
            ->setDelivery($deliveryDto)
            ->setPayment($paymentDto)
            ->setProducts($productsData)
        ;

        $this->entityManager->persist($orderDataLog);
        $this->entityManager->flush();
        return $orderDataLog;
    }

    public function logDeliveryChanges(Order $order, Employee $employee): ?OrderDataLog
    {
        if (!$this->canLog($order, $employee)) {
            return null;
        }

        $deliveryDto = new OrderDataLogDeliveryDto(
            $order->getDelivery()?->getName(),
            $order->getDeliveryPrice(),
        );

        $lastDeliveryData = $this->orderDataLogRepository->findLastDeliveryDataLogByOrder($order)?->getDelivery();
        if (ArrayHelper::compare($deliveryDto, $lastDeliveryData)) {
            return null;
        }

        $orderDataLog = $this->getOrCreateOrderDataLog($order, $employee);

        $orderDataLog->setDelivery($deliveryDto);
        $orderDataLog->getOrderEventLog()
            ->setSeenByCustomer(false)
            ->setCreatedAt()
        ;
        $order->addOrderDataLog($orderDataLog);

        $this->entityManager->flush();
        return $orderDataLog;
    }

    public function logPaymentChanges(Order $order, Employee $employee): ?OrderDataLog
    {
        if (!$this->canLog($order, $employee)) {
            return null;
        }

        $paymentDto = new OrderDataLogPaymentDto($order->getPayment()->getName());
        $lastPaymentData = $this->orderDataLogRepository->findLastPaymentDataLogByOrder($order)?->getPayment();
        if (ArrayHelper::compare($paymentDto, $lastPaymentData)) {
            return null;
        }

        $orderDataLog = $this->getOrCreateOrderDataLog($order, $employee);

        $orderDataLog->setPayment($paymentDto);
        $orderDataLog->getOrderEventLog()
            ->setSeenByCustomer(false)
            ->setCreatedAt()
        ;
        $order->addOrderDataLog($orderDataLog);

        $this->entityManager->flush();
        return $orderDataLog;
    }

    public function logProductsChanges(Order $order, Employee $employee): ?OrderDataLog
    {
        if (!$this->canLog($order, $employee)) {
            return null;
        }

        $productsData = [];
        foreach ($order->getOrderProducts() as $orderProduct) {
//            $key = "{$orderProduct->getProduct()->getId()}:{$orderProduct->getProductPackage()->getId()}";
            $key = $orderProduct->getId();
            $productsData[$key] = OrderDataLogProductDto::createFromOrderProduct($orderProduct);
        }

        $lastProductsData = $this->orderDataLogRepository->findLastProductsDataLogByOrder($order)?->getProducts();
        if (ArrayHelper::compare($productsData, $lastProductsData)) {
            return null;
        }

        $orderDataLog = $this->getOrCreateOrderDataLog($order, $employee);

        $orderDataLog->setProducts($productsData);
        $orderDataLog->getOrderEventLog()
            ->setSeenByCustomer(false)
            ->setCreatedAt()
        ;
        $order->addOrderDataLog($orderDataLog);

        $this->entityManager->flush();
        return $orderDataLog;
    }

    private function getOrCreateOrderDataLog(Order $order, Employee $employee): OrderDataLog
    {
        $lastDataLog = $order->getLastDataLog();
        $lastEventLog = $order->getLastEventLog();

        if ($lastDataLog &&
            $lastEventLog === $lastDataLog->getOrderEventLog() &&
            $lastEventLog->getEvent() === OrderEventConstants::EVENT_EDIT &&
            $employee === $lastEventLog->getEmployee()
        ) {
            $orderDataLog = $lastDataLog;
        } else {
            $orderEventLog = OrderEventLog::create(
                $employee,
                OrderEventConstants::EVENT_EDIT,
                $order,
            );

            $orderDataLog = OrderDataLog::createByEventLog($orderEventLog)
                ->setDelivery($lastDataLog->getDelivery())
                ->setPayment($lastDataLog->getPayment())
                ->setProducts($lastDataLog->getProducts())
            ;
        }

        return $orderDataLog;
    }

    private function canLog(Order $order, Employee $employee): bool
    {
        return $order->getPlacedAt()
            && $order->getLastDataLog()
            && $employee->getCompany() === $order->getSupplierCompany()
        ;
    }
}