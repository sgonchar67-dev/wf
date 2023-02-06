<?php

namespace App\Service\Order\OrderStateMachine\State;

use App\Service\Order\OrderStateMachine\OrderActions;

class DraftState extends State
{
    protected function getPossibleSupplierActions(): array
    {
        return [
            OrderActions::ORDER_DELETE,
            OrderActions::ORDER_EDIT,
            OrderActions::ORDER_SEND,
        ];
    }

    protected function getPossibleCustomerActions(): array
    {
        return [
            OrderActions::ORDER_CHECKOUT,
            OrderActions::ORDER_EDIT,
            OrderActions::ORDER_SEND,
        ];
    }
}