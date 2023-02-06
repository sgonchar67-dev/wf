<?php

namespace App\Service\Order\OrderStateMachine\State;

use App\Service\Order\OrderStateMachine\OrderActions;

class PlacedState extends State
{
    protected function getPossibleSupplierActions(): array
    {
        return [
            OrderActions::ORDER_REFUND,
            OrderActions::ORDER_CONFIRM,
            OrderActions::ORDER_MARK_AS_SEEN,
        ];
    }

    protected function getPossibleCustomerActions(): array
    {
        return [
            OrderActions::ORDER_REFUND,
        ];
    }
}