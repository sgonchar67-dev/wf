<?php

namespace App\Service\Order\OrderStateMachine\State;

use App\Service\Order\OrderStateMachine\OrderActions;

class InProgressState extends State
{

    protected function getPossibleSupplierActions(): array
    {
        return [
            OrderActions::ORDER_NOTIFICATION,
            OrderActions::ORDER_EDIT,
            OrderActions::ORDER_BILLING,
            OrderActions::ORDER_PAYMENT,
            OrderActions::ORDER_REFUND,
            OrderActions::ORDER_SHIPMENT,
            OrderActions::ORDER_COMPLETE,
            OrderActions::ORDER_CANCEL,
        ];
    }

    protected function getPossibleCustomerActions(): array
    {
        return [
            OrderActions::ORDER_CHECKOUT,
            OrderActions::ORDER_NOTIFICATION,
            OrderActions::ORDER_PAYMENT,
            OrderActions::ORDER_REFUND,
        ];
    }
}