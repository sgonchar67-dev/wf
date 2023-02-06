<?php

namespace App\Controller\Order;

use App\Controller\AbstractController;
use App\Domain\Entity\Order\Order;
use App\Handler\Order\NoteOrderHandler;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class NoteOrderAction extends AbstractController
{
    public function __invoke(Order $data, NoteOrderHandler $handler): Order
    {
        $handler->handle($data, $this->getEmployee());
        return $data;
    }
}