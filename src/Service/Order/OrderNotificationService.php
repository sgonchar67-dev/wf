<?php

namespace App\Service\Order;

use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Document;
use App\Domain\Entity\Order\Order;
use App\Domain\Entity\Order\OrderEventLog\OrderEventLog;
use App\Domain\Entity\Order\OrderStatusConstants;
use App\Domain\Entity\Order\OrderEventLog\OrderEventConstants;
use App\Exception\NotFoundException;
use App\Repository\Order\RefundRepository;
use App\Repository\Order\PaymentRepository;

class OrderNotificationService
{
    public function __construct(
        private RefundRepository  $invoiceRepository,
        private PaymentRepository $paymentRepository,
    )
    {
    }

    public function giveNotice(OrderEventLog $orderEventLog): void
    {
        $order = $orderEventLog->getOrder();
        $action = $order->getStatus() === OrderStatusConstants::STATUS_PLACED ? 'in_new' : 'update';
        if (!$partnerCompany = $order->getPartnerCompany($orderEventLog->getCompany())) {
            return;
        }

        $data = [
            'user_id'=> $partnerCompany->getUser()->getId(),
            'type' => 'update_deal',
            'id' => $order->getId(),
            'action' => $action,
            'text' => $this->getNoticeByOrderEventLog($orderEventLog),
            'comment' => $orderEventLog->getComment(),
            'files' => $orderEventLog->getDocuments()->map(
                fn(Document $d) => $d->getContentUrl()
            )->toArray(),
        ];

        $this->send($data);
    }

    /**
     * @deprecated
     * @throws NotFoundException
     */
    public function notify(\App\Domain\Entity\Order\Order $order, Company $company): void
    {
        $action = $order->getStatus() === \App\Domain\Entity\Order\Order::STATUS_PLACED ? 'in_new' : 'update';

        $contractorUser = $order->getSupplierCompany() === $company
            ? $order->getCustomerCompany()->getUser()
            : $order->getSupplierCompany()->getUser();

        $event = $order->findLastOrderEventLogByCompany($company);

        $data = [
            'user_id'=> $contractorUser->getId(),
            'type' => 'update_deal',
            'id' => $order->getId(),
            'action' => $action,
            'text' => $this->getNoticeByOrderEventLog($event),
            'comment' => $event?->getComment(),
            'files' => $event?->getDocuments()->map(
                fn(Document $d) => $d->getContentUrl()
            )->toArray(),
        ];

        $this->send($data);
    }

    private function send(array|object $data): void
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,"https://api.workface.ru/api/notifications/send/");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        curl_exec ($ch);
        curl_close ($ch);
    }

    private function getNoticeByOrderEventLog(OrderEventLog $eventLog): ?string
    {
        $companyName = $eventLog->getCompany()->getName();
        $isConsumerActor = $eventLog->isCustomerActor();
        $orderId = $eventLog->getOrder()->getId();

        switch ($eventLog->getEvent()) {
            case OrderEventConstants::EVENT_SEND:

                return "{$companyName} прислал заявку №{$orderId}";
            case OrderEventConstants::EVENT_REFUSE:
                $alias = $isConsumerActor ? 'заказ' : 'заявку';
                return "{$companyName} отклонил {$alias} №{$orderId}";
            case OrderEventConstants::EVENT_CONFIRM:

                return "{$companyName} подтвердил корректировки по заявке №{$orderId}";
//            case OrderEventConstants::EVENT_PERFORM:
//
//                $alias = $isConsumerActor ? 'заказ' : 'заявку';
//                $completed = $isConsumerActor ? 'выполненным' : 'выполненной';
//                return "{$companyName} отметил {$alias} №{$orderId} {$completed}";
            case OrderEventConstants::EVENT_COMPLETE:
                $alias = $isConsumerActor ? 'заказ' : 'заявку';
                return "{$companyName} завершил {$alias} №{$orderId}";
//            case OrderEventConstants::EVENT_CHALLENGE:
//                $alias = $isConsumerActor ? 'заказа' : 'заявки';
//                return "{$companyName} оспорил выполнение {$alias} №{$orderId}";
            case OrderEventConstants::EVENT_CANCEL:

                return "{$companyName} отменил заявку №{$orderId}";
            case OrderEventConstants::EVENT_SEEN:

                return "{$companyName} просмотрел заказ №{$orderId}";
//            case OrderEventConstants::EVENT_CLAIM:
//                $alias = $isConsumerActor ? 'заказу' : 'заявке';
//                return "{$companyName} добавил претензию к {$alias} №{$orderId}";
            case OrderEventConstants::EVENT_NOTIFICATION:
                $alias = $isConsumerActor ? 'заказу' : 'заявке';
                return "{$companyName} добавил уведомление {$alias} №{$orderId}";
            case OrderEventConstants::EVENT_SHIPMENT:

                return "{$companyName} совершил отгрузку по заявке №{$orderId}";
            case OrderEventConstants::EVENT_BILLING:
                $invoice = $this->invoiceRepository->findOneByOrderEventLog($eventLog);
                $sum = " на сумму {$invoice?->getAmount()} рублей";
                return "{$companyName} выставил счет по заказу №{$orderId}{$sum}";
            case OrderEventConstants::EVENT_PAYMENT:
                $result = '';
                if ($payment = $this->paymentRepository->findOneByOrderEventLog($eventLog)) {
                    $sum = " в размере {$payment?->getAmount()} рублей";
                    $result = "{$companyName} подтвердил оплату счета по заявке №{$orderId}{$sum}";
                }
                return $result;
            case OrderEventConstants::EVENT_REFUND:
                $sum = isset($data['sum']) ? " в размере {$data['sum']} рублей" : '';
                return "{$companyName} выполнил возврат средств по заявке №{$orderId}{$sum}";
            default:

                return '';
        }
    }
}
