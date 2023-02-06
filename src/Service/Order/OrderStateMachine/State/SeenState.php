<?php

namespace App\Service\Order\OrderStateMachine\State;

use App\Service\Order\OrderStateMachine\OrderActions;

class SeenState extends State
{
    protected function getPossibleSupplierActions(): array
    {
        return [
            OrderActions::ORDER_EDIT,
            OrderActions::ORDER_REFUSE,
            OrderActions::ORDER_CONFIRM,
        ];
    }

    protected function getPossibleCustomerActions(): array
    {
        return [
            OrderActions::ORDER_REFUSE,
        ];
    }
}