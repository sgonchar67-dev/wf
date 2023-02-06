<?php

namespace App\Service\Order\OrderStateMachine;

interface OrderStateInterface extends CustomerOrderStateInterface, SupplierOrderStateInterface
{

}