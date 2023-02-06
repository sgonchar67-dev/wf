<?php

namespace App\Service\Order\OrderStateMachine\State;

use App\Service\Order\OrderStateMachine\OrderActions;

class RefusedState extends State
{
    public function getPossibleActions(): array
    {
        return [
            OrderActions::ORDER_SEND,
            OrderActions::ORDER_DELETE,
        ];
    }

    protected function getPossibleSupplierActions(): array
    {
        return [
            OrderActions::ORDER_SEND,
            OrderActions::ORDER_DELETE,
        ];
    }

    protected function getPossibleCustomerActions(): array
    {
        return [
            OrderActions::ORDER_SEND,
            OrderActions::ORDER_DELETE,
        ];
    }

}