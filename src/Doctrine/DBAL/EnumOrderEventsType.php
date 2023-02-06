<?php

namespace App\Doctrine\DBAL;

use App\Domain\Entity\Order\OrderEventLog\OrderEventConstants;

class EnumOrderEventsType extends EnumType
{
    public const NAME = 'EnumOrderEventsType';
    protected string $name = 'EnumOrderEventsType';
    protected array $values = OrderEventConstants::EVENTS;
}
