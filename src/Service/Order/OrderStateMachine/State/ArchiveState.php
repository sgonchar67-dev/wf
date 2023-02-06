<?php

namespace App\Service\Order\OrderStateMachine\State;

use App\Service\Order\OrderStateMachine\OrderActions;

class ArchiveState extends State
{
    public function getPossibleActions(): array
    {
        return [
            OrderActions::ORDER_UNARCHIVE,
        ];
    }

    protected function getPossibleSupplierActions(): array
    {
        return [
            OrderActions::ORDER_UNARCHIVE,
        ];
    }

    protected function getPossibleCustomerActions(): array
    {
        return [
            OrderActions::ORDER_UNARCHIVE,
        ];
    }

    public function checkout()
    {
        // TODO: Implement checkout() method.
    }

    public function send()
    {
        // TODO: Implement send() method.
    }

    public function refuse()
    {
        // TODO: Implement refuse() method.
    }

    public function archive()
    {
        // TODO: Implement archive() method.
    }

    public function payment($comment = '', $data = [], $documents = [])
    {
        // TODO: Implement payment() method.
    }

    public function refund($comment = '', $data = [], $documents = [])
    {
        // TODO: Implement refund() method.
    }

    public function notification($comment = '', $data = [], $documents = [])
    {
        // TODO: Implement notification() method.
    }
}