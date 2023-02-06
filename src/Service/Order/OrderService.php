<?php

namespace App\Service\Order;

use App\DTO\Order\OrderActionDto;
use App\Domain\Entity\Order\Order;
use App\Handler\Order\ArchiveOrderHandler;
use App\Handler\Order\CheckoutOrderHandler;
use App\Handler\Order\CompleteOrderHandler;
use App\Handler\Order\CustomerCreateOrderHandler;
use App\Handler\Order\SupplierCreateOrderHandler;
use App\Handler\Order\CancelOrderHandler;
use App\Handler\Order\ConfirmOrderHandler;
use App\Handler\Order\NotifyOrderHandler;
use App\Handler\Order\PlaceOrderHandler;
use App\Handler\Order\RefuseOrderHandler;
use App\Handler\Order\SeenOrderHandler;
use App\Repository\Cart\CartRepository;
use App\Repository\DocumentRepository;
use App\Repository\EmployeeRepository;
use App\Repository\Order\OrderEventLogRepository;
use App\Repository\Order\OrderRepository;


class OrderService
{
    public function __construct(
        private CartRepository             $cartRepository,
        private OrderRepository            $orderRepository,
        private EmployeeRepository         $employeeRepository,
        private DocumentRepository         $documentRepository,
        private SupplierCreateOrderHandler $supplierCreateOrderHandler,
        private CustomerCreateOrderHandler $customerCreateOrderHandler,
        private SeenOrderHandler           $seeOrderHandler,
        private PlaceOrderHandler          $placeOrderHandler,
        private CheckoutOrderHandler       $checkoutOrderHandler,
        private ConfirmOrderHandler        $confirmOrderHandler,
        private RefuseOrderHandler         $refuseOrderHandler,
        private CancelOrderHandler         $cancelOrderHandler,
        private CompleteOrderHandler       $completeOrderHandler,
        private ArchiveOrderHandler        $archiveOrderHandler,
        private NotifyOrderHandler         $notifyOrderHandler,
    ) {
    }

    public function customerCreate($cartId, $employeeId): Order
    {
        $cart = $this->cartRepository->get($cartId);
        $employee = $this->employeeRepository->get($employeeId);
        $order = Order::costumerCreate($cart, $employee);

        $this->customerCreateOrderHandler->handle($order, $employee);
        return $order;
    }

    public function supplierCreate($employeeId): Order
    {
        $employee = $this->employeeRepository->get($employeeId);
        $order = Order::supplierCreate($employee->getCompany(), $employee);

        $this->supplierCreateOrderHandler->handle($order, $employee);
        return $order;
    }

    public function checkout($orderId, $employeeId): Order
    {
        $order = $this->orderRepository->get($orderId);
        $employee = $this->employeeRepository->get($employeeId);

        $this->checkoutOrderHandler->handle($order, $employee);
        return $order;
    }

    public function place($orderId, $employeeId): Order
    {
        $employee = $this->employeeRepository->get($employeeId);
        $order = $this->orderRepository->get($orderId);

        return $this->placeOrderHandler->handle($order, $employee);
    }

    public function seen($orderId, $employeeId): Order
    {
        $order = $this->orderRepository->get($orderId);
        $employee = $this->employeeRepository->get($employeeId);
        $this->seeOrderHandler->handle($order, $employee);

        return $order;
    }

    /** cancel order execution */
    public function cancel($orderId, $employeeId, OrderActionDto $dto): Order
    {
        $order = $this->orderRepository->get($orderId);
        $employee = $this->employeeRepository->get($employeeId);
        $dto->documents = $this->documentRepository->findByIds($dto->documents);
        $this->cancelOrderHandler->handle($order, $employee, $dto);

        return $order;
    }

    /** Reject order */
    public function refuse($orderId, $employeeId, OrderActionDto $dto): Order
    {
        $order = $this->orderRepository->get($orderId);
        $employee = $this->employeeRepository->get($employeeId);
        $dto->documents = $this->documentRepository->findByIds($dto->documents);
        
        $this->refuseOrderHandler->handle($order, $employee, $dto);
        
        return $order;
    }

    public function complete($orderId, $employeeId, OrderActionDto $dto): Order
    {
        $order = $this->orderRepository->get($orderId);
        $employee = $this->employeeRepository->get($employeeId);
        $dto->documents = $this->documentRepository->findByIds($dto->documents);

        $this->completeOrderHandler->handle($order, $employee, $dto);
        
        return $order;
    }

    public function archive(?int $orderId, int $employeeId): Order
    {
        $order = $this->orderRepository->get($orderId);
        $employee = $this->employeeRepository->get($employeeId);

        $this->archiveOrderHandler->handle($order, $employee);
        return $order;
    }

    public function confirm($orderId, $employeeId, OrderActionDto $dto): Order
    {
        $order = $this->orderRepository->get($orderId);
        $employee = $this->employeeRepository->get($employeeId);
        $dto->documents = $this->documentRepository->findByIds($dto->documents);

        $this->confirmOrderHandler->handle($order, $employee, $dto);
        
        return $order;
    }

    public function notify($orderId, $employeeId, OrderActionDto $dto): Order
    {
        $order = $this->orderRepository->get($orderId);
        $employee = $this->employeeRepository->get($employeeId);
        $dto->documents = $this->documentRepository->findByIds($dto->documents);

        $this->notifyOrderHandler->handle($order, $employee, $dto);

        return $order;
    }
}