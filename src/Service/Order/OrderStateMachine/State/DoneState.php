<?php

namespace App\Service\Order\OrderStateMachine\State;

use App\Service\Order\OrderStateMachine\OrderActions;

class DoneState extends State
{

    protected function getPossibleSupplierActions(): array
    {
        return [
            OrderActions::ORDER_ARCHIVE,
            OrderActions::ORDER_CANCEL,
        ];
    }

    protected function getPossibleCustomerActions(): array
    {
        return [
            OrderActions::ORDER_ARCHIVE,
            OrderActions::ORDER_CANCEL,
        ];
    }
}