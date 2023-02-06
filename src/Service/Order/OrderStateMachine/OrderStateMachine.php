<?php

namespace App\Service\Order\OrderStateMachine;

use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Order\Order;
use App\Service\Order\OrderStateMachine\State\State;

class OrderStateMachine
{
    private Order $order;

    private ?StateFactory $stateFactory;

    private State $state;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->stateFactory = new StateFactory();
        $this->state = $this->stateFactory->create($order);
    }

    public static function create(Order $order): static
    {
        return new self($order);
    }

    public function can(Company $company, string $action): bool
    {
        return $this->state->isPossibleAction($company, $action);
    }
}