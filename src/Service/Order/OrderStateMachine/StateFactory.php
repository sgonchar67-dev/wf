<?php

namespace App\Service\Order\OrderStateMachine;

use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Order\Order;
use App\Service\Order\OrderStateMachine\State\DoneState;
use App\Service\Order\OrderStateMachine\State\DraftState;
use App\Service\Order\OrderStateMachine\State\InProgressState;
use App\Service\Order\OrderStateMachine\State\PlacedState;
use App\Service\Order\OrderStateMachine\State\RefusedState;
use App\Service\Order\OrderStateMachine\State\SeenState;
use App\Service\Order\OrderStateMachine\State\ArchiveState;
use App\Service\Order\OrderStateMachine\State\State;

class StateFactory
{

    public function create(Order $order): State
    {
        return match ($order->getStatus()) {
            Order::STATUS_DRAFT_CUSTOMER => new DraftState($order),
            Order::STATUS_DRAFT_SUPPLIER => new DraftState($order),
            Order::STATUS_PLACED => new PlacedState($order),
            Order::STATUS_REFUSED => new RefusedState($order),
            Order::STATUS_SEEN => new SeenState($order),
            Order::STATUS_IN_PROGRESS => new InProgressState($order),
            Order::STATUS_DONE => new DoneState($order),
        };
    }
}