<?php

namespace App\Service\Order\OrderStateMachine\State;

use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Order\Order;
use App\Service\Order\OrderStateMachine\OrderActions;
use JetBrains\PhpStorm\Pure;

abstract class State //implements OrderStateInterface
{
    private const COMMON_ORDER_ACTIONS = [OrderActions::ORDER_VIEW, OrderActions::ORDER_NOTE];
    public function __construct(protected Order $order)
    {
    }

    public function isPossibleAction(Company $company, string $action): bool
    {
        $isPossibleAction = false;
        if ($this->isSupplier($company, $this->order)) {
            $possibleCustomerActions = array_merge(self::COMMON_ORDER_ACTIONS, $this->getPossibleSupplierActions());
            $isPossibleAction = in_array($action, $possibleCustomerActions);
        }
        if ($this->isCustomer($company, $this->order)) {
            $possibleCustomerActions = array_merge(self::COMMON_ORDER_ACTIONS, $this->getPossibleCustomerActions());
            $isPossibleAction = in_array($action, $possibleCustomerActions);
        }

        return $isPossibleAction;
    }

    abstract protected function getPossibleSupplierActions(): array;
    abstract protected function getPossibleCustomerActions(): array;

    #[Pure] private function isSupplier(Company $company, Order $order): bool
    {
        return $company === $order->getSupplierCompany();
    }

    #[Pure] private function isCustomer(Company $company, Order $order): bool
    {
        return $company === $order->getCustomerCompany();
    }
}