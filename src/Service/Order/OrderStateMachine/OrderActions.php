<?php

namespace App\Service\Order\OrderStateMachine;

interface OrderActions
{
    public const ACTOR_SUPPLIER = 'ACTOR_SUPPLIER';
    public const ACTOR_CUSTOMER = 'ACTOR_CUSTOMER';

    public const ORDER_NOTE = 'ORDER_NOTE';

    public const ORDER_CREATE = 'ORDER_CREATE';
    public const ORDER_CUSTOMER_CREATE = 'ORDER_CUSTOMER_CREATE';
    public const ORDER_SUPPLIER_CREATE = 'ORDER_SUPPLIER_CREATE';
    public const ORDER_EDIT = 'ORDER_EDIT';
    /** @var string Привести в соответствие с корзиной */
    public const ORDER_CHECKOUT = 'ORDER_CHECKOUT';
    public const ORDER_SEND = 'ORDER_SEND';
    /** @var string Отклонить */
    public const ORDER_REFUSE = 'ORDER_REFUSE';
    public const ORDER_CONFIRM = 'ORDER_CONFIRM';
    public const ORDER_COMPLETE = 'ORDER_COMPLETE';
    /** @var string Отменить выполнение */
    public const ORDER_CANCEL = 'ORDER_CANCEL';
    public const ORDER_VIEW = 'ORDER_VIEW';
    public const ORDER_MARK_AS_SEEN = 'ORDER_MARK_AS_SEEN';
    public const ORDER_NOTIFICATION = 'ORDER_NOTIFICATION';
    public const ORDER_SHIPMENT = 'ORDER_SHIPMENT';
    public const ORDER_BILLING = 'ORDER_BILLING';
    public const ORDER_PAYMENT = 'ORDER_PAYMENT';
    public const ORDER_REFUND = 'ORDER_REFUND';
    public const ORDER_ARCHIVE = 'ORDER_ARCHIVE';
    public const ORDER_UNARCHIVE = 'ORDER_UNARCHIVE';
    public const ORDER_DELETE = 'ORDER_DELETE';

    public const ACTORS = [
        self::ACTOR_CUSTOMER, self::ACTOR_SUPPLIER, self::ORDER_NOTE
    ];

    public const SUPPLIER_ACTIONS = [
        self::ORDER_CREATE,
        self::ORDER_NOTE,
        self::ORDER_EDIT,
        self::ORDER_SEND,
        self::ORDER_MARK_AS_SEEN,
        self::ORDER_REFUSE,
        self::ORDER_CONFIRM,
        self::ORDER_COMPLETE,
        self::ORDER_CANCEL,
        self::ORDER_VIEW,
        self::ORDER_NOTIFICATION,
        self::ORDER_SHIPMENT,
        self::ORDER_BILLING,
        self::ORDER_PAYMENT,
        self::ORDER_ARCHIVE,
        self::ORDER_DELETE,
    ];

    public const CUSTOMER_ACTIONS = [
        self::ORDER_CREATE,
        self::ORDER_NOTE,
        self::ORDER_CHECKOUT,
        self::ORDER_EDIT,
        self::ORDER_SEND,
        self::ORDER_REFUSE,
        self::ORDER_CANCEL,
        self::ORDER_VIEW,
        self::ORDER_NOTIFICATION,
        self::ORDER_PAYMENT,
        self::ORDER_REFUND,
        self::ORDER_ARCHIVE,
    ];
}