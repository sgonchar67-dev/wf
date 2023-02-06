<?php

namespace App\Domain\Entity\Order\OrderEventLog;

interface OrderEventConstants
{
    public const ACTOR_SUPPLIER = 'ACTOR_SUPPLIER';
    public const ACTOR_CUSTOMER = 'ACTOR_CUSTOMER';

    public const EVENT_CREATE = 'EVENT_CREATE';
    public const EVENT_EDIT = 'EVENT_EDIT';

    public const EVENT_CHECKOUT = 'EVENT_CHECKOUT';
    public const EVENT_SEND = 'EVENT_SEND';
    public const EVENT_REFUSE = 'EVENT_REFUSE';
    public const EVENT_CONFIRM = 'EVENT_CONFIRM';
    public const EVENT_COMPLETE = 'EVENT_COMPLETE';
    public const EVENT_CANCEL = 'EVENT_CANCEL';
    public const EVENT_SEEN = 'EVENT_SEEN';
    public const EVENT_NOTIFICATION = 'EVENT_NOTIFICATION';
    public const EVENT_SHIPMENT = 'EVENT_SHIPMENT';
    public const EVENT_BILLING = 'EVENT_BILLING';
    public const EVENT_PAYMENT = 'EVENT_PAYMENT';
    public const EVENT_REFUND = 'EVENT_REFUND';

    public const EVENTS = [
        self::EVENT_SEND,
        self::EVENT_EDIT,
        self::EVENT_REFUSE,
        self::EVENT_CONFIRM,
        self::EVENT_COMPLETE,
        self::EVENT_CANCEL,
        self::EVENT_SEEN,
        self::EVENT_NOTIFICATION,
        self::EVENT_SHIPMENT,
        self::EVENT_BILLING,
        self::EVENT_PAYMENT,
        self::EVENT_REFUND,
    ];

    public const SUPPLIER_EVENTS = [
        self::EVENT_CREATE,
        self::EVENT_SEND,
        self::EVENT_EDIT,
        self::EVENT_REFUSE,
        self::EVENT_CONFIRM,
        self::EVENT_COMPLETE,
        self::EVENT_CANCEL,
        self::EVENT_SEEN,
        self::EVENT_NOTIFICATION,
        self::EVENT_SHIPMENT,
        self::EVENT_BILLING,
        self::EVENT_PAYMENT,
        self::EVENT_REFUND,
    ];

    public const CUSTOMER_EVENTS = [
        self::EVENT_SEND,
        self::EVENT_REFUSE,
        self::EVENT_CANCEL,
        self::EVENT_SEEN,
        self::EVENT_NOTIFICATION,
        self::EVENT_PAYMENT,
        self::EVENT_REFUND,
    ];
}