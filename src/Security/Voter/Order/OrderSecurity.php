<?php

namespace App\Security\Voter\Order;

use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Order\Order;
use App\Domain\Entity\User\UserPermissionConstants;
use App\Domain\Entity\User\UserRolesConstants;
use App\Security\SecurityInterface;
use App\Service\Order\OrderStateMachine\OrderActions;
use App\Service\Order\OrderStateMachine\OrderStateMachine;
use App\Service\User\PermissionChecker;
use JetBrains\PhpStorm\Pure;

class OrderSecurity
{
    public function __construct(
        private SecurityInterface $security,
    ) {
    }

    private function checkAction(Order $order, string $action): bool
    {
        $employee = $this->security->getUser()->getEmployee();
        $stateMachine = new OrderStateMachine($order);
        return $stateMachine->can($employee->getCompany(), $action);
    }

    public function isGrantedToAction(Order $order, $action): bool
    {
        return $this->isGrantedToOrder($order)
            && $this->checkAction($order, $action);
    }

    public function isGrantedToActionAsCustomer(Order $order, $action): bool
    {
        return $this->isGrantedAsCustomer($order)
            && $this->checkAction($order, $action);
    }

    public function isGrantedToActionAsSupplier(Order $order, $action): bool
    {
        return $this->isGrantedAsSupplier($order)
            && $this->checkAction($order, $action);
    }


    public function isGrantedAsCustomer(Order $order): bool
    {
        return $this->security->isGranted(UserRolesConstants::ROLE_ADMIN_PURCHASES)
            && $this->isCustomer($order);
    }

    public function isGrantedAsSupplier(Order $order): bool
    {
        return $this->security->isGranted(UserRolesConstants::ROLE_ADMIN_SALES)
            && $this->isSupplier($order);
    }

    private function getCompany(): Company
    {
        return $this->security->getUser()->getEmployeeCompany();
    }

    private function isGrantedToOrder(Order $order): bool
    {
        return $this->isGrantedAsCustomer($order)
            || $this->isGrantedAsSupplier($order);
    }

    private function isCustomer(Order $order): bool
    {
        return $this->getCompany() === $order->getCustomerCompany()
            || $this->getCompany() === $order->getCart()?->getCustomerCompany();
    }

    private function isSupplier(Order $order): bool
    {
        return $this->getCompany() === $order->getSupplierCompany();
    }
}