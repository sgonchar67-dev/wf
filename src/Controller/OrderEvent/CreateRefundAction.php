<?php

namespace App\Controller\OrderEvent;

use App\Controller\AbstractController;
use App\Domain\Entity\Order\OrderEventLog\OrderEvent\Refund;
use App\Handler\OrderEvent\CreateRefundHandler;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CreateRefundAction extends AbstractController
{
    public function __invoke(Refund $data, CreateRefundHandler $handler): Refund
    {
        $handler->handle($data, $this->getEmployee());
        return $data;
    }
}