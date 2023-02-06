<?php

namespace App\Controller\Order;

use App\Controller\AbstractController;
use App\Domain\Entity\Order\Order;
use App\Helper\RequestHelper;
use App\Service\Order\OrderDataLog\OrderDataLogService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class EditOrderAction extends AbstractController
{
    public function __construct(private OrderDataLogService $orderDataLogService)
    {
    }

    public function __invoke(Order $data, Request $request): Order
    {
        if ($data->getPlacedAt() && $this->getUser()->getEmployeeCompany() === $data->getSupplierCompany()) {
            $content = RequestHelper::getContent($request);
            if (isset($content['delivery']) || isset($content['deliveryPrice'])) {
                $this->orderDataLogService->logDeliveryChanges($data, $this->getEmployee());
            }
            if (isset($content['payment'])) {
                $this->orderDataLogService->logPaymentChanges($data, $this->getEmployee());
            }
            if (isset($content['orderProducts'])) {
                $this->orderDataLogService->logProductsChanges($data, $this->getEmployee());
            }
        }

        return $data;
    }
}