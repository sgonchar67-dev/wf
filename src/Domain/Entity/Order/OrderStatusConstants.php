<?php

namespace App\Domain\Entity\Order;

interface OrderStatusConstants
{

    /** @var int черновик */
    public const STATUS_DRAFT_CUSTOMER = 2;
    public const STATUS_DRAFT_SUPPLIER = 3;

    /** @var int отклоненная */
    public const STATUS_REFUSED = 4;
    /** @var int Отправленная (Новая) */
    public const STATUS_PLACED = 5;

    /** @var int Просмотренная (На ознакомлении) */
    public const STATUS_SEEN = 7;

    /** @var int На выполнении */
    public const STATUS_IN_PROGRESS = 9;

    /** @var int Выполнена */
    public const STATUS_DONE = 11;

}